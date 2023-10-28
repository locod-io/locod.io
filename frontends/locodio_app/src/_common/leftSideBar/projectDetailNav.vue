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
  <div v-if="appStore.organisation && appStore.project" class="p-2 text-sm font-semibold">

    <!-- ----------------------------------------------------------------------------------------------------------- -->
    <!-- lodocio -->
<!--    <div class="flex gap-2 font-bold mt-1 cursor-pointer" @click="toggleDocumentation">-->
<!--      <div class="no-flex w-4">-->
<!--        <i class="pi pi-chevron-down" style="font-size: 0.8rem;" v-if="showDocumentationMenu"></i>-->
<!--        <i class="pi pi-chevron-right" style="font-size: 0.8rem;" v-else></i>-->
<!--      </div>-->
<!--      <div class="flex-grow line-clamp-1">Documentation</div>-->
<!--    </div>-->
<!--    <div class="ml-3 mt-1" v-if="showDocumentationMenu">-->
<!--    </div>-->

    <!-- ----------------------------------------------------------------------------------------------------------- -->
    <!-- locod.io // Model Driven Development - Template Based Code Generation                                       -->
    <!-- ----------------------------------------------------------------------------------------------------------- -->
    <div class="flex gap-2 font-bold mt-4 cursor-pointer" @click="toggleModel">
      <div class="no-flex w-4">
        <i class="pi pi-chevron-down" style="font-size: 0.8rem;" v-if="showModelMenu"></i>
        <i class="pi pi-chevron-right" style="font-size: 0.8rem;" v-else></i>
      </div>
      <div class="flex-grow line-clamp-1">Model</div>
    </div>
    <div class="ml-3 mt-1" v-if="showModelMenu">
      <locodio-navigation/>
    </div>

  </div>
</template>

<script setup lang="ts">
import {useAppStore} from "@/stores/app";
import router from "@/router";
import {ref} from "vue";
import LocodioNavigation from "@/_common/leftSideBar/locodioNavigation.vue";

const appStore = useAppStore();

const showModelMenu = ref<boolean>(true);

function toggleModel() {
  showModelMenu.value = !showModelMenu.value;
}

const showDocumentationMenu = ref<boolean>(true);

function toggleDocumentation() {
  showDocumentationMenu.value = !showDocumentationMenu.value;
}

</script>

<style scoped>

</style>