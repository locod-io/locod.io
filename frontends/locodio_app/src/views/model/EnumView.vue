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
    <SplitterPanel :size="25">
      <list-enum/>
    </SplitterPanel>
    <SplitterPanel :size="75">
      <div v-if="modelStore.enumLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="modelStore.enumSelectedId !== 0">
        <detail-enum/>
      </div>
    </SplitterPanel>
  </Splitter>
</template>

<script setup lang="ts">
import ListEnum from "@/components/model/listEnum.vue";
import DetailEnum from "@/components/model/detailEnum.vue";
import {useModelStore} from "@/stores/model";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";

const modelStore = useModelStore();

</script>