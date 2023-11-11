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
  <div class="p-4">

    <!-- -- render the current document linked -->
    <div class="flex gap-2 mt-2" v-if="relatedProjectDocument">
      <div class="flex-grow line-clamp-1">
        {{ relatedProject }} -> {{ relatedProjectDocument.title }}
      </div>
      <div class="flex-none">
        <div v-if="!isSaving">
          <delete-button v-on:deleted="deleteProjectDocumentFn"/>
        </div>
        <div v-else>
          <i class="pi pi-spin pi-spinner"/>
        </div>
      </div>
    </div>

    <!-- -- create new document in project -->
    <div v-else
         class="flex gap-2">

      <div class="flex-none mt-1">
        create a new document in
      </div>
      <div class="flex-grow">
        <Dropdown optionLabel="name" v-model="selectedProject" :options="appStore.project?.relatedProjects"
                  placeholder="Select a related project" class="w-full p-dropdown-sm"/>
      </div>
      <div>
        <Button v-if="!isSaving"
                icon="pi pi-save"
                @click="createProjectDocument"
                label="CREATE"
                class="p-button-success p-button-sm"/>
        <Button v-else
                icon="pi pi-spin pi-spinner"
                disabled
                label="CREATE"
                class="p-button-success p-button-sm"/>
      </div>

    </div>

  </div>
</template>

<script setup lang="ts">
import {computed, ref} from 'vue';
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import {useAppStore} from "@/stores/app";
import {useLinearStore} from "@/stores/linear";
import type {Project} from "@/api/query/interface/linear";
import type {relatedProjectDocument} from "@/_lodocio/api/interface/tracker";
import DeleteButton from "@/components/common/deleteButton.vue";
import {addProjectDocument} from "@/_lodocio/api/command/tracker/addProjectDocument";
import {useToast} from "primevue/usetoast";
import {deleteProjectDocument} from "@/_lodocio/api/command/tracker/deleteProjectDocument";

const props = defineProps<{
  type: 'tracker' | 'group' | 'node',
  id: number,
  markdown: string,
  relatedProjectDocument?: relatedProjectDocument,
}>();

const trackerStore = useTrackerStore();
const appStore = useAppStore();
const linearStore = useLinearStore();
const toaster = useToast();
const isSaving = ref<boolean>(false);

const selectedDocumentId = ref<string>(props.relatedProjectDocument?.relatedDocumentId ?? '');
const selectedProject = ref<Project>(null);

const relatedProject = computed(() => {
  if (props.relatedProjectDocument && appStore.project.relatedProjects) {
    for (const project of appStore.project.relatedProjects) {
      if (props.relatedProjectDocument.relatedProjectId === project.id) return project.name;
    }
  }
  return '';
});

async function createProjectDocument() {
  if (selectedProject.value !== null) {
    isSaving.value = true;
    await addProjectDocument(
        {
          type: props.type,
          subjectId: props.id,
          relatedProjectId: selectedProject.value.id,
          content: props.markdown
        }
    );
    toaster.add({
      severity: "success",
      summary: "Related document created",
      detail: "",
      life: appStore.toastLifeTime,
    });
    if (props.type === 'node') {
      await trackerStore.reloadTrackerNode();
    }
    await trackerStore.reloadTracker();
    isSaving.value = false;
  }
}

async function deleteProjectDocumentFn() {
  if (props.relatedProjectDocument) {
    isSaving.value = true;
    await deleteProjectDocument(
        {
          type: props.type,
          subjectId: props.id,
          relatedProjectId: props.relatedProjectDocument.relatedProjectId,
          relatedDocumentId: props.relatedProjectDocument.relatedDocumentId,
        }
    );
    toaster.add({
      severity: "success",
      summary: "Related document deleted",
      detail: "",
      life: appStore.toastLifeTime,
    });
    await trackerStore.reloadTracker();
    if (props.type === 'node') {
      await trackerStore.reloadTrackerNode();
    }
    isSaving.value = false;
  }
}

</script>

<style scoped>

</style>