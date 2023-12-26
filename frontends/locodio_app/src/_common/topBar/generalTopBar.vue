<!--
/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
-->

<template>
  <div class="h-12 border-b-[1px] border-gray-300 w-full dark:border-gray-600">
    <div class="flex h-12 w-full">
      <!-- dashboard -->
      <div v-if="type=='dashboard'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-building"></i></div>
        <div class="flex-none correction">Dashboard</div>
      </div>
      <!-- my-organisations -->
      <div v-if="type=='my-organisations'"
           class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-building"></i></div>
        <div class="flex-none correction">My
          {{ appStore.user.organisationLabel.charAt(0).toUpperCase() + appStore.user.organisationLabel.slice(1) }}s
        </div>
      </div>
      <!-- my profile -->
      <div v-if="type=='my-profile'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-user"></i></div>
        <div class="flex-none correction">My Profile</div>
      </div>
      <!-- change password -->
      <div v-if="type=='change-password'"
           class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-lock"></i></div>
        <div class="flex-none correction">Change My Password</div>
      </div>
      <!-- about -->
      <div v-if="type=='about'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-question-circle"></i></div>
        <div class="flex-none correction">About this application</div>
      </div>

      <!-- my roadmap -->
      <div v-if="type=='my-roadmap'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-map"></i></div>
        <div class="flex-none correction">My Roadmap</div>
      </div>

      <!-- filler -->
      <div class="flex-grow border-b-[1px] border-gray-300 bg-gray-200 dark:bg-gray-900 dark:border-gray-600">
        <div style="margin-top:-3px;margin-right:-1px;" :style="'background:'+themeColor+';'" class="h-3"></div>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">

import {useAppStore} from "@/stores/app";
import {onMounted, ref} from "vue";

const props = defineProps<{
  type: 'my-organisations' | 'dashboard' | 'my-profile' | 'change-password' | 'about' | 'my-roadmap',
}>();

const appStore = useAppStore();
const themeColor = ref<string>('');

onMounted((): void => {
  themeColor.value = theme_color;
});

</script>

<style scoped>
.correction {
  margin-top: -0.1rem;
}
</style>