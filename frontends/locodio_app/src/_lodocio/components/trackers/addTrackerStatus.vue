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
  <tr id="Component_Add_Model_Status" class="h-12"
      v-on:keyup.enter="save"
      v-on:keyup.esc="viewForm">

    <!-- add button ------------------------------------------------------------------------------------------------ -->
    <td v-if="isView" colspan="2">
      <div class="flex ml-8 mt-3 ">
        <add-button @click="editForm"/>
      </div>
    </td>
    <td v-if="isView" colspan="4">
      <div class="text-right">
        <div @click="editWorkflow" class="cursor-pointer mr-4">
          <font-awesome-icon icon="fa-solid fa-diagram-project" class="mr-1" />
          Edit workflow
        </div>
      </div>
    </td>

    <!-- edit mode ------------------------------------------------------------------------------------------------- -->
    <td v-if="!isView" colspan="2">
      <span class="p-input-icon-right w-full ml-2">
        <InputText class="w-full p-inputtext-sm"
                   placeholder="name"
                   v-model="commandAdd.name"/>
        <i v-if="!vStatusAdd$.name.$invalid" class="pi pi-check text-green-600"/>
        <i v-if="vStatusAdd$.name.$invalid" class="pi pi-times text-red-600"/>
      </span>
    </td>
    <td v-if="!isView">
      <div class="pl-2">
        <ColorPicker v-model="commandAdd.color"></ColorPicker>
      </div>
    </td>
    <td v-if="!isView">
      <div class="pl-2 mt-0.5">
        <InputSwitch v-model="commandAdd.isStart"/>
      </div>
    </td>
    <td v-if="!isView">
      <div class="pl-2 mt-0.5">
        <InputSwitch v-model="commandAdd.isFinal"/>
      </div>
    </td>
    <td v-if="!isView">
      <div class="flex mt-2 mb-2">
        <div class="mr-2 ml-2">
          <Button class="p-button-sm p-button-success p-button-icon"
                  icon="pi pi-save"
                  @click="save"
                  v-if="!isSaving"/>
          <Button v-else
                  class="p-button-sm p-button-success p-button-icon"
                  icon="pi pi-spin pi-spinner"
                  disabled/>
        </div>
        <div class="mr-2">
          <close-button @click="viewForm"></close-button>
        </div>
      </div>
    </td>
  </tr>
</template>

<script setup lang="ts">
import CloseButton from "@/components/common/closeButton.vue";
import AddButton from "@/components/common/addButton.vue";
import {onMounted, ref} from "vue";
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {useToast} from "primevue/usetoast";
import {useAppStore} from "@/stores/app";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import type {AddTrackerNodeStatusCommand} from "@/_lodocio/api/command/tracker/addTrackerNodeStatus";
import {addTrackerNodeStatus} from "@/_lodocio/api/command/tracker/addTrackerNodeStatus";

// -- props
const isView = ref<boolean>(true);
const isSaving = ref<boolean>(false);
const appStore = useAppStore();
const projectStore = useDocProjectStore();

const toaster = useToast();
const commandAdd = ref<AddTrackerNodeStatusCommand>({
  trackerId: projectStore.selectedTrackerId,
  name: '',
  color: 'CCCCCC',
  isStart: false,
  isFinal: false,
});
const emit = defineEmits(["workflow"]);

// -- mounted
onMounted((): void => {
  vStatusAdd$.value.$touch();
});

function editWorkflow() {
  emit('workflow');
}

// -- validation
const rules = {name: {required}};
const vStatusAdd$ = useVuelidate(rules, commandAdd);

// -- functions

function editForm() {
  isView.value = false;
}

function viewForm() {
  isView.value = true;
  commandAdd.value.name = '';
  commandAdd.value.color = 'CCCCCC';
  commandAdd.value.isFinal = false;
}

async function save() {
  vStatusAdd$.value.$touch();
  if (!vStatusAdd$.value.$invalid) {
    isSaving.value = true;
    await addTrackerNodeStatus(commandAdd.value);
    toaster.add({
      severity: "success",
      summary: "Status added",
      detail: "",
      life: appStore.toastLifeTime,
    });
    await projectStore.reloadTrackerDetail()
    commandAdd.value.name = '';
    commandAdd.value.color = 'CCCCCC';
    commandAdd.value.isFinal = false;
    isSaving.value = false;
    isView.value = true;
  }
}

</script>