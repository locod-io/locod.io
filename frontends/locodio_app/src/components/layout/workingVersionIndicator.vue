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

  <!-- model indicator -->
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

  <!-- doc indicator -->
  <div id="workingVersionIndicatorForDocumentation"
       class="flex cursor-pointer"
       v-if="docProjectStore.organisation && docProjectStore.docProject"
       @click="gotoDocProject(docProjectStore.organisation,docProjectStore.docProject)">
    <div class="mt-2 mr-2">
      <i class="pi pi-building"></i>
    </div>
    <div class="text-sm mr-2 mt-2">
      {{ docProjectStore.organisation.name }}
    </div>
    <div class="text-sm mr-2 mt-1">
      <div class="rounded-full text-white text-xs mt-0.5 py-1 px-2"
           :style="'background-color:'+docProjectStore.docProject.color">
        {{ docProjectStore.docProject.code }}
      </div>
    </div>
    <div class="text-sm mr-2 mt-2">
      {{ docProjectStore.docProject.name }}
    </div>
  </div>

  <!-- tracker indicator -->


</template>

<script setup lang="ts">
import {useAppStore} from "@/stores/app";
import type {Organisation, Project} from "@/api/query/interface/model";
import router from "@/router";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import type {DocProject} from "@/_lodocio/api/query/interface/project";

const appStore = useAppStore();
const docProjectStore = useDocProjectStore();
const trackerStore = useTrackerStore();

function gotoProject(organisation: Organisation, project: Project) {
  appStore.setCurrentWorkspace(organisation, project);
  router.push('/model/o/' + organisation.id + '/p/' + project.id + '/overview');
}

function gotoDocProject(organisation: Organisation, docProject: DocProject) {
  docProjectStore.setCurrentWorkspace(organisation, docProject);
  router.push('/doc/o/' + organisation.id + '/dp/' + docProject.id + '/roadmap');
}

</script>
