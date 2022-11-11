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
  <user-heading label="My Master Templates"/>
  <div>
    <Splitter style="background-color: #EEEEEE;">
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
import UserHeading from "@/components/user/userHeading.vue";
import {useModelStore} from "@/stores/model";
import ListMasterTemplate from "@/components/model/listMasterTemplate.vue";
import DetailMasterTemplate from "@/components/model/detailMasterTemplate.vue";
import {onMounted} from "vue";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import router from "@/router";

const modelStore = useModelStore();

onMounted((): void => {
  void modelStore.loadMasterTemplates();
});

</script>