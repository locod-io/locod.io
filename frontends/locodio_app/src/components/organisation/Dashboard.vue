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
  <div class="overflow-auto bg-gray-300"
       style="position: absolute;left: 0px;right: 0;top: 49px;bottom: 35px">

    <div class="flex flex-wrap">
      <div v-for="organisation in appStore.userProjects.collection">

        <div class="border-2 rounded-xl w-80 ml-4 mt-4 bg-gray-100" :style="'border-color:'+organisation.color">
          <div class="p-2 text-white rounded-t-lg flex" :style="'background-color:'+organisation.color">
            <div class="mt-0.5"><i class="pi pi-building"></i></div>
            <div class="ml-2">{{ organisation.name }}</div>
          </div>
          <div>
            <div v-for="project in organisation.projects">
              <div @click="gotoProject(organisation,project)"
                   class="flex border-t-[1px] pt-2 pb-2 hover:bg-gray-200 cursor-pointer">
                <div class="ml-2">
                  <div class="rounded-full text-white text-xs mt-0.5 py-1 px-2"
                       :style="'background-color:'+project.color">
                    {{ project.code }}
                  </div>
                </div>
                <div class="ml-2">{{ project.name }}</div>
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

const appStore = useAppStore();
const modelStore = useModelStore();

function gotoProject(organisation: Organisation, project: Project) {
  modelStore.resetStore();
  appStore.setCurrentWorkspace(organisation,project);
  router.push('/model/o/' + organisation.id + '/p/' + project.id + '/overview');
}

</script>