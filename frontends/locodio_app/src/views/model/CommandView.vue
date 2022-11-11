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
      <list-command/>
    </SplitterPanel>

    <SplitterPanel :size="75">
      <div v-if="modelStore.commandLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="modelStore.commandSelectedId !== 0">
        <detail-command/>
      </div>
    </SplitterPanel>

  </Splitter>
</template>

<script setup lang="ts">
import ListCommand from "@/components/model/listCommand.vue";
import DetailCommand from "@/components/model/detailCommand.vue";
import {useModelStore} from "@/stores/model";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";

const modelStore = useModelStore();

</script>
