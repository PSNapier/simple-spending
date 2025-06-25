<script setup lang="ts">
import {
     Table,
     TableBody,
     TableCell,
     TableHead,
     TableHeader,
     TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import {
     CheckCircleIcon,
     CheckIcon,
     TrashIcon,
     XMarkIcon,
} from '@heroicons/vue/24/outline';
import { onMounted, ref } from 'vue';
const csrfToken = document
     .querySelector('meta[name="csrf-token"]')
     ?.getAttribute('content');

const breadcrumbs: BreadcrumbItem[] = [
     {
          title: 'Dashboard',
          href: '/dashboard',
     },
];

// POPULATE TRANSACTION LIST
const transactions = ref([]);

onMounted(async () => {
     try {
          const res = await fetch('/spend', {
               headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
               },
               credentials: 'include',
          });

          const data = await res.json();

          transactions.value = data.map((tx) => ({
               ...tx,
               editName: tx.name,
               editAmount: parseFloat(tx.amount || 0).toFixed(0),
               editDate: formatDate(tx.date), // e.g. MM/DD
               dirty: false,
          }));
     } catch (error) {
          console.error('Error loading transactions:', error);
     }
});

function formatDate(dateStr) {
     const [year, month, day] = dateStr.split('-');
     return `${Number(month)}/${Number(day)}`;
}

// NEW TRANSACTION FORM
const newTransaction = ref({
     name: '',
     amount: '$0',
     date: new Date().toLocaleDateString(undefined, {
          month: 'numeric',
          day: 'numeric',
     }),
});
function parseDateInput(input) {
     const [month, day] = input.split('/');
     const year = new Date().getFullYear();
     const date = new Date(year, parseInt(month) - 1, parseInt(day));
     return date.toISOString().slice(0, 10); // format: YYYY-MM-DD
}
async function submitTransaction() {
     const { name, amount, date } = newTransaction.value;

     if (!name || !amount || !date) return alert('Please fill out all fields.');

     try {
          const parsedDate = parseDateInput(date);

          const res = await fetch('/spend', {
               method: 'POST',
               headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
               },
               credentials: 'include',
               body: JSON.stringify({
                    name: newTransaction.value.name || 'Transaction',
                    amount: Number(newTransaction.value.amount) || 0,
                    date: parsedDate || new Date().toISOString().slice(0, 10),
               }),
          });

          // if (!res.ok) throw new Error('Failed to submit');
          if (!res.ok) {
               const error = await res.json();
               console.error('Validation error:', error);
               throw new Error('Failed to submit');
          }

          const created = await res.json();

          function normalizeTransaction(tx) {
               return {
                    ...tx,
                    editName: tx.name,
                    editAmount: parseFloat(tx.amount || 0).toFixed(0),
                    editDate: formatDate(tx.date), // Make sure this returns MM/DD format
                    dirty: false,
               };
          }
          transactions.value.unshift(normalizeTransaction(created));

          newTransaction.value = {
               name: '',
               amount: '$0',
               date: new Date().toLocaleDateString(undefined, {
                    month: 'numeric',
                    day: 'numeric',
               }),
          };
     } catch (err) {
          console.error(err);
          alert('Something went wrong.');
     }
}

// DELETE TRANSACTION
async function deleteTransaction(id) {
     if (!confirm('Are you sure you want to delete this transaction?')) return;

     try {
          const res = await fetch(`/spend/${id}`, {
               method: 'DELETE',
               headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
               },
               credentials: 'include',
          });

          if (!res.ok) throw new Error('Failed to delete');

          // Remove from local list
          transactions.value = transactions.value.filter((t) => t.id !== id);
     } catch (err) {
          console.error('Delete failed:', err);
          alert('Something went wrong while deleting.');
     }
}

// UPDATE TRANSACTION
async function submitUpdate(tx) {
     try {
          await fetch(`/spend/${tx.id}`, {
               method: 'PUT',
               headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
               },
               credentials: 'include',
               body: JSON.stringify({
                    name: tx.editName,
                    amount: Number(tx.editAmount),
                    date: parseDateInput(tx.editDate),
               }),
          });

          // Update original fields
          tx.name = tx.editName;
          tx.amount = parseFloat(tx.editAmount).toFixed(0);
          tx.date = tx.editDate; // parse if you're using ISO

          tx.dirty = false;
     } catch (error) {
          console.error('Update failed:', error);
     }
}
</script>

<template>
     <AppLayout :breadcrumbs="breadcrumbs">
          <div
               class="m-auto flex h-full w-full flex-1 flex-col gap-4 p-4 md:max-w-[500px]">
               <div class="grid auto-rows-min gap-4">
                    <div class="mt-2 grid grid-cols-2 text-xl font-semibold">
                         <div>
                              <div class="flex flex-row gap-2">
                                   <div>Total Spent:</div>
                                   <div>$35</div>
                              </div>
                              <div class="flex flex-row gap-2">
                                   <div>Projected Total:</div>
                                   <div>$35</div>
                              </div>
                         </div>
                         <div
                              class="flex h-full flex-col justify-end text-right text-6xl">
                              {{
                                   new Date().toLocaleString('default', {
                                        month: 'long',
                                   })
                              }}
                         </div>
                    </div>
                    <Table class="text-lg">
                         <TableHeader>
                              <TableRow>
                                   <TableHead
                                        ><TrashIcon class="size-4"
                                   /></TableHead>
                                   <TableHead>Date</TableHead>
                                   <TableHead>Name</TableHead>
                                   <TableHead class="text-right">
                                        Amount
                                   </TableHead>
                                   <TableHead
                                        ><CheckCircleIcon class="size-4"
                                   /></TableHead>
                              </TableRow>
                         </TableHeader>
                         <TableBody>
                              <!-- Add Transaction -->
                              <TableCell></TableCell>
                              <TableCell class="w-14">
                                   <input
                                        v-model="newTransaction.date"
                                        class="w-full bg-transparent text-center text-lg outline-none" />
                              </TableCell>
                              <TableCell>
                                   <input
                                        v-model="newTransaction.name"
                                        placeholder="Add..."
                                        class="w-full bg-transparent text-lg outline-none" />
                              </TableCell>
                              <!-- Editable Amount -->
                              <TableCell class="text-right">
                                   <span class="relative left-2 opacity-30"
                                        >$</span
                                   >
                                   <input
                                        type="number"
                                        step="5"
                                        class="w-16 bg-transparent text-right focus:outline-none"
                                        v-model.number="
                                             newTransaction.amount
                                        " />
                              </TableCell>
                              <TableCell>
                                   <button
                                        @click="submitTransaction"
                                        class="cursor-pointer">
                                        <CheckIcon
                                             class="size-4 transition hover:text-green-600" />
                                   </button>
                              </TableCell>

                              <!-- List Transactions -->
                              <TableRow
                                   v-for="tx in transactions"
                                   :key="tx.id">
                                   <!-- Delete Button -->
                                   <TableCell>
                                        <XMarkIcon
                                             class="size-4 cursor-pointer transition hover:text-red-500"
                                             @click="
                                                  deleteTransaction(tx.id)
                                             " />
                                   </TableCell>

                                   <!-- Editable Date -->
                                   <TableCell class="text-center">
                                        <input
                                             type="text"
                                             class="w-16 bg-transparent text-center focus:outline-none"
                                             v-model="tx.editDate"
                                             @input="tx.dirty = true" />
                                   </TableCell>

                                   <!-- Editable Name -->
                                   <TableCell
                                        class="max-w-[145px] overflow-hidden">
                                        <input
                                             type="text"
                                             class="w-full truncate bg-transparent focus:outline-none"
                                             v-model="tx.editName"
                                             @input="tx.dirty = true" />
                                   </TableCell>

                                   <!-- Editable Amount -->
                                   <TableCell class="text-right">
                                        <span class="relative left-2 opacity-30"
                                             >$</span
                                        >
                                        <input
                                             type="number"
                                             step="5"
                                             class="w-16 bg-transparent text-right focus:outline-none"
                                             v-model.number="tx.editAmount"
                                             @input="tx.dirty = true" />
                                   </TableCell>

                                   <!-- Confirm Changes -->
                                   <TableCell>
                                        <CheckIcon
                                             v-if="tx.dirty"
                                             class="size-4 cursor-pointer text-green-600 transition"
                                             @click="submitUpdate(tx)" />
                                   </TableCell>
                              </TableRow>
                         </TableBody>
                    </Table>
               </div>
          </div>
     </AppLayout>
</template>
