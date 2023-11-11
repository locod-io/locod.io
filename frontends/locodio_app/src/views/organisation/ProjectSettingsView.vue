<template>
  <model-top-bar type="settings"/>
  <div v-if="appStore.project">
    <extended-project-settings/>
  </div>
  <div v-else>
    <loading-spinner/>
  </div>
</template>

<script setup lang="ts">
import ExtendedProjectSettings from "@/_locodio/project/extendedProjectSettings.vue";
import ModelTopBar from "@/_common/topBar/modelTopBar.vue";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import {useAppStore} from "@/stores/app";

const appStore = useAppStore();
</script>

<style scoped>

</style>