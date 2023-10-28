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

  <tr id="Component_Edit_Field"
      class="border-b-[1px] border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900"
      v-on:keyup.enter="save" v-on:keyup.esc="viewForm">

    <!-- view mode ------------------------------------------------- -->
    <td v-if="isView">
      <div class="flex mt-1 mr-2 ml-2" v-if="!modelStore.isEnumFinal">
        <div class="mt-1.5 text-gray-200 hover:text-green-600 cursor-move mr-2 dark:text-gray-600">
          <i class="pi pi-bars handle"></i>
        </div>
        <div class="mt-0.5">
          <edit-button @click="editForm" v-if="!modelStore.isEnumFinal"/>
        </div>

      </div>
    </td>
    <td v-if="isView" class="pt-2 pb-2"><strong>{{ item.code }}</strong></td>
    <td v-if="isView">{{ item.value }}</td>
    <td v-if="isView" align="right" class="pr-2 pt-1">
      <delete-button @deleted="deleteItem" v-if="!modelStore.isEnumFinal"/>
    </td>

    <!-- edit mode ------------------------------------------------- -->
    <td v-if="!isView">&nbsp;</td>
    <td v-if="!isView">
      <span class="p-input-icon-right w-full">
        <InputText class="w-full p-inputtext-sm"
                   placeholder="name"
                   v-model="commandEdit.code"/>
        <i v-if="!v$.code.$invalid" class="pi pi-check text-green-600"/>
        <i v-if="v$.code.$invalid" class="pi pi-times text-red-600"/>
      </span>
    </td>
    <td v-if="!isView">
      <div class="pl-2">
        <span class="p-input-icon-right w-full">
          <InputText class="w-full p-inputtext-sm"
                     placeholder="value"
                     v-model="commandEdit.value"/>
          <i v-if="!v$.value.$invalid" class="pi pi-check text-green-600"/>
          <i v-if="v$.value.$invalid" class="pi pi-times text-red-600"/>
        </span>
      </div>
    </td>
    <td v-if="!isView">
      <div class="flex mt-1">
        <div class="ml-2">
          <Button class="p-button-sm p-button-success p-button-icon"
                  icon="pi pi-save"
                  @click="save"
                  v-if="!isSaving"/>
          <Button v-else
                  class="p-button-sm p-button-success p-button-icon"
                  icon="pi pi-spin pi-spinner"
                  disabled/>
        </div>
        <div class="ml-2">
          <close-button @click="viewForm"></close-button>
        </div>
      </div>
    </td>

  </tr>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import EditButton from "@/components/common/editButton.vue";
import SaveButton from "@/components/common/saveButton.vue";
import DeleteButton from "@/components/common/deleteButton.vue";
import CloseButton from "@/components/common/closeButton.vue";
import type {EnumOption} from "@/api/query/interface/model";
import {useModelStore} from "@/stores/model";
import type {ChangeEnumOptionCommand} from "@/api/command/interface/enumCommands";
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {changeEnumOption} from "@/api/command/model/changeEnumOption";
import {useToast} from "primevue/usetoast";
import {deleteEnumOption} from "@/api/command/model/deleteEnumOption";

// -- props

const props = defineProps<{
  item: EnumOption,
}>();

const isView = ref<boolean>(true);
const isSaving = ref<boolean>(false);
const modelStore = useModelStore();
const toaster = useToast();
const commandEdit = ref<ChangeEnumOptionCommand>({
  id: props.item.id,
  code: props.item.code,
  value: props.item.value,
});

// -- mounted

onMounted((): void => {
  v$.value.$touch();
});

// -- validation

const rules = {
  code: {required},
  value: {required},
};
const v$ = useVuelidate(rules, commandEdit);

// -- functions

function editForm() {
  isView.value = false;
  commandEdit.value.id = props.item.id;
  commandEdit.value.code = props.item.code;
  commandEdit.value.value = props.item.value;
}

function viewForm() {
  isView.value = true;
}

async function save() {
  v$.value.$touch();
  if (!v$.value.$invalid) {
    isSaving.value = true;
    await changeEnumOption(commandEdit.value);
    toaster.add({
      severity: "success",
      summary: "Option changed",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadEnum();
    isSaving.value = false;
    isView.value = true;
  }
}

async function deleteItem() {
  isSaving.value = true;
  await deleteEnumOption({id: props.item.id});
  toaster.add({
    severity: "success",
    summary: "Option deleted.",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadEnum();
  isSaving.value = false;
}

</script>