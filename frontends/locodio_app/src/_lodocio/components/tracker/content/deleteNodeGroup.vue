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
  <div v-if="group.nodes.length === 0 && group.groups.length === 0">
    <delete-button @deleted="deleteGroup"/>
  </div>
</template>

<script setup lang="ts">
import DeleteButton from "@/components/common/deleteButton.vue";
import type {TrackerNodeGroup} from "@/_lodocio/api/interface/tracker";
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import {useToast} from "primevue/usetoast";
import {useAppStore} from "@/stores/app";
import {ref} from "vue";
import {deleteTrackerNodeGroup} from "@/_lodocio/api/command/tracker/deleteTrackerNodeGroup";

const props = defineProps<{ group: TrackerNodeGroup }>();
const trackerStore = useTrackerStore();
const toaster = useToast();
const appStore = useAppStore();
const isSaving = ref<boolean>(false);

async function deleteGroup() {
  isSaving.value = true;
  await trackerStore.removeGroupFromStructure(props.group.uuid);
  await deleteTrackerNodeGroup({id: props.group.id});
  toaster.add({
    severity: "success",
    summary: "Group deleted.",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await trackerStore.reloadTracker();
  isSaving.value = false;
}

</script>

<style scoped>

</style>