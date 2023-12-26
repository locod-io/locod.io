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

  <tracker-top-bar type="tracker-document"/>

  <Splitter :style="'background-color:'+appStore.backgroundColor+';'">
    <!-- tracker structure -->
    <SplitterPanel :size="30">
      <div v-if="trackerStore.trackerIsLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="trackerStore.trackerId !== 0">
        <tracker-navigation/>
      </div>
    </SplitterPanel>

    <!-- central content view -->
    <SplitterPanel :size="40">
      <div v-if="trackerStore.trackerIsLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="trackerStore.trackerId !== 0">
        <tracker-content/>
      </div>
    </SplitterPanel>

    <!-- tracker right navigation -->
    <SplitterPanel :size="30">
      <div v-if="trackerStore.trackerId !== 0">
        <tracker-node/>
      </div>
    </SplitterPanel>

  </Splitter>

</template>

<script setup lang="ts">
import {useAppStore} from "@/stores/app";
import TrackerNavigation from "@/_lodocio/components/tracker/trackerNavigation.vue";
import TrackerNode from "@/_lodocio/components/tracker/trackerNode.vue";
import TrackerContent from "@/_lodocio/components/tracker/trackerContent.vue";
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import TrackerTopBar from "@/_lodocio/components/tracker/trackerTopBar.vue";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";

const appStore = useAppStore();
const trackerStore = useTrackerStore();

</script>

<style scoped>

</style>