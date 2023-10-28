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
  <general-top-bar type="dashboard"/>
  <div class="overflow-auto">
    <div class="flex flex-wrap p-1">
      <div v-for="organisation in appStore.userProjects.collection">
        <div class="border-2 rounded-xl w-96 ml-4 mt-4 bg-white dark:bg-gray-900" :style="'border-color:'+organisation.color">
          <div class="p-2 text-white rounded-t-lg flex" :style="'background-color:'+organisation.color">
            <div class="mt-0.5"><i class="pi pi-building"></i></div>
            <div class="ml-2">{{ organisation.name }}</div>
          </div>
          <div>
            <div v-for="project in organisation.projects">
              <div class="flex border-t-[1px] pt-2 pb-2 hover:bg-indigo-100 dark:hover:bg-indigo-900 flew-row cursor-pointer border-gray-300 dark:border-gray-600"
                   @click="gotoProject(organisation,project)">
                <div class="ml-2 basis-2/12">
                  <div class="rounded-full text-white text-xs mt-0.5 py-1 px-2 text-center"
                       :style="'background-color:'+project.color">
                    {{ project.code }}
                  </div>
                </div>
                <div class="ml-2 basis-10/12 font-bold">
                  {{ project.name }}
                </div>
              </div>
            </div>
          </div>
          <div class="pt-2"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import router from "@/router";
import {useAppStore} from "@/stores/app";
import type {Organisation, Project} from "@/api/query/interface/model";
import {useModelStore} from "@/stores/model";
import type {DocProject} from "@/_lodocio/api/query/interface/project";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import GeneralTopBar from "@/_common/topBar/generalTopBar.vue";

const appStore = useAppStore();
const modelStore = useModelStore();
const docProjectStore = useDocProjectStore();

function gotoProject(organisation: Organisation, project: Project) {
  modelStore.resetStore();
  appStore.setCurrentWorkspace(organisation, project);
  docProjectStore.resetStore();
  router.push('/model/o/' + organisation.id + '/p/' + project.id + '/');
}

</script>