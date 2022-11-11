<!--
/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
-->

<template>
  <div id="workingVersionIndicator"
       class="flex cursor-pointer"
       v-if="appStore.organisation && appStore.project"
       @click="gotoProject(appStore.organisation,appStore.project)">
    <div class="mt-2 mr-2">
      <i class="pi pi-building"></i>
    </div>
    <div class="text-sm mr-2 mt-2">
      {{ appStore.organisation.name }}
    </div>
    <div class="text-sm mr-2 mt-1">
      <div class="rounded-full text-white text-xs mt-0.5 py-1 px-2" :style="'background-color:'+appStore.project.color">
        {{ appStore.project.code }}
      </div>
    </div>
    <div class="text-sm mr-2 mt-2">
      {{ appStore.project.name }}
    </div>
  </div>

</template>

<script setup lang="ts">
import {useAppStore} from "@/stores/app";
import type {Organisation, Project} from "@/api/query/interface/model";
import router from "@/router";

const appStore = useAppStore();

function gotoProject(organisation: Organisation, project: Project) {
  appStore.setCurrentWorkspace(organisation, project);
  router.push('/model/o/' + organisation.id + '/p/' + project.id + '/overview');
}

</script>
