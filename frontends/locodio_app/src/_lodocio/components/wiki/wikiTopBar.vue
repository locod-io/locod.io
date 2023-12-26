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
      <div v-if="type=='wiki-document'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-th-large"></i></div>
        <div class="flex-none correction">
          Wiki ->
        </div>
        <div>
          <div v-if="wikiStore.wikiId === 0">
            <i class="pi pi-spin pi-spinner"></i>
          </div>
          <div v-else class="correction">
             <span class="rounded-full px-2 text-white font-bold text-xs py-1"
                   :style="'background-color:'+wikiStore.wiki.color">
                    {{ wikiStore.wiki.code }}
              </span>
              {{wikiStore.wiki.name}}
          </div>
        </div>
      </div>

      <!-- wiki -->
      <div v-if="type=='wiki-configuration'" class="p-4 flex gap-2 text-sm border-r-[1px] border-gray-300 dark:border-gray-600">
        <div class="flex-none"><i class="pi pi-cog"></i></div>
        <div class="flex-none correction">
          Wiki &gt;
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
import {useWikiStore} from "@/_lodocio/stores/wiki";

const props = defineProps<{
  type: 'wiki-document' | 'wiki-configuration' ,
}>();

const wikiStore = useWikiStore();

</script>

<style scoped>
.correction {
  margin-top: -0.1rem;
}
</style>