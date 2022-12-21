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
  <div id="projectConfiguration">
    <div class="flex flex-row mx-auto">
      <div class="basis-1/3 p-2 ml-2">
        <project-settings/>
      </div>
      <div class="basis-1/3 p-2">
        <project-modules/>
      </div>
      <div class="basis-1/3 p-2 mr-2">
        <project-model-status/>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import ProjectSettings from "@/components/model/projectSettings.vue";
import ProjectModules from "@/components/model/projectModules.vue";
import ProjectModelStatus from "@/components/model/projectModelStatus.vue";
</script>