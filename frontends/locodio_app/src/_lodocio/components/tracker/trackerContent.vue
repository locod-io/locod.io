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

  <!-- // top navigation -->
  <div class="flex gap-2 border-b-[1px] border-gray-300 dark:border-gray-600 h-12">
    <div class="py-2 pl-2">
      <Button @click="openNodes"
              title="open all items"
              icon="pi pi-folder-open"
              class="p-button-sm p-button-outlined p-button-secondary p-button-icon-only"
      />
    </div>
    <div class="py-2">
      <Button @click="closeNodes"
              title="close all items"
              icon="pi pi-folder"
              class="p-button-sm p-button-outlined p-button-secondary p-button-icon-only"
      />
    </div>
    <div class="flex-grow">&nbsp;</div>
    <div>
      <download-pdf-button class="mt-3" @click="downloadPdf"/>
    </div>
    <div class="mt-3 mr-3">
      <export-button @click="showContentDialogTrackerMarkdownFn"/>
    </div>
  </div>

  <!-- // content rendering  -->
  <DetailWrapper :estate-height="125" class="bg-white dark:bg-gray-900">
    <div v-if="trackerStore.tracker">
      <!-- // intro nodes -->
      <content-node-renderer :node="node" v-for="node in trackerStore.tracker.nodes"/>
      <!-- // groups -->
      <content-groups-renderer :groups="trackerStore.tracker.groups"/>
    </div>
  </DetailWrapper>

  <!-- -- markdown exporter for the entire tracker -->
  <div v-if="trackerStore.tracker">
    <Dialog position="top"
            v-model:visible="showContentDialogTrackerMarkdown"
            :modal="true"
    >
      <template #header>
        <div class="flex gap-2">
          <div>{{ trackerStore.tracker.name }}</div>
        </div>
      </template>
      <dialog-tracker-mark-down/>
    </Dialog>
  </div>

  <!-- // bottom navigation -->
  <div class="flex gap-2 border-t-[1px] border-gray-300 dark:border-gray-600 h-12">
    &nbsp;
  </div>

</template>

<script setup lang="ts">
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import ContentNodeRenderer from "@/_lodocio/components/tracker/content/contentNodeRenderer.vue";
import ContentGroupsRenderer from "@/_lodocio/components/tracker/content/contentGroupsRenderer.vue";
import DetailWrapper from "@/components/wrapper/detailWrapper.vue";
import TrackerArtefactStatusLabel from "@/_lodocio/components/tracker/navigation/trackerArtefactStatusLabel.vue";
import DialogNodeMarkdown from "@/_lodocio/components/tracker/dialog/dialogNodeMarkdown.vue";
import {ref} from "vue";
import DialogTrackerMarkDown from "@/_lodocio/components/tracker/dialog/dialogTrackerMarkDown.vue";
import MarkdownButton from "@/_common/buttons/markdownButton.vue";
import DownloadPdfButton from "@/_common/buttons/downloadPdfButton.vue";
import ExportButton from "@/_common/buttons/exportButton.vue";

const apiUrl = import.meta.env.VITE_API_URL as string;
const trackerStore = useTrackerStore();

function openNodes() {
  trackerStore.openNodes();
}

function closeNodes() {
  trackerStore.closeNodes();
}

function downloadPdf() {
  let url = apiUrl + '/doc/tracker/' + trackerStore.trackerId + '/pdf';
  window.open(url, '_blank');
}

const showContentDialogTrackerMarkdown = ref<boolean>(false);

function showContentDialogTrackerMarkdownFn(): void {
  showContentDialogTrackerMarkdown.value = true;
}

</script>

<style scoped>

</style>