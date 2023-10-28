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
  <model-top-bar type="domain-model"/>
  <Splitter :style="'background-color:'+appStore.backgroundColor+';'">
    <!-- list of domain models -->
    <SplitterPanel :size="30">
      <list-domain-model/>
    </SplitterPanel>
    <!-- central control panel -->
    <SplitterPanel :size="70">
      <div v-if="modelStore.domainModelLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="modelStore.domainModelSelectedId !== 0">
        <detail-domain-model/>
      </div>
    </SplitterPanel>
    <!-- right extension panel  -->
    <SplitterPanel :size="50" v-if="modelStore.domainModelExtendedView">
      <div v-if="modelStore.domainModelLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="modelStore.domainModelSelectedId !== 0">
        <right-extension type="domain-model" :id="modelStore.domainModelSelectedId"/>
      </div>
    </SplitterPanel>
  </Splitter>
</template>

<script setup lang="ts">
import ListDomainModel from "@/components/model/listDomainModel.vue";
import DetailDomainModel from "@/components/model/detailDomainModel.vue";
import {useModelStore} from "@/stores/model";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import ModelTopBar from "@/_common/topBar/modelTopBar.vue";
import {useAppStore} from "@/stores/app";
import RightExtension from "@/_locodio/rightExtension.vue";

const modelStore = useModelStore();
const appStore = useAppStore();

</script>