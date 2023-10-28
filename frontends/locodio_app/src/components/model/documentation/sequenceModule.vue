<template>
  <div id="sequenceModule">
    <table class="w-full">
    <Draggable
        v-model="modelStore.project.modules"
        tag="tbody"
        item-key="name"
        handle=".handle"
        @end="saveModuleOrder"
        ghost-class="ghost">
      <template #item="{ element }">
        <tr class="border-b-[1px] border-gray-300 dark:border-gray-600">
          <td width="5%">
            <div class="mt-1 text-gray-200 hover:text-green-600 cursor-move mr-2">
              <i class="pi pi-bars handle"></i>
            </div>
          </td>
          <td width="15%" align="center">
            <status-badge-small :id="element.artefactId" :status="element.documentor.status"/>
          </td>
          <td class="text-sm ml-2">
            <span class="font-semibold">{{element.name}}</span>
          </td>
        </tr>
      </template>
    </Draggable>
    </table>
  </div>
</template>

<script setup lang="ts">
import {useModelStore} from "@/stores/model";
import Draggable from "vuedraggable";
import StatusBadgeSmall from "@/components/common/statusBadgeSmall.vue";
import {useToast} from "primevue/usetoast";
import {computed} from "vue";
import type {OrderModuleCommand} from "@/api/command/interface/modelConfiguration";
import {orderModule} from "@/api/command/model/orderModule";

const modelStore = useModelStore();
const toaster = useToast();

const sequenceModules = computed((): OrderModuleCommand => {
  if (modelStore.project) {
    let sequence = [];
    for (let i = 0; i < modelStore.project.modules.length; i++) {
      sequence.push(modelStore.project.modules[i].id);
    }
    return {sequence: sequence};
  } else {
    return {sequence: []};
  }
});

async function saveModuleOrder(){
  await orderModule(sequenceModules.value);
  toaster.add({
    severity: "success",
    summary: "Module order saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadProject();
}

</script>