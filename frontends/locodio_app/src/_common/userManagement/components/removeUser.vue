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
  <div v-if="(userManagementStore.userId != 0) && (!userManagementStore.userDetailLoading)">
    <Button v-if="!isSaving"
            icon="pi pi-trash"
            class="p-button-sm p-button-danger p-button-outlined"
            @click="removeAction($event)"/>
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
import {useUserManagementStore} from "@/_common/userManagement/store/userManagementStore";
import {removeUserFromOrganisation} from "@/_common/userManagement/api/command/removeUserFromOrganisation";

const appStore = useAppStore();
const userManagementStore = useUserManagementStore();
const toaster = useToast();
const confirm = useConfirm();
const isSaving = ref<boolean>(false);

function removeAction(event: MouseEvent) {
  const target = event.currentTarget;
  if (target instanceof HTMLElement) {
    confirm.require({
      target: target,
      message: "Are you sure you want to remove '" + userManagementStore.userDetail.email + " from this " + appStore.user?.organisationLabel + " ?",
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
  await removeUserFromOrganisation({
    organisationId: userManagementStore.organisationId,
    userId: userManagementStore.userId
  });
  toaster.add({
    severity: "success",
    summary: "User '" + userManagementStore.userDetail.email + " removed from " + appStore.user?.organisationLabel + " ?",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await userManagementStore.reloadUsers();
  userManagementStore.userId = 0;
  userManagementStore.userDetail = null;
  isSaving.value = false;
}

</script>

<style scoped>

</style>