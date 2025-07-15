<script setup lang="ts">
import {
     Table,
     TableBody,
     TableCell,
     TableHead,
     TableHeader,
     TableRow,
} from '@/components/ui/table';
import {
     CheckCircleIcon,
     CheckIcon,
     ChevronLeftIcon,
     ChevronRightIcon,
     TrashIcon,
     XMarkIcon,
     PlusIcon,
     MinusIcon,
} from '@heroicons/vue/24/outline';
import { computed, onMounted, ref, watch } from 'vue';
const csrfToken = document
     .querySelector('meta[name="csrf-token"]')
     ?.getAttribute('content');

// DATE SELECTOR
const now = new Date();
const selectedMonth = ref(now.getMonth()); // 0-indexed (Jan = 0)
const selectedYear = ref(now.getFullYear());

function goToPreviousMonth() {
     if (selectedMonth.value === 0) {
          selectedMonth.value = 11;
          selectedYear.value--;
     } else {
          selectedMonth.value--;
     }
}

function goToNextMonth() {
     if (selectedMonth.value === 11) {
          selectedMonth.value = 0;
          selectedYear.value++;
     } else {
          selectedMonth.value++;
     }
}

// INCOME LIST
type Mod = { type: string; value: string | number; description: string };
type Income = {
    id?: number;
    user_id?: number;
    name: string;
    amount: number | string;
    date: string;
    mods: Mod[];
    editName?: string;
    editAmount?: string;
    rawDate?: string;
    editDate?: string;
    editMods?: Mod[];
    dirty?: boolean;
};
const incomes = ref<Income[]>([]);

// TOTAL EARNED
const monthlyTotal = computed(() =>
     incomes.value.reduce((sum, inc) => {
          return sum + Number(inc.amount || 0);
     }, 0),
);

// PROJECTED EARNINGS
const projectedTotal = computed(() => {
     const today = now.getDate();
     if (
          selectedMonth.value !== now.getMonth() ||
          selectedYear.value !== now.getFullYear()
     ) {
          return monthlyTotal.value;
     }
     if (today === 0) return 0;
     const avgPerDay = monthlyTotal.value / today;
     return Math.round(avgPerDay * 30);
});

// POPULATE INCOME LIST
async function loadIncomes() {
     try {
          const res = await fetch('/income', {
               headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
               },
               credentials: 'include',
          });
          const data = await res.json();
          incomes.value = data
               .filter((inc) => {
                    const incDate = new Date(inc.date);
                    return (
                         incDate.getMonth() === selectedMonth.value &&
                         incDate.getFullYear() === selectedYear.value
                    );
               })
               .map((inc) => ({
                    ...inc,
                    editName: inc.name,
                    editAmount: parseFloat(inc.amount || 0).toFixed(0),
                    rawDate: inc.date,
                    editDate: formatDate(inc.date),
                    editMods: Array.isArray(inc.mods) ? [...inc.mods] : [],
                    dirty: false,
               }));
     } catch (error) {
          console.error('Error loading incomes:', error);
     }
}

onMounted(loadIncomes);
watch([selectedMonth, selectedYear], loadIncomes);

function formatDate(dateStr: string) {
     const [year, month, day] = dateStr.split('-');
     return `${Number(month)}/${Number(day)}`;
}

// NEW INCOME FORM
const newIncome = ref<Income>({
    name: '',
    amount: '$0',
    date: new Date().toLocaleDateString(undefined, {
        month: 'numeric',
        day: 'numeric',
    }),
    mods: [],
});

function parseDateInput(input: string) {
     const [month, day] = input.split('/');
     const year = new Date().getFullYear();
     const date = new Date(year, parseInt(month) - 1, parseInt(day));
     return date.toISOString().slice(0, 10);
}

function addMod(modsArr: Mod[]) {
     modsArr.push({ type: 'raw', value: '', description: '' });
}
function removeMod(modsArr: Mod[], idx: number) {
     modsArr.splice(idx, 1);
}

function parseModString(str: string) {
     // Accepts strings like '* .87', '- 100', '+ 10%'
     str = str.trim();
     if (str.startsWith('*')) {
          return { type: 'multiply', value: parseFloat(str.slice(1)), description: str };
     } else if (str.startsWith('-')) {
          return { type: 'subtract', value: parseFloat(str.slice(1)), description: str };
     } else if (str.startsWith('+')) {
          return { type: 'add', value: parseFloat(str.slice(1)), description: str };
     } else {
          return { type: 'raw', value: str, description: str };
     }
}

function applyMods(base: number | string, mods: string[]) {
     let result = Number(base);
     for (const mod of mods) {
          if (typeof mod === 'string') {
               const parsed = parseModString(mod);
               if (parsed.type === 'multiply') result *= parsed.value;
               else if (parsed.type === 'subtract') result -= parsed.value;
               else if (parsed.type === 'add') result += parsed.value;
          }
     }
     // Round down to nearest 5, no decimals
     return Math.floor(result / 5) * 5;
}

async function submitIncome() {
     const { name, amount, date, mods } = newIncome.value;
     if (!name || !amount || !date) return alert('Please fill out all fields.');
     try {
          const parsedDate = parseDateInput(date);
          const res = await fetch('/income', {
               method: 'POST',
               headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
               },
               credentials: 'include',
               body: JSON.stringify({
                    name: newIncome.value.name || 'Income',
                    amount: Number(newIncome.value.amount) || 0,
                    date: parsedDate || new Date().toISOString().slice(0, 10),
                    mods: newIncome.value.mods,
               }),
          });
          if (!res.ok) {
               const error = await res.json();
               console.error('Validation error:', error);
               throw new Error('Failed to submit');
          }
          const created = await res.json();
          function normalizeIncome(inc) {
               return {
                    ...inc,
                    editName: inc.name,
                    editAmount: parseFloat(inc.amount || 0).toFixed(0),
                    editDate: formatDate(inc.date),
                    editMods: Array.isArray(inc.mods) ? [...inc.mods] : [],
                    dirty: false,
               };
          }
          incomes.value.unshift(normalizeIncome(created));
          newIncome.value = {
               name: '',
               amount: '$0',
               date: new Date().toLocaleDateString(undefined, {
                    month: 'numeric',
                    day: 'numeric',
               }),
               mods: [],
          };
     } catch (err) {
          console.error(err);
          alert('Something went wrong.');
     }
}

async function deleteIncome(id: number) {
     if (!confirm('Are you sure you want to delete this income?')) return;
     try {
          const res = await fetch(`/income/${id}`, {
               method: 'DELETE',
               headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
               },
               credentials: 'include',
          });
          if (!res.ok) throw new Error('Failed to delete');
          incomes.value = incomes.value.filter((i) => i.id !== id);
     } catch (err) {
          console.error('Delete failed:', err);
          alert('Something went wrong while deleting.');
     }
}

async function submitUpdate(inc: Income) {
     try {
          await fetch(`/income/${inc.id}`, {
               method: 'PUT',
               headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
               },
               credentials: 'include',
               body: JSON.stringify({
                    name: inc.editName,
                    amount: Number(inc.editAmount),
                    date: parseDateInput(inc.editDate),
                    mods: inc.editMods,
               }),
          });
          inc.name = inc.editName;
          inc.amount = parseFloat(inc.editAmount).toFixed(0);
          inc.date = inc.editDate;
          inc.mods = [...inc.editMods];
          inc.dirty = false;
     } catch (error) {
          console.error('Update failed:', error);
     }
}
</script>

<template>
     <div class="grid auto-rows-min gap-4">
          <div class="mt-2 grid grid-cols-2 items-end text-lg font-semibold">
               <div class="flex flex-col justify-end gap-2">
                    <div class="flex flex-row gap-2">
                         <div>
                              Total Earned:
                              <span>
                                   ${{
                                        monthlyTotal.toLocaleString(undefined, {
                                             minimumFractionDigits: 0,
                                             maximumFractionDigits: 2,
                                        })
                                   }}
                              </span>
                         </div>
                    </div>
                    <div class="flex flex-row gap-2">
                         <div>
                              Projected Earnings:
                              <span>
                                   ${{
                                        projectedTotal.toLocaleString(
                                             undefined,
                                             {
                                                  minimumFractionDigits: 0,
                                                  maximumFractionDigits: 2,
                                             },
                                        )
                                   }}
                              </span>
                         </div>
                    </div>
               </div>
               <div
                    class="flex h-full flex-row items-center justify-end text-right text-6xl">
                    <ChevronLeftIcon
                         class="inline-block size-8 cursor-pointer align-middle opacity-50 hover:text-gray-500"
                         @click="goToPreviousMonth" />
                    <span class="mx-2">
                         {{
                              new Date(
                                   selectedYear,
                                   selectedMonth,
                              ).toLocaleString('default', {
                                   month: 'short',
                              })
                         }}
                    </span>
                    <ChevronRightIcon
                         class="inline-block size-8 cursor-pointer align-middle opacity-50 hover:text-gray-500"
                         @click="goToNextMonth" />
               </div>
          </div>
          <Table class="text-lg">
               <TableHeader>
                    <TableRow
                         class="border-none bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-900 dark:hover:bg-neutral-800">
                         <TableHead><TrashIcon class="size-4" /></TableHead>
                         <TableHead>Date</TableHead>
                         <TableHead>Name</TableHead>
                         <TableHead class="text-right">Gross</TableHead>
                         <TableHead class="text-right">Net</TableHead>
                         <TableHead>Mods</TableHead>
                         <TableHead><CheckCircleIcon class="size-4" /></TableHead>
                    </TableRow>
               </TableHeader>
               <TableBody>
                    <!-- Add Income -->
                    <TableRow>
                         <TableCell></TableCell>
                         <TableCell class="w-14">
                              <input
                                   v-model="newIncome.date"
                                   class="w-full bg-transparent text-center text-lg outline-none" />
                         </TableCell>
                         <TableCell>
                              <input
                                   v-model="newIncome.name"
                                   placeholder="Add..."
                                   class="w-full bg-transparent text-lg outline-none" />
                         </TableCell>
                         <TableCell class="text-right">
                              <span class="relative left-2 opacity-30">$</span>
                              <input
                                   type="number"
                                   step="5"
                                   class="w-16 bg-transparent text-right focus:outline-none"
                                   placeholder="5"
                                   v-model.number="newIncome.amount" />
                         </TableCell>
                         <TableCell class="text-right">
                              <span class="relative left-6 opacity-30">$</span>
                              <span class="inline-block w-16 font-mono">
                                  {{ isNaN(Number(newIncome.amount)) ? '5' : applyMods(newIncome.amount, newIncome.mods.map(m => m.description)) }}
                              </span>
                         </TableCell>
                         <TableCell>
                              <div class="flex flex-col gap-1">
                                   <div v-for="(mod, idx) in newIncome.mods" :key="idx" class="flex items-center gap-1">
                                        <input
                                             v-model="newIncome.mods[idx].description"
                                             placeholder="e.g. * .87, - 100"
                                             class="w-24 bg-transparent text-sm outline-none border-b border-gray-300" />
                                        <XMarkIcon class="size-4 cursor-pointer text-gray-400 transition hover:text-red-500" @click="removeMod(newIncome.mods, idx)" />
                                   </div>
                                   <button @click="addMod(newIncome.mods)" class="text-xs text-gray-400 hover:text-blue-600 flex items-center gap-1 cursor-pointer"><PlusIcon class="size-4" /> Add Mod</button>
                              </div>
                         </TableCell>
                         <TableCell>
                              <button
                                   @click="submitIncome"
                                   class="cursor-pointer">
                                   <CheckIcon
                                        class="size-4 transition hover:text-green-600" />
                              </button>
                         </TableCell>
                    </TableRow>

                    <!-- List Incomes -->
                    <TableRow
                         v-for="inc in incomes"
                         :key="inc.id">
                         <!-- Delete Button -->
                         <TableCell>
                              <XMarkIcon
                                   class="size-4 cursor-pointer transition hover:text-red-500"
                                   @click="deleteIncome(inc.id)" />
                         </TableCell>
                         <!-- Editable Date -->
                         <TableCell class="text-center">
                              <input
                                   type="text"
                                   class="w-16 bg-transparent text-center focus:outline-none"
                                   v-model="inc.editDate"
                                   @input="inc.dirty = true" />
                         </TableCell>
                         <!-- Editable Name -->
                         <TableCell class="max-w-[145px] overflow-hidden">
                              <input
                                   type="text"
                                   class="w-full truncate bg-transparent focus:outline-none"
                                   v-model="inc.editName"
                                   @input="inc.dirty = true" />
                         </TableCell>
                         <!-- Editable Base Amount -->
                         <TableCell class="text-right">
                              <span class="relative left-2 opacity-30">$</span>
                              <input
                                   type="number"
                                   step="5"
                                   class="w-16 bg-transparent text-right focus:outline-none"
                                   v-model.number="inc.editAmount"
                                   @input="inc.dirty = true" />
                         </TableCell>
                         <!-- Final Amount (after mods) -->
                         <TableCell class="text-right">
                              <span class="relative left-6 opacity-30">$</span>
                              <span class="inline-block w-16 font-mono">
                                  {{ applyMods(inc.editAmount, inc.editMods.map(m => m.description)) }}
                              </span>
                         </TableCell>
                         <!-- Mods -->
                         <TableCell>
                              <div class="flex flex-col gap-1">
                                   <div v-for="(mod, idx) in inc.editMods" :key="idx" class="flex items-center gap-1">
                                        <input
                                             v-model="inc.editMods[idx].description"
                                             placeholder="e.g. * .87, - 100"
                                             class="w-24 bg-transparent text-sm outline-none border-b border-gray-300" @input="inc.dirty = true" />
                                        <XMarkIcon class="size-4 cursor-pointer text-gray-400 transition hover:text-red-500" @click="removeMod(inc.editMods, idx)" />
                                   </div>
                                   <button @click="addMod(inc.editMods)" class="text-xs text-gray-400 hover:text-blue-600 flex items-center gap-1 cursor-pointer"><PlusIcon class="size-4" /> Add Mod</button>
                              </div>
                         </TableCell>
                         <!-- Confirm Changes -->
                         <TableCell>
                              <CheckIcon
                                   v-if="inc.dirty"
                                   class="size-4 cursor-pointer text-green-600 transition"
                                   @click="submitUpdate(inc)" />
                         </TableCell>
                    </TableRow>
               </TableBody>
          </Table>
     </div>
</template>
