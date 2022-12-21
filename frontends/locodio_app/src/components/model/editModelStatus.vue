<template>
  <tr id="Component_Edit_Model_Status"
      class="border-b-[1px]"
      v-on:keyup.enter="save" v-on:keyup.esc="viewForm">

    <!-- view mode ------------------------------------------------- -->
    <td v-if="isView">
      <div class="flex mt-1 mb-1 mr-2">
        <div class="mt-1 text-gray-200 hover:text-green-600 cursor-move mr-2">
          <i class="pi pi-bars handle"></i>
        </div>
        <edit-button @click="editForm"/>
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
      <div v-if="item.usages == 0" class="ml-1">
        <delete-button @deleted="deleteItem"></delete-button>
      </div>
    </td>

    <!-- edit mode ------------------------------------------------- -->
    <td v-if="!isView" colspan="2">
      <span class="p-input-icon-right w-full">
        <InputText class="w-full p-inputtext-sm"
                   placeholder="name"
                   v-model="commandEdit.name"/>
        <i v-if="!vStatusEdit$.name.$invalid" class="pi pi-check text-green-600"/>
        <i v-if="vStatusEdit$.name.$invalid" class="pi pi-times text-red-600"/>
      </span>
    </td>
    <td v-if="!isView">
      <div class="pl-2">
        <ColorPicker v-model="commandEdit.color"></ColorPicker>
      </div>
    </td>
    <td v-if="!isView">
      <div class="pl-2">
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
          <save-button @click="save"></save-button>
        </div>
        <div class="mt-0.5">
          <close-button @click="viewForm"></close-button>
        </div>
      </div>
    </td>

  </tr>
</template>

<script setup lang="ts">
import type {ModelStatus} from "@/api/query/interface/model";
import {onMounted, ref} from "vue";
import {useModelStore} from "@/stores/model";
import {useToast} from "primevue/usetoast";
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import type {ChangeModelStatusCommand} from "@/api/command/interface/modelConfiguration";
import EditButton from "@/components/common/editButton.vue";
import SaveButton from "@/components/common/saveButton.vue";
import CloseButton from "@/components/common/closeButton.vue";
import {changeModelStatus} from "@/api/command/model/changeModelStatus";
import {deleteModule} from "@/api/command/model/deleteModule";
import {deleteModelStatus} from "@/api/command/model/deleteModelStatus";
import DeleteButton from "@/components/common/deleteButton.vue";

// -- props
const props = defineProps<{
  item: ModelStatus,
}>();

const isView = ref<boolean>(true);
const isSaving = ref<boolean>(false);
const modelStore = useModelStore();
const toaster = useToast();
const commandEdit = ref<ChangeModelStatusCommand>({
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
    await changeModelStatus(commandEdit.value);
    toaster.add({
      severity: "success",
      summary: "Status changed",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.loadModelStatus();
    isView.value = true;
    isSaving.value = false;
  }
}

async function deleteItem() {
  isSaving.value = true;
  await deleteModelStatus({id: props.item.id});
  toaster.add({
    severity: "success",
    summary: "Status deleted.",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadProject();
  isSaving.value = false;
}

</script>
