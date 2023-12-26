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
  <div id="wikiNodeStatus" class="border-b-[1px] border-gray-300 dark:border-gray-600">
    <div>
      <div class="text-sm">
        <table cellpadding="2" border="1" v-if="projectStore.wikiDetail">
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
              v-model="projectStore.wikiDetail.workflow"
              tag="tbody"
              item-key="name"
              handle=".handle"
              @end="saveStatusOrder"
              ghost-class="ghost">
            <template #item="{ element }">
              <edit-wiki-status :item="element"/>
            </template>
          </Draggable>
          <add-wiki-status @workflow="showWorkflowDialog"/>
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
    <dialog-wiki-status-workflow/>
  </Dialog>

</template>

<script setup lang="ts">
import Draggable from "vuedraggable";
import {computed, ref} from "vue";
import {useToast} from "primevue/usetoast";
import {useAppStore} from "@/stores/app";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import type {OrderWikiNodeStatusCommand} from "@/_lodocio/api/command/wiki/orderWikiNodeStatus";
import {orderWikiNodeStatus} from "@/_lodocio/api/command/wiki/orderWikiNodeStatus";
import DialogWikiStatusWorkflow from "@/_lodocio/components/wiki/configuration/workflow/dialogWikiStatusWorkflow.vue";
import EditWikiStatus from "@/_lodocio/components/wiki/configuration/editWikiStatus.vue";
import AddWikiStatus from "@/_lodocio/components/wiki/configuration/addWikiStatus.vue";


const appStore = useAppStore();
const projectStore = useDocProjectStore();
const toaster = useToast();
const showWorkflow = ref<boolean>(false);

function showWorkflowDialog() {
  showWorkflow.value = true;
}

const sequenceModules = computed((): OrderWikiNodeStatusCommand => {
  if (projectStore.wikiDetail) {
    let sequence = [];
    for (let i = 0; i < projectStore.wikiDetail.workflow.length; i++) {
      sequence.push(projectStore.wikiDetail.workflow[i].id);
    }
    return {sequence: sequence};
  } else {
    return {sequence: []};
  }
});

async function saveStatusOrder() {
  await orderWikiNodeStatus(sequenceModules.value);
  toaster.add({
    severity: "success",
    summary: "Workflow order saved",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await projectStore.reloadWikiDetail();
}
</script>