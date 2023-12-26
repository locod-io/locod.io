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
  <div :id="'content-group-'+groupRef.uuid"
       class="dark:bg-gray-800 bg-gray-100">

    <div class="flex gap-2 font-bold p-2">
      <div v-if="groupRef.level !== 0" class="flex-none">
        <span v-for="i in groupRef.level">
          &nbsp;&nbsp;&nbsp;&nbsp;
        </span>
      </div>
      <div class="flex-none">
        <div v-if="!groupRef.isOpen" @click="openGroup" class="cursor-pointer">
          <font-awesome-icon :icon="['fas', 'circle-right']"/>
        </div>
        <div v-if="groupRef.isOpen" @click="closeGroup" class="cursor-pointer">
          <font-awesome-icon :icon="['fas', 'circle-down']"/>
        </div>
      </div>
      <div>{{ groupRef.number }}.</div>

      <!-- render the group name -->
      <div v-if="!editNameMode" class="flex-grow line-clamp-1" @dblclick="editName">
        {{ groupRef.name }}
      </div>

      <!-- edit the group name -->
      <div v-else class="flex gap-2 flex-grow" v-on:keyup.enter="saveName" v-on:keyup.esc="closeEditName">
        <div class="flex-grow">
          <InputText
              :id="'node-group-'+groupRef.uuid"
              class="p-inputtext-sm w-full"
              v-model="changeNameCommand.name"/>
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
        <div class="flex-none">
          <close-button @click="closeEditName"></close-button>
        </div>
      </div>

      <!-- if closed show some extra info -->
      <div v-if="!groupRef.isOpen" class="flex-none line-clamp-1">
        <div class="text-xs mt-1 font-normal">
          {{ groupRef.nodes.length + groupRef.groups.length }} items
        </div>
      </div>

      <delete-node-group :group="group"/>

      <div class="mr-2" v-if="group.nodes.length !== 0 || group.groups.length !== 0">
        <export-button @click="showContentDialogWikiMarkdownFn"/>
      </div>

    </div>

  </div>

  <!-- -- markdown exporter for this group -->
  <Dialog position="top"
          v-model:visible="showContentDialogGroupMarkdown"
          :modal="true"
  >
    <template #header>
      <div class="flex gap-2">
        <div>{{ groupRef.name }}</div>
      </div>
    </template>
    <dialog-group-mark-down :group="groupRef"/>
  </Dialog>

</template>

<script setup lang="ts">
import {useWikiStore} from "@/_lodocio/stores/wiki";
import type {WikiNodeGroup} from "@/_lodocio/api/interface/wiki";
import {ref, toRef, watch} from "vue";
import {useToast} from "primevue/usetoast";
import {useAppStore} from "@/stores/app";
import type {ChangeGroupNameCommand} from "@/_lodocio/api/command/wiki/changeGroupName";
import {changeGroupName} from "@/_lodocio/api/command/wiki/changeGroupName";
import CloseButton from "@/components/common/closeButton.vue";
import DeleteNodeGroup from "@/_lodocio/components/wiki/content/deleteNodeGroup.vue";
import DialogGroupMarkDown from "@/_lodocio/components/wiki/dialog/dialogGroupMarkDown.vue";
import MarkdownButton from "@/_common/buttons/markdownButton.vue";
import ExportButton from "@/_common/buttons/exportButton.vue";

const props = defineProps<{ group: WikiNodeGroup }>();
const wikiStore = useWikiStore();
const toaster = useToast();
const appStore = useAppStore();
const groupRef = toRef(props, 'group');
const editNameMode = ref<boolean>(false);
const isSaving = ref<boolean>(false);

// -- change the name

watch(groupRef, (newValue): void => {
  changeNameCommand.value = {
    id: newValue.id,
    name: newValue.name
  }
});

const changeNameCommand = ref<ChangeGroupNameCommand>({
  id: groupRef.value.id,
  name: groupRef.value.name
});

function editName() {
  editNameMode.value = true;
  setTimeout(function () {
    document.getElementById("node-group-" + groupRef.value.uuid).focus();
  }, 200);
}

function closeEditName() {
  editNameMode.value = false;
}

async function saveName() {
  isSaving.value = true;
  await changeGroupName(changeNameCommand.value);
  toaster.add({
    severity: "success",
    summary: "Name changed",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await wikiStore.reloadWiki();
  isSaving.value = false;
  closeEditName();
}

// -- open and close group

function closeGroup() {
  groupRef.value.isOpen = false;
  wikiStore.renumberTree();
}

function openGroup() {
  groupRef.value.isOpen = true;
  wikiStore.renumberTree();
}

// -- dialog window to render markdown for this group only

const showContentDialogGroupMarkdown = ref<boolean>(false);

function showContentDialogWikiMarkdownFn(): void {
  showContentDialogGroupMarkdown.value = true;
}

</script>

<style scoped>

</style>