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
      <list-domain-model/>
    </SplitterPanel>
    <SplitterPanel :size="75">
      <div v-if="modelStore.domainModelLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="modelStore.domainModelSelectedId !== 0">
        <detail-domain-model/>
      </div>
    </SplitterPanel>
  </Splitter>
</template>

<script setup lang="ts">
import ListDomainModel from "@/components/model/listDomainModel.vue";
import DetailDomainModel from "@/components/model/detailDomainModel.vue";
import {useModelStore} from "@/stores/model";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";

const modelStore = useModelStore();

</script>