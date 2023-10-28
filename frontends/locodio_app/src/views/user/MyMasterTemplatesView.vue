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
  <model-top-bar type="masterTemplates"/>
  <div>
    <Splitter :style="'background-color:'+appStore.backgroundColor+';'">
      <!-- list -->
      <SplitterPanel :size="25">
        <list-master-template/>
      </SplitterPanel>
      <!-- detail -->
      <SplitterPanel :size="75">
        <div v-if="modelStore.masterTemplateLoading">
          <loading-spinner></loading-spinner>
        </div>
        <div v-else-if="modelStore.masterTemplateSelectedId !== 0">
          <detail-master-template/>
        </div>
      </SplitterPanel>
    </Splitter>
  </div>
</template>

<script setup lang="ts">
import {useModelStore} from "@/stores/model";
import ListMasterTemplate from "@/components/model/listMasterTemplate.vue";
import DetailMasterTemplate from "@/components/model/detailMasterTemplate.vue";
import {onMounted} from "vue";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import ModelTopBar from "@/_common/topBar/modelTopBar.vue";
import {useAppStore} from "@/stores/app";

const modelStore = useModelStore();
const appStore = useAppStore();

onMounted((): void => {
  void modelStore.loadMasterTemplates();
});

</script>