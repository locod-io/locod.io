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

      <!-- wiki -->
      <div v-if="type=='wiki'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-globe"></i></div>
        <div class="flex-none correction">Documentation -> Wiki</div>
      </div>

      <!-- wiki configuration -->
      <div v-if="type=='wiki-configuration'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-wrech"></i></div>
        <div class="flex-none correction">Documentation -> Wiki Configuration</div>
      </div>

      <!-- roadmap -->
      <div v-if="type=='roadmap'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-map"></i></div>
        <div class="flex-none correction">Documentation -> Roadmap</div>
      </div>

      <!-- platform roadmap -->
      <div v-if="type=='organisation-roadmap'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-map"></i></div>
        <div class="flex-none correction">
          {{appStore.user.organisationLabel.charAt(0).toUpperCase()}}{{appStore.user.organisationLabel.slice(1)}}
          -> Roadmap
        </div>
      </div>

      <!-- releases -->
      <div v-if="type=='releases'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-share-alt"></i></div>
        <div class="flex-none correction">Documentation -> Releases</div>
      </div>

      <!-- trackers -->
      <div v-if="type=='trackers'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-book"></i></div>
        <div class="flex-none correction">Documentation -> Trackers</div>
      </div>

      <!-- issues -->
      <div v-if="type=='issues'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-exclamation-circle"></i></div>
        <div class="flex-none correction">Documentation -> Issues</div>
      </div>

      <!-- documents -->
      <div v-if="type=='documents'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-box"></i></div>
        <div class="flex-none correction">Documentation -> Documents</div>
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
  type: 'wiki'
      | 'wiki-configuration'
      | 'roadmap'
      | 'releases'
      | 'trackers'
      | 'issues'
      | 'documents'
      | 'organisation-roadmap',
}>();

const appStore = useAppStore();

</script>

<style scoped>
.correction {
  margin-top: -0.1rem;
}
</style>