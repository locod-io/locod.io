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
  <div id="trackerNodeStatus" class="border-b-[1px] border-gray-300 dark:border-gray-600">
    <div>
      <div class="text-sm">
        <table cellpadding="2" border="1" v-if="projectStore.trackerDetail">
          <thead>
          <tr class="border-b-[1px] border-gray-300 dark:border-gray-600 h-8">
            <th width="5%">&nbsp;</th>
            <th width="50%">Name</th>
            <th width="10%">&nbsp;</th>
            <th width="20%">Start</th>
            <th width="20%">Final</th>
            <th>&nbsp;</th>
          </tr>
          </thead>
          <Draggable
              v-model="projectStore.trackerDetail.workflow"
              tag="tbody"
              item-key="name"
              handle=".handle"
              @end="saveStatusOrder"
              ghost-class="ghost">
            <template #item="{ element }">
              <edit-tracker-status :item="element"/>
            </template>
          </Draggable>
          <add-tracker-status @workflow="showWorkflowDialog"/>
        </table>
      </div>
    </div>
  </div>

  <!-- -- dialog status workflow -->
  <Dialog
      v-model:visible="showWorkflow"
      header="Status workflow"
      :modal="true"
  >
    <dialog-tracker-status-workflow/>
  </Dialog>

</template>

<script setup lang="ts">
import Draggable from "vuedraggable";
import {computed, ref} from "vue";
import {useToast} from "primevue/usetoast";
import {useAppStore} from "@/stores/app";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import type {OrderTrackerNodeStatusCommand} from "@/_lodocio/api/command/tracker/orderTrackerNodeStatus";
import {orderTrackerNodeStatus} from "@/_lodocio/api/command/tracker/orderTrackerNodeStatus";
import EditTrackerStatus from "@/_lodocio/components/trackers/editTrackerStatus.vue";
import AddTrackerStatus from "@/_lodocio/components/trackers/addTrackerStatus.vue";
import DialogTrackerStatusWorkflow from "@/_lodocio/components/trackers/workflow/dialogTrackerStatusWorkflow.vue";

const appStore = useAppStore();
const projectStore = useDocProjectStore();
const toaster = useToast();
const showWorkflow = ref<boolean>(false);

function showWorkflowDialog() {
  showWorkflow.value = true;
}

const sequenceModules = computed((): OrderTrackerNodeStatusCommand => {
  if (projectStore.trackerDetail) {
    let sequence = [];
    for (let i = 0; i < projectStore.trackerDetail.workflow.length; i++) {
      sequence.push(projectStore.trackerDetail.workflow[i].id);
    }
    return {sequence: sequence};
  } else {
    return {sequence: []};
  }
});

async function saveStatusOrder() {
  await orderTrackerNodeStatus(sequenceModules.value);
  toaster.add({
    severity: "success",
    summary: "Workflow order saved",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await projectStore.reloadTrackerDetail();
}
</script>