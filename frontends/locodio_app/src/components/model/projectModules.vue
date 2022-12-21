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
  <div id="projectModules">
    <Fieldset legend="Modules">
      <div class="text-sm mt-2">
        <table>
          <thead>
          <tr class="border-b-[1px]">
            <th width="10%">&nbsp;</th>
            <th width="45%">Name</th>
            <th width="45%">Namespace</th>
            <th width="5%">Status</th>
            <th>&nbsp;</th>
          </tr>
          </thead>
          <Draggable
              v-model="modelStore.project.modules"
              tag="tbody"
              item-key="name"
              handle=".handle"
              @end="saveModuleOrder"
              ghost-class="ghost">
            <template #item="{ element }">
              <edit-module :item="element"/>
            </template>
          </Draggable>
          <add-module/>
        </table>
      </div>
    </Fieldset>
  </div>
</template>

<script setup lang="ts">
import Draggable from "vuedraggable";
import {useModelStore} from "@/stores/model";
import EditModule from "@/components/model/editModule.vue";
import AddModule from "@/components/model/addModule.vue";
import {computed} from "vue";
import type {OrderModuleCommand} from "@/api/command/interface/modelConfiguration";
import {orderModule} from "@/api/command/model/orderModule";
import {useToast} from "primevue/usetoast";

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