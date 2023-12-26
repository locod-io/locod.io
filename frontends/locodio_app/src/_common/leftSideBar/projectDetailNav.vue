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
  <div v-if="appStore.organisation && appStore.project" class="p-2 text-sm font-semibold">

    <!-- ----------------------------------------------------------------------------------------------------------- -->
    <!-- lodoc.io // Close the loop of your documentation.                                                           -->
    <!-- ----------------------------------------------------------------------------------------------------------- -->
    <div v-if="appStore.user.hasLodocio">
      <div class="flex gap-2 font-bold mt-1 cursor-pointer">
        <div class="no-flex w-4" @click="toggleDocumentation">
          <i class="pi pi-chevron-down" style="font-size: 0.8rem;" v-if="showDocumentationMenu"></i>
          <i class="pi pi-chevron-right" style="font-size: 0.8rem;" v-else></i>
        </div>
        <div @click="toggleDocumentation"
             class="flex-grow line-clamp-1">Documentation
        </div>
        <!-- <div class="flex-none text-gray-300 dark:text-gray-700 hover:text-gray-500 dark:hover:text-gray-500 pr-2">-->
        <!--   <i class="pi pi-cog" style="font-size: 0.8rem;"></i>-->
        <!-- </div>-->
      </div>
      <div class="ml-3 mt-1" v-if="showDocumentationMenu">
        <lodocio-navigation/>
      </div>
    </div>

    <!-- ----------------------------------------------------------------------------------------------------------- -->
    <!-- locod.io // Model Driven Development - Template Based Code Generation                                       -->
    <!-- ----------------------------------------------------------------------------------------------------------- -->
    <div v-if="appStore.user?.hasLocodio">
      <div class="flex gap-2 font-bold mt-4 cursor-pointer">
        <div class="no-flex w-4" @click="toggleModel">
          <i class="pi pi-chevron-down" style="font-size: 0.8rem;" v-if="showModelMenu"></i>
          <i class="pi pi-chevron-right" style="font-size: 0.8rem;" v-else></i>
        </div>
        <div class="flex-grow line-clamp-1" @click="toggleModel">Model</div>
        <div v-if="isOrganisationAdmin" @click="gotoModelPart('configuration')"
             class="flex-none text-gray-300 dark:text-gray-700 hover:text-gray-500 dark:hover:text-gray-500 pr-2">
          <i class="pi pi-cog" style="font-size: 0.8rem;"></i>
        </div>
      </div>
      <div class="ml-3 mt-1" v-if="showModelMenu">
        <locodio-navigation/>
      </div>
    </div>
  </div>

  <div v-else>
    <loading-spinner v-if="showLoader"/>
  </div>

</template>

<script setup lang="ts">
import {useAppStore} from "@/stores/app";
import router from "@/router";
import {computed, ref} from "vue";
import LocodioNavigation from "@/_common/leftSideBar/locodioNavigation.vue";
import LodocioNavigation from "@/_common/leftSideBar/lodocioNavigation.vue";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";

const appStore = useAppStore();
const showModelMenu = ref<boolean>(true);

function toggleModel() {
  showModelMenu.value = !showModelMenu.value;
}

const isOrganisationAdmin = computed(() => {
  if (appStore.organisation) {
    return appStore.isOrganisationAdmin(appStore.organisation.id);
  } else {
    return false;
  }
});

const showDocumentationMenu = ref<boolean>(true);

const showLoader = computed((): boolean => {
  console.log((router.currentRoute.value.name));
  if (router.currentRoute.value.name === 'home'
      || router.currentRoute.value.name === 'myProfile'
      || router.currentRoute.value.name === 'changeMyPassword'
      || router.currentRoute.value.name === 'myOrganisations'
      || router.currentRoute.value.name === 'myMasterTemplates'
      || router.currentRoute.value.name === 'browseTemplates'
      || router.currentRoute.value.name === 'about'
      || router.currentRoute.value.name === 'myMegaRoadMap'
  ) {
    return false;
  }
  return true;
});

function toggleDocumentation() {
  showDocumentationMenu.value = !showDocumentationMenu.value;
}

function gotoModelPart(routeName: string) {
  if (appStore.organisation && appStore.project) {
    router.push({name: routeName, params: {organisationId: appStore.organisation.id, projectId: appStore.project.id}})
  }
}

</script>

<style scoped>

</style>