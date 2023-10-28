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
  <model-top-bar type="query"/>
  <Splitter :style="'background-color:'+appStore.backgroundColor+';'">
    <!-- list of queries -->
    <SplitterPanel :size="30">
      <list-query/>
    </SplitterPanel>
    <!-- detail of the query -->
    <SplitterPanel :size="70">
      <div v-if="modelStore.queryLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="modelStore.querySelectedId !== 0">
        <detail-query/>
      </div>
    </SplitterPanel>
    <!-- right extension panel  -->
    <SplitterPanel :size="50" v-if="modelStore.queryExtendedView">
      <div v-if="modelStore.queryLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="modelStore.querySelectedId !== 0">
        <right-extension type="query" :id="modelStore.querySelectedId"/>
      </div>
    </SplitterPanel>
  </Splitter>
</template>

<script setup lang="ts">
import ListQuery from "@/components/model/listQuery.vue";
import DetailQuery from "@/components/model/detailQuery.vue";
import {useModelStore} from "@/stores/model";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import ModelTopBar from "@/_common/topBar/modelTopBar.vue";
import {useAppStore} from "@/stores/app";
import RightExtension from "@/_locodio/rightExtension.vue";

const modelStore = useModelStore();
const appStore = useAppStore();

</script>