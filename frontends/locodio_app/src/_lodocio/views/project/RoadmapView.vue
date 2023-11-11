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
  <documentation-top-bar type="roadmap"/>
  <div v-if="projectStore.roadmapsLoading">
    <loading-spinner/>
  </div>
  <div v-else>
    <roadmap-overview/>
  </div>
</template>

<script setup lang="ts">
import {onMounted} from "vue";
import DocumentationTopBar from "@/_common/topBar/documentationTopBar.vue";
import RoadmapOverview from "@/_lodocio/components/roadmap/roadmapOverview.vue";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";

const projectStore = useDocProjectStore();

onMounted((): void => {
  void projectStore.loadRoadmaps();
});

</script>

<style scoped>

</style>