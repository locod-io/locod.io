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
<!--  <div @click="goToDocPart('wiki')"-->
<!--       class="flex gap-2 cursor-pointer py-0.5 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-900">-->
<!--    <div class="no-flex w-4"><i class="pi pi-globe" style="font-size: 0.8rem;"></i></div>-->
<!--    <div class="flex-grow line-clamp-1">Wiki-->
<!--      <span class="font-normal text-xs text-gray-500">[todo]</span></div>-->
<!--  </div>-->
  <div @click="goToDocPart('roadmap')"
       class="flex gap-2 cursor-pointer py-0.5 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-900">
    <div class="no-flex w-4"><i class="pi pi-map" style="font-size: 0.8rem;"></i></div>
    <div class="flex-grow line-clamp-1">Roadmap</div>
  </div>
<!--  <div @click="goToDocPart('releases')"-->
<!--       class="flex gap-2 cursor-pointer py-0.5 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-900">-->
<!--    <div class="no-flex w-4"><i class="pi pi-share-alt" style="font-size: 0.8rem;"></i></div>-->
<!--    <div class="flex-grow line-clamp-1">Releases-->
<!--      <span class="font-normal text-xs text-gray-500">[todo]</span>-->
<!--    </div>-->
<!--  </div>-->

  <div @click="goToDocPart('trackers')"
       class="flex gap-2 cursor-pointer py-0.5 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-900">
    <div class="no-flex w-4"><i class="pi pi-book" style="font-size: 0.8rem;"></i></div>
    <div class="flex-grow line-clamp-1">Trackers</div>
  </div>

  <!-- list trackers -->
  <div v-for="tracker in projectStore.docProject?.trackers">
    <div class="flex gap-2 cursor-pointer py-0.5 px-2 py-1 rounded-lg hover:bg-white dark:hover:bg-gray-900 ml-4 my-2">
      <div @click="goToTrackerPart('document',tracker.id)"
          class="no-flex w-11">
        <span class="rounded-full px-2 text-white font-bold text-xs py-1"
              :style="'background-color:'+tracker.color">
                    {{ tracker.code }}
        </span>
      </div>
      <div @click="goToTrackerPart('document',tracker.id)"
          class="flex-grow line-clamp-1 font-normal">{{ tracker.name }}</div>
      <div @click="goToTrackerPart('configuration',tracker.id)"
          class="flex-none text-gray-300 dark:text-gray-700 hover:text-gray-500 dark:hover:text-gray-500">
        <i class="pi pi-cog" style="font-size: 0.8rem;"></i>
      </div>
    </div>
  </div>

<!--  <div @click="goToDocPart('issues')"-->
<!--       class="flex gap-2 cursor-pointer py-0.5 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-900">-->
<!--    <div class="no-flex w-4"><i class="pi pi-exclamation-circle" style="font-size: 0.8rem;"></i></div>-->
<!--    <div class="flex-grow line-clamp-1">Issues / Bugs-->
<!--      <span class="font-normal text-xs text-gray-500">[GitHub or Linear]</span>-->
<!--    </div>-->
<!--  </div>-->
<!--  <div @click="goToDocPart('documents')"-->
<!--       class="flex gap-2 cursor-pointer py-0.5 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-900">-->
<!--    <div class="no-flex w-4"><i class="pi pi-box" style="font-size: 0.8rem;"></i></div>-->
<!--    <div class="flex-grow line-clamp-1">Documents-->
<!--      <span class="font-normal text-xs text-gray-500">[todo]</span>-->
<!--    </div>-->
<!--  </div>-->

</template>

<script setup lang="ts">
import router from "@/router";
import {useAppStore} from "@/stores/app";
import {useDocProjectStore} from "@/_lodocio/stores/project";

const appStore = useAppStore();
const projectStore = useDocProjectStore();

function goToDocPart(part: string) {
  if (appStore.organisation && appStore.project) {
    router.push('/doc/o/' + appStore.organisation.id + '/p/' + appStore.project.docProject.id + '/' + part);
  }
}

function goToTrackerPart(part: string, trackerId: number) {
  if (appStore.organisation && appStore.project) {
    router.push('/doc/o/' + appStore.organisation.id + '/p/' + appStore.project.docProject.id + '/t/'+trackerId+'/'+part);
  }
}

</script>

<style scoped>

</style>