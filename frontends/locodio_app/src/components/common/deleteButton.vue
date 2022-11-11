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
  <div id="editButton" class="w-8" @click="deleteAction($event)">
    <div class="rounded-full pt-1.5 pb-1 px-2 hover:bg-red-600 hover:text-gray-50 cursor-pointer text-gray-300">
      <i class="pi pi-trash"></i>
    </div>
  </div>
  <ConfirmPopup/>
</template>

<script setup lang="ts">
import {useConfirm} from "primevue/useconfirm";

const emit = defineEmits(["deleted"]);
const confirm = useConfirm();

function deleteAction(event: MouseEvent) {
  const target = event.currentTarget;
  if (target instanceof HTMLElement) {
    confirm.require({
      target: target,
      message: "Are you sure ?",
      icon: "pi pi-exclamation-triangle",
      acceptLabel: "Yes",
      rejectLabel: "No",
      acceptIcon: "pi pi-check",
      rejectIcon: "pi pi-times",
      accept: () => {
        emit("deleted");
      },
      reject: () => {
        // callback to execute when user rejects the action
      },
    });
  }
}

</script>