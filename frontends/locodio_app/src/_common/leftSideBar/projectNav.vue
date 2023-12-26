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
  <div v-if="appStore.organisation && appStore.project">
    <div class="p-2 border-b-[1px] border-gray-300 h-12  dark:border-gray-600">
      <div id="selectedProjectIndicator" class="flex p-1 gap-2 cursor-pointer">
        <div class="rounded-full text-white text-xs py-1 px-2" @click="toggleProjectMenu"
             :style="'background-color:'+appStore.project.color">
          {{ appStore.project.code }}
        </div>
        <div class="flex-grow line-clamp-1 font-bold" @click="toggleProjectMenu">
          {{ appStore.project.name }}
        </div>

        <div v-if="isOrganisationAdmin"
             @click="toggleProjectMenu"
             class="flex-none text-gray-300 dark:text-gray-700 hover:text-gray-500 dark:hover:text-gray-500 pr-1">
          <i class="pi pi-cog" style="font-size: 0.8rem;"></i>
        </div>

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
import {computed, ref, watch} from "vue";
import router from "@/router";
import ChangeProject from "@/components/organisation/changeProject.vue";
import type {UserProject} from "@/api/query/interface/user";

const appStore = useAppStore();
const menuProject = ref();
const isDialogProject = ref<boolean>(false);

const isOrganisationAdmin = computed(() => {
  if (appStore.organisation) {
    return appStore.isOrganisationAdmin(appStore.organisation.id);
  } else {
    return false;
  }
});

const itemsProject = ref(createProjectNavigation());

watch(isOrganisationAdmin, (value) => {
  itemsProject.value = createProjectNavigation();
});

function createProjectNavigation() {
  let _items = [];

  _items.push({
    label: 'Switch project',
    icon: 'pi pi-box',
    command: () => {
      gotoRoute('home')
    }
  });

  // -- project settings for admins only
  if (appStore.organisation) {
    const isAdmin = appStore.isOrganisationAdmin(appStore.organisation.id);
    if (isAdmin) {
      _items.push({
        label: 'Project settings',
        icon: 'pi pi-pencil',
        command: () => {
          if (appStore.project) {
            goToModelPart('settings')
          }
        }
      });
    }
  }

  return _items;
}

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

function goToModelPart(part: string) {
  if (appStore.organisation && appStore.project) {
    router.push('/model/o/' + appStore.organisation.id + '/p/' + appStore.project.id + '/' + part);
  }
}

function gotoRoute(routeName: string) {
  router.push({name: routeName})
}

const toggleProjectMenu = (event: any) => {
  menuProject.value.toggle(event);
};

</script>

<style scoped>

</style>