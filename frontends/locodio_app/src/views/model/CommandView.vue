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
  <model-top-bar type="command"/>
  <Splitter :style="'background-color:'+appStore.backgroundColor+';'">
    <!-- list of commands -->
    <SplitterPanel :size="30">
      <list-command/>
    </SplitterPanel>
    <!-- detail of the command -->
    <SplitterPanel :size="70">
      <div v-if="modelStore.commandLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="modelStore.commandSelectedId !== 0">
        <detail-command/>
      </div>
    </SplitterPanel>
    <!-- right extension panel  -->
    <SplitterPanel :size="50" v-if="modelStore.commandExtendedView">
      <div v-if="modelStore.commandLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="modelStore.commandSelectedId !== 0">
        <right-extension type="command" :id="modelStore.commandSelectedId"/>
      </div>
    </SplitterPanel>
  </Splitter>
</template>

<script setup lang="ts">
import ListCommand from "@/components/model/listCommand.vue";
import DetailCommand from "@/components/model/detailCommand.vue";
import {useModelStore} from "@/stores/model";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import ModelTopBar from "@/_common/topBar/modelTopBar.vue";
import {useAppStore} from "@/stores/app";
import RightExtension from "@/_locodio/rightExtension.vue";

const modelStore = useModelStore();
const appStore = useAppStore();

</script>
