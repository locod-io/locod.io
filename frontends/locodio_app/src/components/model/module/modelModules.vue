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
  <model-top-bar type="module"/>
  <div id="projectConfiguration">
    <Splitter :style="'background-color:'+appStore.backgroundColor+';'">
      <SplitterPanel :size="50">
        <project-modules/>
      </SplitterPanel>
      <SplitterPanel :size="50">
        &nbsp;
      </SplitterPanel>
    </Splitter>
  </div>
</template>

<script setup lang="ts">
import ModelTopBar from "@/_common/topBar/modelTopBar.vue";
import {useAppStore} from "@/stores/app";
import ProjectModules from "@/components/model/module/projectModules.vue";

const appStore = useAppStore();

</script>