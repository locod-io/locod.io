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
      <!-- master templates -->
      <div v-if="type=='masterTemplates'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-file"></i></div>
        <div class="flex-none correction">User -> My Master Templates</div>
      </div>
      <!-- configuration -->
      <div v-if="type=='configuration'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-cog"></i></div>
        <div class="flex-none correction">Model -> Configuration</div>
      </div>
      <!-- overview -->
      <div v-if="type=='overview'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-sitemap"></i></div>
        <div class="flex-none correction">Model -> Overview</div>
      </div>
      <!-- documentation -->
      <div v-if="type=='documentation'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-map"></i></div>
        <div class="flex-none correction">Model -> Documentation</div>
      </div>
      <!-- template -->
      <div v-if="type=='template'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-code"></i></div>
        <div class="flex-none correction">Model -> Templates</div>
      </div>
      <!-- module -->
      <div v-if="type=='module'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-box"></i></div>
        <div class="flex-none correction">Model -> Module</div>
      </div>
      <!-- domain-model -->
      <div v-if="type=='domain-model'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-database"></i></div>
        <div class="flex-none correction">Model -> Domain Model</div>
      </div>
      <!-- enum -->
      <div v-if="type=='enum'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-align-justify"></i></div>
        <div class="flex-none correction">Model -> Enum</div>
      </div>
      <!-- query -->
      <div v-if="type=='query'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-upload"></i></div>
        <div class="flex-none correction">Model -> Query</div>
      </div>
      <!-- command -->
      <div v-if="type=='command'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-download"></i></div>
        <div class="flex-none correction">Model -> Command</div>
      </div>

      <!-- project settings -->
      <div v-if="type=='settings'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-cog"></i></div>
        <div class="flex-none correction">Project -> Settings</div>
      </div>

      <!-- organisation user management -->
      <div v-if="type=='users'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-users"></i></div>
        <div class="flex-none correction">Usermanagement</div>
      </div>

      <!-- filler -->
      <div class="flex-grow border-b-[1px] border-gray-300 bg-gray-200 dark:bg-gray-900 dark:border-gray-600">
        <div :style="'margin-top:-3px;margin-right:-1px;background-color:'+appStore.project?.color" class="h-3"></div>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import {useAppStore} from "@/stores/app";

const props = defineProps<{
  type: 'masterTemplates'
      | 'configuration'
      | 'documentation'
      | 'overview'
      | 'template'
      | 'domain-model'
      | 'enum'
      | 'query'
      | 'command'
      | 'module'
      | 'settings'
      | 'users'
}>();

const appStore = useAppStore();

</script>

<style scoped>
.correction {
  margin-top:-0.1rem;
}
</style>