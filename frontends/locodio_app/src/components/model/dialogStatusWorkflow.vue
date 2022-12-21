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
  <div id="dialogStatusWorkflow" style="min-width: 800px;">
    <div class="flowWrapper border-2">
      <VueFlow
          :default-edge-options="{ type: 'smoothstep' }"
          :default-zoom="1"
          :min-zoom="0.2"
          :max-zoom="4"
          :apply-default="false"
          :snap-to-grid="true"
          v-model="elements">
        <!-- -- custom node -->
        <template #node-status="{ data, label }">
          <status-node :data="data" :label="label"/>
        </template>
        <!-- -- custom draggin' line -->
        <template #connection-line="{ sourceX, sourceY, targetX, targetY }">
          <custom-connection-line :source-x="sourceX" :source-y="sourceY" :target-x="targetX" :target-y="targetY"/>
        </template>
        <!-- -- elements -->
        <Background pattern-color="#aaa" gap="10"/>
        <Controls/>
      </VueFlow>

    </div>
    <!-- -- save button -->
    <div class="mt-2">
      <Button
          v-if="!isSaving"
          @click="saveWorkflow"
          class="p-button-sm p-button-success w-64"
          icon="pi pi-save"
          label="Save"/>
      <Button v-else
              label="Save"
              class="p-button-sm p-button-success w-64"
              icon="pi pi-spinner pi-spin"
              disabled
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import {Background, Controls} from '@vue-flow/additional-components';
import {useVueFlow, VueFlow} from '@vue-flow/core';
import {computed, onMounted, ref} from "vue";
import StatusNode from "@/components/model/workflow/statusNode.vue";
import {getModelStatusWorkflow} from "@/api/query/model/getModelStatusWorkflow";
import {useModelStore} from "@/stores/model";
import CustomConnectionLine from "@/components/model/workflow/customConnectionLine.vue";
import type {ModelStatusWorkflowCommand} from "@/api/command/interface/modelConfiguration";
import {saveModelStatusWorkflow} from "@/api/command/model/saveModelStatusWorkflow";
import {useToast} from "primevue/usetoast";

const modelStore = useModelStore();
const toaster = useToast();
const {onConnect, addEdges, onNodesChange, applyNodeChanges, onEdgesChange, applyEdgeChanges} = useVueFlow();
const emit = defineEmits(["saved"]);
const selectedEdge = ref<string>('');
const elements = ref([])

onConnect((params) => addEdges([params]));

onMounted((): void => {
  void getWorkflow();
});

async function getWorkflow() {
  let workflow = await getModelStatusWorkflow(modelStore.projectId);
  elements.value = JSON.parse(JSON.stringify(workflow.workflow));
}

const isSaving = ref<boolean>(false);

async function saveWorkflow() {
  isSaving.value = true;
  await saveModelStatusWorkflow(modelStore.projectId, command.value);
  toaster.add({
    severity: "success",
    summary: "Workflow saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  isSaving.value = false;
  emit("saved");
}

const command = computed((): ModelStatusWorkflowCommand => {
  let result = [];
  for (const element of elements.value) {
    if (element.type === 'status') {
      let status = {};
      status.id = element.id;
      status.label = element.label;
      status.position = element.position;
      status.flowIn = findFlowIn(element.id);
      status.flowOut = findFlowOut(element.id);
      result.push(status);
    }
  }
  return {workflow: result};
});

function findFlowIn(id: string): Array<string> {
  let result = [];
  for (const element of elements.value) {
    if (element.target && element.source) {
      if (element.target === id) {
        result.push(element.source);
      }
    }
  }
  return result;
}

function findFlowOut(id: string): Array<string> {
  let result = [];
  for (const element of elements.value) {
    if (element.target && element.source) {
      if (element.source === id) {
        result.push(element.target);
      }
    }
  }
  return result;
}

// -- workflow ---------------------------------------------------------------------------------------

onEdgesChange((changes) => {
  const nextChanges = [];
  for (let nextChange of changes) {
    if (nextChange.type == 'add') {
      if (nextChange.item.sourceHandle !== nextChange.item.targetHandle) {
        nextChange.item.style = {stroke: '#' + nextChange.item.targetNode.data.color, strokeWidth: 4};
        nextChange.item.data = {color: nextChange.item.targetNode.data.color}
        nextChanges.push(nextChange);
      }
    }
    if (nextChange.type == 'select') {
      if (nextChange.selected) {
        selectEdge(nextChange.id);
      } else {
        deselectEdge(nextChange.id);
      }
      nextChanges.push(nextChange);
    }
    if (nextChange.type === 'remove') {
      if (nextChange.id === selectedEdge.value) {
        nextChanges.push(nextChange);
      }
    }
  }
  applyEdgeChanges(nextChanges);
})

onNodesChange((changes) => {
  const nextChanges = [];
  for (let nextChange of changes) {
    if (nextChange.type === 'select') {
      selectedEdge.value = '0';
    }
    if (nextChange.type !== 'remove') {
      nextChanges.push(nextChange);
    }
  }
  applyNodeChanges(nextChanges);
})

function selectEdge(id: string) {
  selectedEdge.value = id;
  for (const element of elements.value) {
    if (element.id === id) {
      element.animated = true;
    }
  }
}

function deselectEdge(id) {
  for (const element of elements.value) {
    if (element.id === id) {
      element.animated = false;
    }
  }
}

</script>

<style scoped>

.flowWrapper {
  display: block;
  width: 100%;
  height: 400px;
}

.vue-flow__container .selected .status-node {
  border: 2px solid black !important;
}

</style>