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

const breadcrumbs: BreadcrumbItem[] = [
     {
          title: 'Dashboard',
          href: '/dashboard',
     },
];

// const transactions = [
//      {
//           date: '2023-06-25',
//           name: "Tino's Greek Food",
//           amount: 35,
//      },
//      {
//           date: '2023-06-24',
//           name: 'Saltgrass Steak House',
//           amount: 55,
//      },
//      {
//           date: '2023-06-23',
//           name: 'Kyoto Sushi',
//           amount: 60,
//      },
// ];

const transactions = ref([]);
async function loadTransactions() {
     try {
          const res = await fetch('/spend', {
               headers: {
                    Accept: 'application/json',
               },
               credentials: 'include',
          });
          if (!res.ok) throw new Error('Failed to fetch transactions');

          const data = await res.json();
          transactions.value = data;
     } catch (error) {
          console.error('Error loading transactions:', error);
     }
}

onMounted(() => {
     loadTransactions();
});

function formatDate(dateStr) {
     const options = { month: 'numeric', day: 'numeric' };
     return new Date(dateStr).toLocaleDateString(undefined, options);
}

const newTransaction = ref({
     name: '',
     amount: '$0',
     date: new Date().toLocaleDateString(undefined, {
          month: 'numeric',
          day: 'numeric',
     }),
});
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
                              <TableCell>
                                   <input
                                        v-model="newTransaction.date"
                                        placeholder="MM/DD"
                                        class="w-full bg-transparent text-lg outline-none" />
                              </TableCell>
                              <TableCell>
                                   <input
                                        v-model="newTransaction.name"
                                        placeholder="Add transaction..."
                                        class="w-full bg-transparent text-lg outline-none" />
                              </TableCell>
                              <TableCell class="text-right">
                                   <input
                                        v-model.number="newTransaction.amount"
                                        class="w-full bg-transparent text-right text-lg outline-none" />
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
                                   <TableHead>
                                        <XMarkIcon
                                             class="size-4 cursor-pointer transition hover:text-red-500" />
                                   </TableHead>
                                   <TableCell>{{
                                        formatDate(tx.date)
                                   }}</TableCell>
                                   <TableCell>{{ tx.name }}</TableCell>
                                   <TableCell class="text-right">
                                        ${{
                                             parseFloat(tx.amount || 0).toFixed(
                                                  2,
                                             )
                                        }}
                                   </TableCell>
                                   <TableCell>
                                        <CheckIcon class="hidden size-4" />
                                   </TableCell>
                              </TableRow>
                         </TableBody>
                    </Table>
               </div>
          </div>
     </AppLayout>
</template>
