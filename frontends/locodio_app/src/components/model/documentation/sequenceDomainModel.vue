<template>
  <div id="sequenceModule">
    <table class="w-full">
      <Draggable
          v-model="modelStore.project.domainModels"
          tag="tbody"
          item-key="name"
          handle=".handle"
          @end="saveDomainModelOrder"
          ghost-class="ghost">
        <template #item="{ element }">
          <tr class="border-b-[1px] border-gray-300">
            <td width="5%">
              <div class="mt-1 text-gray-200 hover:text-green-600 cursor-move mr-2">
                <i class="pi pi-bars handle"></i>
              </div>
            </td>
            <td width="15%" align="center">
              <status-badge-small :id="'DM-'+element.id" :status="element.documentor.status"/>
            </td>
            <td class="text-sm ml-2">
              <span class="text-gray-400">
              {{element.module.name}} »
              </span>
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
import type {OrderDomainModelCommand} from "@/api/command/interface/domainModelCommands";
import {orderDomainModels} from "@/api/command/model/orderDomainModel";

const modelStore = useModelStore();
const toaster = useToast();

const sequenceDomainModels = computed((): OrderDomainModelCommand => {
  if (modelStore.project) {
    let sequence = [];
    for (let i = 0; i < modelStore.project.domainModels.length; i++) {
      sequence.push(modelStore.project.domainModels[i].id);
    }
    return {sequence: sequence};
  } else {
    return {sequence: []};
  }
});

async function saveDomainModelOrder() {
  await orderDomainModels(sequenceDomainModels.value);
  toaster.add({
    severity: "success",
    summary: "Model order saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadProject();
}

</script>