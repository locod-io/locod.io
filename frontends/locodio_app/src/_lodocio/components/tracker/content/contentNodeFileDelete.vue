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
  <div v-if="!isSaving">
    <delete-button @deleted="deleteImage"/>
  </div>
  <div v-else>
    <i class="pi pi-spin pi-spinner"/>
  </div>
</template>

<script setup lang="ts">
import {ref} from "vue";
import {useAppStore} from "@/stores/app";
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import DeleteButton from "@/components/common/deleteButton.vue";
import {deleteTrackerFile} from "@/_lodocio/api/command/tracker/deleteTrackerFile";
import type {TrackerFile} from "@/_lodocio/api/interface/tracker";
import {useToast} from "primevue/usetoast";

const props = defineProps<{ file: TrackerFile }>();
const appStore = useAppStore();
const trackerStore = useTrackerStore();
const isSaving = ref<boolean>(false);
const toaster = useToast();

async function deleteImage() {
  console.log('-- delete image');
  isSaving.value = true;
  await deleteTrackerFile({id: props.file.id})
  toaster.add({
    severity: "success",
    summary: "File " + props.file.name + " deleted",
    detail: "",
    life: appStore.toastLifeTime
  });
  await trackerStore.reloadTracker();
  isSaving.value = false;
}

</script>

<style scoped>
#dropZoneTrackerImage {
  height: 40px !important;
  padding: 2px !important;
}
</style>