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
      <div v-if="type=='tracker-document'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-th-large"></i></div>
        <div class="flex-none correction">
          Tracker ->
        </div>
        <div>
          <div v-if="trackerStore.trackerId === 0">
            <i class="pi pi-spin pi-spinner"></i>
          </div>
          <div v-else class="correction">
             <span class="rounded-full px-2 text-white font-bold text-xs py-1"
                   :style="'background-color:'+trackerStore.tracker.color">
                    {{ trackerStore.tracker.code }}
              </span>
              {{trackerStore.tracker.name}}
          </div>
        </div>
      </div>

      <!-- wiki -->
      <div v-if="type=='tracker-configuration'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-cog"></i></div>
        <div class="flex-none correction">
          Tracker &gt;
        </div>
      </div>

      <!-- filler -->
      <div class="flex-grow border-b-[1px] border-gray-300 bg-gray-200 dark:bg-gray-900 dark:border-gray-600">
        <div style="margin-top:-3px;margin-right:-1px;"
             class="bg-gradient-to-r from-black from-1% via-indigo-900 via-30% to-green-700 h-3"></div>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import {useTrackerStore} from "@/_lodocio/stores/tracker";

const props = defineProps<{
  type: 'tracker-document' | 'tracker-configuration' ,
}>();

const trackerStore = useTrackerStore();

</script>

<style scoped>
.correction {
  margin-top: -0.1rem;
}
</style>