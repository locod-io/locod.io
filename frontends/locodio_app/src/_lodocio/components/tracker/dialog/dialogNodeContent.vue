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
  <div style="width:700px;" class="p-2">

    <div class="flex gap-2 flex-grow mt-1" v-on:keyup.enter="saveName">
      <div class="flex-grow">
        <InputText class="p-inputtext-sm w-full" v-model="changeNameCommand.name"/>
      </div>
      <div class="flex-none">
        <Button class="p-button-sm p-button-success p-button-icon"
                icon="pi pi-save"
                @click="saveName"
                v-if="!isSaving"/>
        <Button v-else
                class="p-button-sm p-button-success p-button-icon"
                icon="pi pi-spin pi-spinner"
                disabled/>
      </div>
    </div>
    <div class="flex text-sm mt-2">
      <div class="flex-grow">
        <extended-editor v-model="changeDescriptionCommand.description"/>
      </div>
    </div>
    <div class="flex gap-2 p-2 h-12">
      <div>
        <Button class="p-button-sm p-button-success p-button-icon"
                icon="pi pi-save"
                @click="saveDescription"
                v-if="!isSaving"/>
        <Button v-else
                class="p-button-sm p-button-success p-button-icon"
                icon="pi pi-spin pi-spinner"
                disabled/>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import {ref, toRef, watch} from "vue";
import type {TrackerNode} from "@/_lodocio/api/interface/tracker";
import {useAppStore} from "@/stores/app";
import {useToast} from "primevue/usetoast";
import type {ChangeNodeNameCommand} from "@/_lodocio/api/command/tracker/changeNodeName";
import {changeNodeName} from "@/_lodocio/api/command/tracker/changeNodeName";
import type {ChangeNodeDescriptionCommand} from "@/_lodocio/api/command/tracker/changeNodeDescription";
import {changeNodeDescription} from "@/_lodocio/api/command/tracker/changeNodeDescription";
import SimpleEditor from "@/_common/editor/simpleEditor.vue";
import ExtendedEditor from "@/_common/editor/extendedEditor.vue";

const props = defineProps<{ node: TrackerNode }>();
const trackerStore = useTrackerStore();
const appStore = useAppStore();
const isSaving = ref<boolean>(false);
const nodeRef = toRef(props, 'node');
const toaster = useToast();

watch(nodeRef, (newValue): void => {
  changeNameCommand.value = {
    id: newValue.id,
    name: newValue.name
  }
  changeDescriptionCommand.value = {
    id: newValue.id,
    description: newValue.description
  }
});

const changeNameCommand = ref<ChangeNodeNameCommand>({
  id: nodeRef.value.id,
  name: nodeRef.value.name
});

async function saveName() {
  isSaving.value = true;
  await changeNodeName(changeNameCommand.value);
  toaster.add({
    severity: "success",
    summary: "Name changed",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await trackerStore.reloadTrackerNode();
  await trackerStore.reloadTracker();
  isSaving.value = false;
}

const changeDescriptionCommand = ref<ChangeNodeDescriptionCommand>({
  id: nodeRef.value.id,
  description: nodeRef.value.description
});

async function saveDescription() {
  isSaving.value = true;
  await changeNodeDescription(changeDescriptionCommand.value);
  toaster.add({
    severity: "success",
    summary: "Description changed",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await trackerStore.reloadTrackerNode();
  await trackerStore.reloadTracker();
  isSaving.value = false;
}

</script>

<style scoped>

</style>