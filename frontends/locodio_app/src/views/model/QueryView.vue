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
      <list-query/>
    </SplitterPanel>

    <SplitterPanel :size="75">
      <div v-if="modelStore.queryLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="modelStore.querySelectedId !== 0">
        <detail-query/>
      </div>
    </SplitterPanel>

  </Splitter>
</template>

<script setup lang="ts">
import ListQuery from "@/components/model/listQuery.vue";
import DetailQuery from "@/components/model/detailQuery.vue";
import {useModelStore} from "@/stores/model";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";

const modelStore = useModelStore();

</script>