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
  <Splitter style="background-color: #EEEEEE;">
    <!-- list -->
    <SplitterPanel :size="25">
      <list-template/>
    </SplitterPanel>
    <!-- detail -->
    <SplitterPanel :size="75">
      <div v-if="modelStore.templateLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="modelStore.templateSelectedId !== 0">
        <detail-template/>
      </div>
    </SplitterPanel>

  </Splitter>
</template>

<script setup lang="ts">
import ListTemplate from "@/components/model/listTemplate.vue";
import DetailTemplate from "@/components/model/detailTemplate.vue";
import {useModelStore} from "@/stores/model";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";

const modelStore = useModelStore();

</script>
