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
  <div v-if="wikiStore.wikiNodeId !== 0">
    <!-- -- loading -->
    <div v-if="wikiStore.wikiNodeIsLoading">
      <loading-spinner></loading-spinner>
    </div>
    <div v-else>

      <!--  detail node -->
      <div class="flex gap-2 border-b-[1px] border-gray-300 dark:border-gray-600 h-12 p-3 bg-white dark:bg-gray-900">
        <div v-if="!wikiStore.wikiNode.status.isFinal">
          <edit-button @click="showContentDialogFn"/>
        </div>
        <div class="mt-1">
          <wiki-artefact-status-label :node="wikiStore.wikiNode"/>
        </div>
        <div class="flex-grow line-clamp-1">
          {{ wikiStore.wikiNode.name }}
        </div>
        <div>
          <export-button @click="showContentDialogMarkdownFn"/>
        </div>
      </div>

      <TabView>
        <TabPanel header="Related Issues">
          <extension-wrapper :estate-height="166">
            <node-related-issues/>
          </extension-wrapper>
        </TabPanel>
        <TabPanel header="Activity">
          <extension-wrapper :estate-height="166">
            <node-activity/>
          </extension-wrapper>
        </TabPanel>
      </TabView>

      <!-- // node detail footer -->
      <div class="border-t-[1px] border-gray-300 dark:border-gray-600 h-12 p-3 text-sm">

      </div>

    </div>
  </div>

  <!-- -- extended editor for the node -->
  <div v-if="wikiStore.wikiNode">
    <Dialog
        v-model:visible="showContentDialog"
        maximizable
        :modal="true"
    >
      <template #header>
        <div class="flex gap-2">
          <div class="mt-0.5">
            <wiki-artefact-status-label :node="wikiStore.wikiNode"/>
          </div>
          <div>{{ wikiStore.wikiNode.name }}</div>
        </div>
      </template>
      <dialog-node-content :node="wikiStore.wikiNode" class="mx-auto"/>
    </Dialog>
  </div>

  <!-- -- markdown exporter for the node -->
  <div v-if="wikiStore.wikiNode">
    <Dialog
        v-model:visible="showContentDialogMarkdown"
        position="top"
        :modal="true"
    >
      <template #header>
        <div class="flex gap-2">
          <div class="mt-0.5">
            <wiki-artefact-status-label :node="wikiStore.wikiNode"/>
          </div>
          <div>{{ wikiStore.wikiNode.name }}</div>
        </div>
      </template>
      <dialog-node-markdown :node="wikiStore.wikiNode"/>
    </Dialog>
  </div>

</template>

<script setup lang="ts">
import {useWikiStore} from "@/_lodocio/stores/wiki";
import ExtensionWrapper from "@/_locodio/extension/extensionWrapper.vue";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import NodeRelatedIssues from "@/_lodocio/components/wiki/node/nodeRelatedIssues.vue";
import NodeActivity from "@/_lodocio/components/wiki/node/nodeActivity.vue";
import WikiArtefactStatusLabel from "@/_lodocio/components/wiki/navigation/wikiArtefactStatusLabel.vue";
import {ref} from 'vue';
import DialogNodeContent from "@/_lodocio/components/wiki/dialog/dialogNodeContent.vue";
import EditButton from "@/components/common/editButton.vue";
import DialogNodeMarkdown from "@/_lodocio/components/wiki/dialog/dialogNodeMarkdown.vue";
import ExportButton from "@/_common/buttons/exportButton.vue";

const wikiStore = useWikiStore();
const showContentDialog = ref<boolean>(false);
const showContentDialogMarkdown = ref<boolean>(false);
function showContentDialogFn() {
  showContentDialog.value = true;
}

function showContentDialogMarkdownFn() {
  showContentDialogMarkdown.value = true;
}

</script>

<style scoped>

</style>