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
  <div id="projectStatus">
    <Fieldset legend="Status">
      <div class="text-sm mt-2">
        <table>
          <thead>
          <tr class="border-b-[1px]">
            <th width="10%">&nbsp;</th>
            <th width="50%">Name</th>
            <th width="10%">&nbsp;</th>
            <th width="20%">Start</th>
            <th width="20%">Final</th>
            <th>&nbsp;</th>
          </tr>
          </thead>
          <Draggable
              v-model="modelStore.modelStatus.collection"
              tag="tbody"
              item-key="name"
              handle=".handle"
              @end="saveStatusOrder"
              ghost-class="ghost">
            <template #item="{ element }">
              <edit-model-status :item="element"/>
            </template>
          </Draggable>
          <add-model-status @workflow="showWorkflowDialog"/>
        </table>
      </div>
    </Fieldset>
  </div>

  <!-- -- dialog status workflow -->
  <Dialog
      v-model:visible="showWorkflow"
      header="Status workflow"
      :modal="true"
  >
    <dialog-status-workflow @saved="(showWorkflow=false)"/>
  </Dialog>

</template>

<script setup lang="ts">
import Draggable from "vuedraggable";
import {useModelStore} from "@/stores/model";
import {computed, ref} from "vue";
import type {OrderModelStatusCommand} from "@/api/command/interface/modelConfiguration";
import {useToast} from "primevue/usetoast";
import {orderModelStatus} from "@/api/command/model/orderModelStatus";
import AddModelStatus from "@/components/model/addModelStatus.vue";
import EditModelStatus from "@/components/model/editModelStatus.vue";
import DialogStatusWorkflow from "@/components/model/dialogStatusWorkflow.vue";

const modelStore = useModelStore();
const toaster = useToast();
const showWorkflow = ref<boolean>(false);

function showWorkflowDialog() {
  showWorkflow.value = true;
}

const sequenceModules = computed((): OrderModelStatusCommand => {
  if (modelStore.project) {
    let sequence = [];
    for (let i = 0; i < modelStore.modelStatus.collection.length; i++) {
      sequence.push(modelStore.modelStatus.collection[i].id);
    }
    return {sequence: sequence};
  } else {
    return {sequence: []};
  }
});

async function saveStatusOrder() {
  await orderModelStatus(sequenceModules.value);
  toaster.add({
    severity: "success",
    summary: "Status order saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.loadModelStatus();
}
</script>