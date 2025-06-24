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

const breadcrumbs: BreadcrumbItem[] = [
     {
          title: 'Dashboard',
          href: '/dashboard',
     },
];
</script>

<template>
     <!-- <Head title="Dashboard" /> -->

     <AppLayout :breadcrumbs="breadcrumbs">
          <div
               class="m-auto flex h-full w-full flex-1 flex-col gap-4 p-4 md:max-w-[500px]">
               <div class="grid auto-rows-min gap-4">
                    <div class="grid grid-cols-2 text-xl font-semibold">
                         <div>
                              <div class="flex flex-row gap-2">
                                   <div>Toal Spent:</div>
                                   <div>$35</div>
                              </div>
                              <div class="flex flex-row gap-2">
                                   <div>Projected Total:</div>
                                   <div>$35</div>
                              </div>
                         </div>
                         <div class="text-right text-5xl">
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
                                   <TableHead>Date</TableHead>
                                   <TableHead>Name</TableHead>
                                   <TableHead class="text-right">
                                        Amount
                                   </TableHead>
                              </TableRow>
                         </TableHeader>
                         <TableBody>
                              <TableRow>
                                   <TableCell>6/25</TableCell>
                                   <TableCell>Tino's Greek Food</TableCell>
                                   <TableCell class="text-right">
                                        $35
                                   </TableCell>
                              </TableRow>
                         </TableBody>
                    </Table>
               </div>
          </div>
     </AppLayout>
</template>
