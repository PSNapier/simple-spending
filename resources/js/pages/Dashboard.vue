<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { computed, ref } from 'vue';
import IncomeView from '../components/custom/IncomeView.vue';
import SpendingView from '../components/custom/SpendingView.vue';
import WishlistView from '../components/custom/WishlistView.vue';

const breadcrumbs: BreadcrumbItem[] = [
     {
          title: 'Dashboard',
          href: '/dashboard',
     },
];

const views = [
     { name: 'Spending', component: SpendingView },
     { name: 'Income', component: IncomeView },
     { name: 'Wishlist', component: WishlistView },
];
const currentViewIndex = ref(0);
const currentView = computed(() => views[currentViewIndex.value]);

// function nextView() {
//      currentViewIndex.value = (currentViewIndex.value + 1) % views.length;
// }

// function prevView() {
//      currentViewIndex.value =
//           (currentViewIndex.value - 1 + views.length) % views.length;
// }
</script>

<template>
     <AppLayout :breadcrumbs="breadcrumbs">
          <nav class="mt-4 flex justify-center gap-4 text-lg md:text-base">
               <button
                    v-for="(view, i) in views"
                    :key="view.name"
                    @click="currentViewIndex = i"
                    class="transition-all"
                    :class="[
                         i === currentViewIndex
                              ? 'scale-110 cursor-pointer text-black'
                              : 'cursor-pointer text-gray-400 hover:text-black',
                    ]">
                    {{ view.name }}
               </button>
          </nav>

          <!-- <button
               @click="prevView"
               class="absolute top-1/2 left-2 z-10 -translate-y-1/2">
               <ChevronLeftIcon class="size-8 text-gray-500 hover:text-black" />
          </button> -->

          <component
               :is="currentView.component"
               :key="currentView.name"
               class="m-auto flex w-full flex-1 flex-col gap-4 p-4 pt-0 md:max-w-[500px]" />

          <!-- <button
               @click="nextView"
               class="absolute top-1/2 right-2 z-10 -translate-y-1/2">
               <ChevronRightIcon
                    class="size-8 text-gray-500 hover:text-black" />
          </button> -->
     </AppLayout>
     <footer class="w-full py-4 text-center text-sm text-gray-400">
          &copy; Abature Studio {{ new Date().getFullYear() }}
     </footer>
</template>
