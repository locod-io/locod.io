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
  <div class="mt-2 flex gap-2 cursor-pointer py-0.5 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-900"
       @click="goToModelPart('module')">
    <div class="no-flex w-4"><i class="pi pi-box" style="font-size: 0.8rem;"></i></div>
    <div class="flex-grow line-clamp-1">Module</div>
  </div>
  <div class="flex gap-2 cursor-pointer py-0.5 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-900"
       @click="goToModelPart('domain-model')">
    <div class="no-flex w-4"><i class="pi pi-database" style="font-size: 0.8rem;"></i></div>
    <div class="flex-grow line-clamp-1">Domain Model</div>
  </div>
  <div class="flex gap-2 cursor-pointer py-0.5 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-900"
       @click="goToModelPart('enum')">
    <div class="no-flex w-4"><i class="pi pi-align-justify" style="font-size: 0.8rem;"></i></div>
    <div class="flex-grow line-clamp-1">Enum</div>
  </div>
  <div class="flex gap-2 cursor-pointer py-0.5 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-900"
       @click="goToModelPart('query')">
    <div class="no-flex w-4"><i class="pi pi-upload" style="font-size: 0.8rem;"></i></div>
    <div class="flex-grow line-clamp-1">Query</div>
  </div>
  <div class="flex gap-2 cursor-pointer py-0.5 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-900"
       @click="goToModelPart('command')">
    <div class="no-flex w-4"><i class="pi pi-download" style="font-size: 0.8rem;"></i></div>
    <div class="flex-grow line-clamp-1">Command</div>
  </div>

  <div class="mt-2 flex gap-2 cursor-pointer py-0.5 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-900"
       @click="goToModelPart('overview')">
    <div class="no-flex w-4"><i class="pi pi-sitemap" style="font-size: 0.8rem;"></i></div>
    <div class="flex-grow line-clamp-1">Overview</div>
  </div>
  <div class="flex gap-2 cursor-pointer py-0.5 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-900"
       @click="goToModelPart('documentation')">
    <div class="no-flex w-4"><i class="pi pi-map" style="font-size: 0.8rem;"></i></div>
    <div class="flex-grow line-clamp-1">Documentation</div>
  </div>
  <div class="flex gap-2 cursor-pointer py-0.5 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-900"
       @click="goToModelPart('template')">
    <div class="no-flex w-4"><i class="pi pi-code" style="font-size: 0.8rem;"></i></div>
    <div class="flex-grow line-clamp-1">Templates</div>
  </div>
</template>

<script setup lang="ts">
import router from "@/router";
import {useAppStore} from "@/stores/app";

const appStore = useAppStore();

function goToModelPart(part: string) {
  if (appStore.organisation && appStore.project) {
    router.push('/model/o/' + appStore.organisation.id + '/p/' + appStore.project.id + '/' + part);
  }
}

</script>

<style scoped>

</style>