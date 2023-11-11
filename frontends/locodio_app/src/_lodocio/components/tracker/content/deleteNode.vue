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
  <div class="mr-2">
    <delete-button @deleted="deleteNode"/>
  </div>
</template>

<script setup lang="ts">
import DeleteButton from "@/components/common/deleteButton.vue";
import type {TrackerNode} from "@/_lodocio/api/interface/tracker";
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import {useToast} from "primevue/usetoast";
import {useAppStore} from "@/stores/app";
import {ref} from "vue";
import {deleteTrackerNode} from "@/_lodocio/api/command/tracker/deleteTrackerNode";

const props = defineProps<{ node: TrackerNode }>();
const trackerStore = useTrackerStore();
const toaster = useToast();
const appStore = useAppStore();
const isSaving = ref<boolean>(false);

async function deleteNode() {
  isSaving.value = true;
  await trackerStore.removeNodeFromStructure(props.node.uuid);
  await deleteTrackerNode({id: props.node.id});
  toaster.add({
    severity: "success",
    summary: trackerStore.tracker?.code + "-" + props.node.artefactId + " deleted.",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await trackerStore.reloadTracker();
  isSaving.value = false;
}

</script>

<style scoped>

</style>