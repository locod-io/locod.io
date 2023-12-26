<!--
/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
-->

<template>

  <documentation-top-bar type="wiki"/>
  <Splitter :style="'background-color:'+appStore.backgroundColor+';'">

    <!-- wiki structure -->
    <SplitterPanel :size="30">
      <div v-if="wikiStore.wikiIsLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="wikiStore.wikiId !== 0">
        <wiki-navigation/>
      </div>
    </SplitterPanel>

    <!-- central content view -->
    <SplitterPanel :size="40">
      <div v-if="wikiStore.wikiIsLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="wikiStore.wikiId !== 0">
        <wiki-content/>
      </div>
    </SplitterPanel>

    <!-- wiki right navigation -->
    <SplitterPanel :size="30">
      <div v-if="wikiStore.wikiId !== 0">
        <wiki-node/>
      </div>
    </SplitterPanel>

  </Splitter>

</template>

<script setup lang="ts">
import DocumentationTopBar from "@/_common/topBar/documentationTopBar.vue";
import {useAppStore} from "@/stores/app";
import {useWikiStore} from "@/_lodocio/stores/wiki";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import WikiNavigation from "@/_lodocio/components/wiki/wikiNavigation.vue";
import WikiContent from "@/_lodocio/components/wiki/wikiContent.vue";
import WikiNode from "@/_lodocio/components/wiki/wikiNode.vue";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import {onMounted} from "vue";
import {useLinearStore} from "@/stores/linear";

const appStore = useAppStore();
const projectStore = useDocProjectStore();
const wikiStore = useWikiStore();
const linearStore = useLinearStore();

onMounted((): void => {
  void loadWiki();
  void cacheLinearIssues();
});

async function loadWiki() {
  if (projectStore.selectedWikiId !== 0) {
    await wikiStore.loadWiki(projectStore.selectedWikiId);
  }
}

async function cacheLinearIssues() {
  if (projectStore.selectedWikiId !== 0) {
    console.log('-- cache linear issues');
    await linearStore.cacheIssuesByWiki(projectStore.selectedWikiId);
  }
}

</script>

<style scoped>

</style>