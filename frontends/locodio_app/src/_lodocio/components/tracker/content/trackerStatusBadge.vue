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
  <div id="statusBadgeNode" class="flex gap-2">
    <div class="bg-gray-300 rounded-full flex flex-none w-46 dark:bg-gray-600">

      <!-- artefact id -->
      <div @click="selectNode"
           class="w-16 text-center font-bold py-0.5 mt-1 cursor-pointer text-xs">
        {{ trackerStore.tracker.code }}-{{ nodeRef.artefactId }}
      </div>

      <!-- status -->
      <div :style="'background-color:#'+nodeRef.status.color"
           @click="toggle"
           aria-haspopup="true"
           aria-controls="status_menu"
           class="rounded-full w-32 text-white font-bold px-2 py-0.5 flex flex-row-reverse cursor-pointer">
        <div
            v-if="!isSaving"
            class="cursor-pointer">
          <font-awesome-icon icon="fa-solid fa-circle-chevron-down"/>
        </div>
        <div v-else style="font-size:.9em;">
          <font-awesome-icon icon="fa-solid fa-circle-notch" class="pi-spin"/>
        </div>

        <!-- menu -->
        <Menu id="status_menu" ref="menu" :model="availableStatus" :popup="true">
          <template #item="{item}">
            <div class="p-2 cursor-pointer" @click="changeStatus(item.id)">
              <div class="text-white rounded-full py-0.5 px-3 w-48 text-sm text-center"
                   :style="'background-color:#'+item.color+''">
                {{ item.name }}
              </div>
            </div>
          </template>
        </Menu>

        <!-- status label -->
        <div class="cursor-pointer text-center mx-auto text-xs mt-1">
          {{ nodeRef.status.name }}
        </div>

      </div>
    </div>
  </div>

</template>

<script setup lang="ts">
import {computed, ref, toRef} from "vue";
import {useToast} from "primevue/usetoast";
import type {TrackerNode} from "@/_lodocio/api/interface/tracker";
import {useAppStore} from "@/stores/app";
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import {changeStatusNode} from "@/_lodocio/api/command/tracker/changeStatusNode";

// -- props & variables
const props = defineProps<{ node: TrackerNode }>();
const nodeRef = toRef(props, 'node');
const menu = ref();
const isSaving = ref<boolean>(false);
const appStore = useAppStore();
const trackerStore = useTrackerStore();
const toaster = useToast();
const emit = defineEmits(["clicked"]);

const toggle = (event: any) => {
  menu.value.toggle(event);
  emit('clicked');
};

function selectNode() {
  trackerStore.selectNode(nodeRef.value.id);
}

const availableStatus = computed(() => {
  let status = [];
  if (trackerStore.tracker && trackerStore.tracker.workflow) {
    for (const statusItem of trackerStore.tracker.workflow) {
      for (const flowOutStatus of nodeRef.value.status.flowOut) {
        if (statusItem.id === flowOutStatus) {
          status.push(statusItem);
        }
      }
    }
  }
  return status;
});

async function changeStatus(statusId: number) {
  console.log('-- change status');
  isSaving.value = true;
  let command = {id: nodeRef.value.id, trackerNodeStatusId: statusId};
  let result = await changeStatusNode(command);
  toaster.add({severity: "success", summary: "Status changed", detail: "", life: appStore.toastLifeTime});
  await trackerStore.reloadTracker();
  if (nodeRef.value.id === trackerStore.trackerNodeId) {
    await trackerStore.reloadTrackerNode();
  }
  isSaving.value = false;
}

</script>