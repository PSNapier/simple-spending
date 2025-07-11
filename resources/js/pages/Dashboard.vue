<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';
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

function getViewComponent(view: string) {
     switch (view) {
          case 'spending':
               return SpendingView;
          case 'income':
               return IncomeView;
          case 'wishlist':
               return WishlistView;
          default:
               return SpendingView;
     }
}

const views = ['spending', 'income', 'wishlist'];
const currentViewIndex = ref(0);

const currentView = computed(() => views[currentViewIndex.value]);

const currentComponent = computed(() => {
     switch (currentView.value) {
          case 'income':
               return IncomeView;
          case 'wishlist':
               return WishlistView;
          default:
               return SpendingView;
     }
});

function nextView() {
     currentViewIndex.value = (currentViewIndex.value + 1) % views.length;
}

function prevView() {
     currentViewIndex.value =
          (currentViewIndex.value - 1 + views.length) % views.length;
}
</script>

<template>
     <AppLayout :breadcrumbs="breadcrumbs">
          <div class="">
               <button
                    @click="prevView"
                    class="absolute top-1/2 left-2 z-10 -translate-y-1/2">
                    <ChevronLeftIcon
                         class="size-8 text-gray-500 hover:text-black" />
               </button>

               <div
                    class="transition-all duration-300"
                    :key="currentView">
                    <component :is="currentComponent" />
               </div>

               <button
                    @click="nextView"
                    class="absolute top-1/2 right-2 z-10 -translate-y-1/2">
                    <ChevronRightIcon
                         class="size-8 text-gray-500 hover:text-black" />
               </button>
          </div>
     </AppLayout>
     <footer class="w-full py-4 text-center text-sm text-gray-400">
          &copy; Abature Studio {{ new Date().getFullYear() }}
     </footer>
</template>
