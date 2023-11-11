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
  <div v-if="(projectStore.selectedTrackerId !== 0)">
    <Button v-if="!isSaving"
            icon="pi pi-trash"
            class="p-button-sm p-button-danger p-button-outlined"
            @click="deleteAction($event)"/>
    <Button v-else
            icon="pi pi-spin pi-spinner"
            class="p-button-sm p-button-danger p-button-outlined"/>
  </div>
  <ConfirmPopup/>
</template>

<script setup lang="ts">
import {useToast} from "primevue/usetoast";
import {useConfirm} from "primevue/useconfirm";
import {ref} from "vue";
import {useAppStore} from "@/stores/app";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import {deleteTracker} from "@/_lodocio/api/command/tracker/deleteTracker";

const projectStore = useDocProjectStore();
const appStore = useAppStore();
const toaster = useToast();
const confirm = useConfirm();
const isSaving = ref<boolean>(false);

function deleteAction(event: MouseEvent) {
  const target = event.currentTarget;
  if (target instanceof HTMLElement) {
    confirm.require({
      target: target,
      message: "Are you sure you want to delete '" + projectStore.trackerDetail?.name+"' ? And all its content, this can not be undone.",
      icon: "pi pi-exclamation-triangle",
      acceptLabel: "Yes",
      rejectLabel: "No",
      acceptIcon: "pi pi-check",
      rejectIcon: "pi pi-times",
      accept: () => {
        void deleteDetail();
      },
      reject: () => {
        // callback to execute when user rejects the action
      },
    });
  }
}

async function deleteDetail() {
  isSaving.value = true;
  await deleteTracker({id: projectStore.selectedTrackerId});
  toaster.add({
    severity: "success",
    summary: "Tracker deleted.",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await projectStore.reloadProject();
  projectStore.selectedTrackerId = 0;
  projectStore.trackerDetail = undefined;
  isSaving.value = false;
}
</script>

<style scoped>

</style>