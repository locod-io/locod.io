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
  <div v-if="appStore.organisation && appStore.project">
    <div class="p-2 border-b-[1px] border-gray-300 h-12  dark:border-gray-600">
      <div id="selectedProjectIndicator" class="flex p-1 gap-2 cursor-pointer" @click="toggleProjectMenu">
        <div class="rounded-full text-white text-xs py-1 px-2"
             :style="'background-color:'+appStore.project.color">
          {{ appStore.project.code }}
        </div>
        <div class="flex-grow line-clamp-1 font-bold">{{ appStore.project.name }}</div>
      </div>
    </div>
    <div class="p-4 border-b-[1px] border-gray-300 h-12 flex  dark:border-gray-600">
      <div class="text-gray-400 text-sm flex-grow">version</div>
      <div class="flex-none text-sm rounded-full bg-indigo-800 text-white px-2 h-5">
        <font-awesome-icon :icon="['fas', 'timeline']"/>
        <span class="font-mono ml-2 font-bold">head</span>
      </div>
    </div>
  </div>
  <div v-else>
    <div class="h-12">&nbsp;</div>
    <div class="h-12">&nbsp;</div>
  </div>

  <!-- menu drop down for a project -------------------------------------------------------------------------------- -->
  <Menu id="project_menu" ref="menuProject" :model="itemsProject" :popup="true"></Menu>

  <!-- -- dialog edit project -------------------------------------------------------------------------------------- -->
  <Dialog
      v-model:visible="isDialogProject"
      header="&nbsp;"
      :modal="true"
  >
    <change-project
        style="width: 400px;"
        :project="appStore.project as UserProject"
        v-on:changed="changed"/>
  </Dialog>

</template>

<script setup lang="ts">
import {useAppStore} from "@/stores/app";
import {ref} from "vue";
import router from "@/router";
import ChangeProject from "@/components/organisation/changeProject.vue";
import type {UserProject} from "@/api/query/interface/user";

const appStore = useAppStore();
const menuProject = ref();
const isDialogProject = ref<boolean>(false);

function editProject() {

  isDialogProject.value = true;
}

async function changed() {
  await appStore.reloadUserProjects();
  if (appStore.organisation && appStore.project) {
    appStore.setCurrentWorkspaceById(appStore.organisation.id, appStore.project.id);
  }
  isDialogProject.value = false;
}

function goToProjectHome() {
  if (appStore.organisation && appStore.project) {
    router.push('/model/o/' + appStore.organisation.id + '/p/' + appStore.project.id + '/');
  }
}

const itemsProject = ref([
  {
    label: 'Switch project',
    icon: 'pi pi-box',
    command: () => {
      gotoRoute('home')
    }
  },
  {
    label: 'Project settings',
    icon: 'pi pi-pencil',
    command: () => {
      if (appStore.project) {
        editProject();
      }
    }
  },
  // {
  //   label: 'Trackers configuration',
  //   icon: 'pi pi-th-large',
  //   command: () => {
  //     console.log('trackers configuration');
  //   }
  // },
  {
    label: 'Model configuration',
    icon: 'pi pi-cog',
    command: () => {
      gotoModelPart('configuration')
    }
  },
]);

function gotoRoute(routeName: string) {
  router.push({name: routeName})
}

function gotoModelPart(routeName: string) {
  if (appStore.organisation && appStore.project) {
    router.push({name: routeName, params: {organisationId: appStore.organisation.id, projectId: appStore.project.id}})
  }
}

const toggleProjectMenu = (event: any) => {
  menuProject.value.toggle(event);
};

</script>

<style scoped>

</style>