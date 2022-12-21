<template>
  <div id="sequenceModule">
    <table class="w-full">
      <Draggable
          v-model="modelStore.project.queries"
          tag="tbody"
          item-key="name"
          handle=".handle"
          @end="saveQueryOrder"
          ghost-class="ghost">
        <template #item="{ element }">
          <tr class="border-b-[1px] border-gray-300">
            <td width="5%">
              <div class="mt-1 text-gray-200 hover:text-green-600 cursor-move mr-2">
                <i class="pi pi-bars handle"></i>
              </div>
            </td>
            <td width="15%" align="center">
              <status-badge-small :id="'Q-'+element.id" :status="element.documentor.status"/>
            </td>
            <td class="text-sm ml-2">
              <span class="text-gray-400">
                {{ element.domainModel.module.name }} » {{ element.domainModel.name }} »
              </span>
              <span class="font-semibold">{{ element.name }}</span>
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
import type {OrderQueryCommand} from "@/api/command/interface/queryCommands";
import {orderQueries} from "@/api/command/model/orderQuery";

const modelStore = useModelStore();
const toaster = useToast();

const sequenceQueries = computed((): OrderQueryCommand => {
  if (modelStore.project) {
    let sequence = [];
    for (let i = 0; i < modelStore.project.queries.length; i++) {
      sequence.push(modelStore.project.queries[i].id);
    }
    return {sequence: sequence};
  } else {
    return {sequence: []};
  }
});

async function saveQueryOrder() {
  await orderQueries(sequenceQueries.value);
  toaster.add({
    severity: "success",
    summary: "Query order saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadProject();
}

</script>