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
  <div v-if="(modelStore.templateSelectedId != 0) && (!modelStore.templateLoading)">
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
import {useModelStore} from "@/stores/model";
import {useToast} from "primevue/usetoast";
import {useConfirm} from "primevue/useconfirm";
import {ref} from "vue";
import {deleteTemplate} from "@/api/command/model/deleteTemplate";

const modelStore = useModelStore();
const toaster = useToast();
const confirm = useConfirm();
const isSaving = ref<boolean>(false);

function deleteAction(event: MouseEvent) {
  const target = event.currentTarget;
  if (target instanceof HTMLElement) {
    confirm.require({
      target: target,
      message: "Are you sure you want to delete '"+modelStore.template?.name+ "' ?",
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
  await deleteTemplate({id: modelStore.templateSelectedId});
  toaster.add({
    severity: "success",
    summary: "Template deleted.",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadProject();
  modelStore.templateSelectedId = 0;
  modelStore.template = undefined;
  isSaving.value = false;
}

</script>

<style scoped>

</style>