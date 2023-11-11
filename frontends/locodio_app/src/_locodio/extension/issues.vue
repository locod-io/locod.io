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
              @click="loadRelatedIssues"
              v-if="!modelStore.documentorLoading"
              icon="pi pi-refresh"
              class="p-button-sm"/>
          <Button
              v-else
              disabled
              icon="pi pi-spin pi-spinner"
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
  <div v-if="!modelStore.documentorIssuesLoading">
    <TabView>
      <TabPanel v-for="issue in documentor.linearIssuesDetails"
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
  </div>
  <div v-else class="text-center">
    <br><br>
    <progress-spinner></progress-spinner>
  </div>

</template>

<script setup lang="ts">
import {computed, onMounted, ref, watch} from "vue";
import type {Documentor} from "@/api/query/interface/model";
import {useModelStore} from "@/stores/model";
import {useToast} from "primevue/usetoast";
import {useLinearStore} from "@/stores/linear";
import type {ChangeDocumentorRelatedIssuesCommand} from "@/api/command/interface/modelConfiguration";
import {changeDocumentorRelatedIssues} from "@/api/command/model/changeDocumentorRelatedIssues";
import Documentation from "@/_locodio/extension/documentation.vue";
import ExtensionWrapper from "@/_locodio/extension/extensionWrapper.vue";
import IssueDetail from "@/_locodio/extension/issueDetail.vue";

const props = defineProps<{
  type: 'module' | 'domain-model' | 'enum' | 'query' | 'command',
  id: number
}>();

const modelStore = useModelStore();
const linearStore = useLinearStore();
const toaster = useToast();

onMounted((): void => {
  loadRelatedIssues();
});

const selectedIssues = ref();
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

const documentorId = computed((): number => {
  if (props.type == 'domain-model') return modelStore.domainModel?.documentor.id ?? 0;
  if (props.type == 'enum') return modelStore.enum?.documentor.id ?? 0;
  if (props.type == 'query') return modelStore.query?.documentor.id ?? 0;
  if (props.type == 'command') return modelStore.command?.documentor.id ?? 0;
  return modelStore.module?.documentor.id ?? 0;
});

const documentor = computed((): Documentor | undefined => {
  if (props.type == 'domain-model') return modelStore.domainModelDocumentor;
  if (props.type == 'enum') return modelStore.enumDocumentor;
  if (props.type == 'query') return modelStore.queryDocumentor;
  if (props.type == 'command') return modelStore.commandDocumentor;
  return modelStore.moduleDocumentor;
});

watch(documentor, (value) => {
  selectedIssues.value = value?.linearIssues;
});

const isSaving = ref<boolean>(false);

async function saveRelatedIssues() {
  isSaving.value = true;
  const command: ChangeDocumentorRelatedIssuesCommand = {
    id: documentor.value?.id ?? 0,
    relatedIssues: selectedIssues.value
  };
  let result = await changeDocumentorRelatedIssues(command);
  if (modelStore.documentor) {
    switch (modelStore.documentor.type) {
      case 'domain-model':
        await modelStore.loadDocumentor(documentor.value.id, documentor.value.type, modelStore.domainModel.id, false, true);
        break;
      case 'enum':
        await modelStore.loadDocumentor(documentor.value.id, documentor.value.type, modelStore.enum.id, false, true);
        break;
      case 'query':
        await modelStore.loadDocumentor(documentor.value.id, documentor.value.type, modelStore.query.id, false, true);
        break;
      case 'command':
        await modelStore.loadDocumentor(documentor.value.id, documentor.value.type, modelStore.command.id, false, true);
        break;
    }
  }
  isSaving.value = false;
  toaster.add({
    severity: "success",
    summary: "Related issues saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  void loadRelatedIssues();
}
async function loadRelatedIssues() {
  await modelStore.loadDocumentorIssueDetails(props.type,documentor.value?.id ?? 0);
}

</script>

<style scoped>

</style>