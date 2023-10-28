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
  <model-top-bar type="enum"/>
  <Splitter :style="'background-color:'+appStore.backgroundColor+';'">
    <!-- list of enums -->
    <SplitterPanel :size="30">
      <list-enum/>
    </SplitterPanel>
    <!-- detail of the enum -->
    <SplitterPanel :size="70">
      <div v-if="modelStore.enumLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="modelStore.enumSelectedId !== 0">
        <detail-enum/>
      </div>
    </SplitterPanel>
    <!-- right extension panel -->
    <SplitterPanel :size="50" v-if="modelStore.enumExtendedView">
      <div v-if="modelStore.enumLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="modelStore.enumSelectedId !== 0">
        <right-extension type="enum" :id="modelStore.enumSelectedId"/>
      </div>
    </SplitterPanel>
  </Splitter>
</template>

<script setup lang="ts">
import ListEnum from "@/components/model/listEnum.vue";
import DetailEnum from "@/components/model/detailEnum.vue";
import {useModelStore} from "@/stores/model";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import ModelTopBar from "@/_common/topBar/modelTopBar.vue";
import {useAppStore} from "@/stores/app";
import RightExtension from "@/_locodio/rightExtension.vue";

const modelStore = useModelStore();
const appStore = useAppStore();

</script>