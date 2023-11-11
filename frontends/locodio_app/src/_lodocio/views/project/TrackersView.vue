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
  <documentation-top-bar type="trackers"/>
  <Splitter :style="'background-color:'+appStore.backgroundColor+';'">

    <!-- list of domain models -->
    <SplitterPanel :size="30">
      <list-trackers></list-trackers>
    </SplitterPanel>

    <!-- central control panel -->
    <SplitterPanel :size="70">
      <div v-if="docProjectStore.trackerDetailLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="docProjectStore.selectedTrackerId !== 0">
        <tracker-detail/>
      </div>
    </SplitterPanel>

  </Splitter>
</template>

<script setup lang="ts">
import DocumentationTopBar from "@/_common/topBar/documentationTopBar.vue";
import {useAppStore} from "@/stores/app";
import ListTrackers from "@/_lodocio/components/trackers/listTrackers.vue";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import TrackerDetail from "@/_lodocio/components/trackers/trackerDetail.vue";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";

const appStore = useAppStore();
const docProjectStore = useDocProjectStore();

</script>

<style scoped>

</style>