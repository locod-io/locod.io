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
  <model-top-bar type="configuration"/>
  <div id="projectConfiguration">
    <Splitter :style="'background-color:'+appStore.backgroundColor+';'">
      <SplitterPanel :size="50">
        <project-settings/>
      </SplitterPanel>
      <SplitterPanel :size="50">
        <project-model-status/>
      </SplitterPanel>
    </Splitter>
  </div>
</template>

<script setup lang="ts">
import ProjectSettings from "@/components/model/projectSettings.vue";
import ProjectModelStatus from "@/components/model/projectModelStatus.vue";
import ModelTopBar from "@/_common/topBar/modelTopBar.vue";
import {useAppStore} from "@/stores/app";

const appStore = useAppStore();
</script>