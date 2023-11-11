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
  <tr id="Component_Edit_Tracker_Node_Status"
      class="border-b-[1px] border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900"
      v-on:keyup.enter="save" v-on:keyup.esc="viewForm">

    <!-- view mode ------------------------------------------------- -->
    <td v-if="isView">
      <div class="flex mt-1 mb-1 mr-2">
        <div class="mt-2.5 ml-2 text-gray-200 hover:text-green-600 cursor-move mr-2 dark:text-gray-600">
          <i class="pi pi-bars handle"></i>
        </div>
        <edit-button @click="editForm" class="mt-1.5"/>
      </div>
    </td>
    <td v-if="isView" colspan="2">
      <div class="text-white rounded-full py-1 px-3 font-bold w-48 text-sm text-center"
           :style="'background-color:#'+item.color+''">
        {{ item.name }}
      </div>
    </td>
    <td v-if="isView">
      <div class="ml-2"><i class="pi pi-check" v-if="(item.isStart)"></i></div>
    </td>
    <td v-if="isView">
      <div class="ml-2"><i class="pi pi-check" v-if="(item.isFinal)"></i></div>
    </td>
    <td v-if="isView" align="right">
      <div v-if="item.usages == 0" class="ml-1 mr-2 mt-2">
        <delete-button @deleted="deleteItem"></delete-button>
      </div>
    </td>

    <!-- edit mode ------------------------------------------------- -->
    <td v-if="!isView" colspan="2">
      <span class="p-input-icon-right w-full ml-2">
        <InputText class="w-full p-inputtext-sm"
                   placeholder="name"
                   v-model="commandEdit.name"/>
        <i v-if="!vStatusEdit$.name.$invalid" class="pi pi-check text-green-600"/>
        <i v-if="vStatusEdit$.name.$invalid" class="pi pi-times text-red-600"/>
      </span>
    </td>
    <td v-if="!isView">
      <div class="pl-2 mt-1">
        <ColorPicker v-model="commandEdit.color"></ColorPicker>
      </div>
    </td>
    <td v-if="!isView">
      <div class="pl-2 mt-1">
        <InputSwitch v-model="commandEdit.isStart"/>
      </div>
    </td>
    <td v-if="!isView">
      <div class="pl-2">
        <InputSwitch v-model="commandEdit.isFinal"/>
      </div>
    </td>
    <td v-if="!isView" colspan="2">
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
        <div class="mt-0.5 mr-2">
          <close-button @click="viewForm"></close-button>
        </div>
      </div>
    </td>

  </tr>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import {useToast} from "primevue/usetoast";
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import EditButton from "@/components/common/editButton.vue";
import CloseButton from "@/components/common/closeButton.vue";
import DeleteButton from "@/components/common/deleteButton.vue";
import type {ChangeTrackerNodeStatusCommand} from "@/_lodocio/api/command/tracker/changeTrackerNodeStatus";
import {changeTrackerNodeStatus} from "@/_lodocio/api/command/tracker/changeTrackerNodeStatus";
import {deleteTrackerNodeStatus} from "@/_lodocio/api/command/tracker/deleteTrackerNodeStatus";
import {useAppStore} from "@/stores/app";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import type {TrackerNodeStatus} from "@/_lodocio/api/interface/tracker";

// -- props
const props = defineProps<{
  item: TrackerNodeStatus,
}>();

const isView = ref<boolean>(true);
const isSaving = ref<boolean>(false);
const appStore = useAppStore();
const projectStore = useDocProjectStore();

const toaster = useToast();
const commandEdit = ref<ChangeTrackerNodeStatusCommand>({
  id: props.item.id,
  name: props.item.name,
  color: props.item.color,
  isStart: props.item.isStart,
  isFinal: props.item.isFinal,
});

// -- mounted

onMounted((): void => {
  vStatusEdit$.value.$touch();
});

// -- validation

const rules = {
  name: {required},
};
const vStatusEdit$ = useVuelidate(rules, commandEdit);

// -- functions

function editForm() {
  isView.value = false;
  commandEdit.value.id = props.item.id;
  commandEdit.value.name = props.item.name;
  commandEdit.value.color = props.item.color;
  commandEdit.value.isFinal = props.item.isFinal;
}

function viewForm() {
  isView.value = true;
}

async function save() {
  vStatusEdit$.value.$touch();
  if (!vStatusEdit$.value.$invalid) {
    isSaving.value = true;
    await changeTrackerNodeStatus(commandEdit.value);
    toaster.add({
      severity: "success",
      summary: "Status changed",
      detail: "",
      life: appStore.toastLifeTime,
    });
    await projectStore.reloadTrackerDetail();
    isSaving.value = false;
    isView.value = true;
  }
}

async function deleteItem() {
  isSaving.value = true;
  await deleteTrackerNodeStatus({id: props.item.id});
  toaster.add({
    severity: "success",
    summary: "Status deleted.",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await projectStore.reloadTrackerDetail();
  isSaving.value = false;
}

</script>
