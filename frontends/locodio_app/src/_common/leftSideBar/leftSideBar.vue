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

  <!-- organisation and profile -->
  <organisation-and-profile/>
  <project-nav/>
  <!-- wrapper -->
  <side-bar-wrapper :estate-height="165">
    <div v-if="!appStore.isLoading">
      <project-detail-nav/>
    </div>
    <div v-else>
      <loading-spinner></loading-spinner>
    </div>
  </side-bar-wrapper>
  <!-- left side bar footer -->
  <footer-left-side-bar/>

</template>

<script setup lang="ts">
import OrganisationAndProfile from "@/_common/leftSideBar/organisationAndProfile.vue";
import SideBarWrapper from "@/_common/wrapper/sideBarWrapper.vue";
import ProjectNav from "@/_common/leftSideBar/projectNav.vue";
import ProjectDetailNav from "@/_common/leftSideBar/projectDetailNav.vue";
import FooterLeftSideBar from "@/_common/leftSideBar/footerLeftSideBar.vue";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import {useAppStore} from "@/stores/app";

const appStore = useAppStore();

</script>

<style scoped>

</style>