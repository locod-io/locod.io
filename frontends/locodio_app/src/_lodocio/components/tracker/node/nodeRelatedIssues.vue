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
  <!-- toolbar -->
  <div class="flex gap-2 border-b-[1px] border-gray-300 dark:border-gray-600 h-12">
    <div class="flex-none py-2 pl-2">
      <Button
          v-if="!isSaving"
          @click="saveRelatedIssues"
          icon="pi pi-save"
          label="SAVE"
          class="p-button-success p-button-sm w-32"/>
      <Button
          v-else
          disabled
          icon="pi pi-spin pi-spinner"
          label="SAVE"
          class="p-button-success p-button-sm w-32"/>
    </div>
    <div class="flex-none py-2 pl-2">
      <div class="flex">
        <div>
          <Button
              @click="reloadNode"
              icon="pi pi-refresh"
              class="p-button-sm"/>
        </div>
      </div>
    </div>
  </div>
  <!-- autocomplete selection -->
  <div class="p-2 border-b-[1px] border-gray-300 dark:border-gray-600">
    <AutoComplete v-model="selectedIssues" class="text-sm"
                  force-selection
                  style="width:100%;display: block"
                  multiple
                  :suggestions="filteredIssues"
                  @complete="search"
                  optionLabel="identifier"/>
  </div>
  <div class="flex gap-2 p-3 border-b-[1px] border-gray-300 dark:border-gray-600 h-12 text-sm bg-white dark:bg-gray-900"
       v-for="issue in selectedIssues" :key="issue.id">
    <div class="flex-none">
      <span class="text-xs">
      {{ issue.identifier }} ->
      </span>
    </div>
    <div class="flex-grow line-clamp-1">
      {{ issue.title }}
    </div>
  </div>

  <!-- render the related issues here -->
  <TabView>
    <TabPanel v-for="issue in trackerStore.trackerNodeRelatedIssues.collection"
              :key="issue.id">
      <template #header>
        <div>
            <span class="rounded-full px-2 text-white font-bold text-xs py-1"
                  :title="issue.state.name"
                  :style="'background-color: '+issue.state.color+''">
              {{issue.identifier}}
            </span>
        </div>
      </template>
      <issue-detail :issue="issue"/>
    </TabPanel>
  </TabView>

</template>

<script setup lang="ts">
import {useLinearStore} from "@/stores/linear";
import {useToast} from "primevue/usetoast";
import {ref, watch, computed} from "vue";
import {useAppStore} from "@/stores/app";
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import type {ChangeNodeRelatedIssuesCommand} from "@/_lodocio/api/command/tracker/changeNodeRelatedIssues";
import {changeNodeRelatedIssues} from "@/_lodocio/api/command/tracker/changeNodeRelatedIssues";
import IssueDetail from "@/_locodio/extension/issueDetail.vue";

const linearStore = useLinearStore();
const toaster = useToast();
const appStore = useAppStore();
const trackerStore = useTrackerStore();

const selectedIssues = ref(trackerStore.trackerNode?.relatedIssues);
const filteredIssues = ref();

const search = (event) => {
  setTimeout(() => {
    if (!event.query.trim().length) {
      filteredIssues.value = [...linearStore.cachedIssues];
    } else {
      filteredIssues.value = linearStore.cachedIssues.filter((issue) => {
        return (issue.title.toLowerCase().startsWith(event.query.toLowerCase()))
            || (issue.identifier.toLowerCase().includes(event.query.toLowerCase()));
      });
    }
  }, 250);
}

const isSaving = ref<boolean>(false);

async function saveRelatedIssues() {
  isSaving.value = true;
  const command: ChangeNodeRelatedIssuesCommand = {
    id: trackerStore.trackerNodeId,
    relatedIssues: selectedIssues.value
  };
  let result = await changeNodeRelatedIssues(command);
  toaster.add({
    severity: "success",
    summary: "Related issues saved",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await trackerStore.reloadTracker();
  isSaving.value = false;
}

function reloadNode() {
  trackerStore.reloadTrackerNode();
}

</script>

<style scoped>

</style>