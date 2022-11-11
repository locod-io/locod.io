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
  <tr id="Component_Add_Field"
      v-on:keyup.enter="save"
      v-on:keyup.esc="viewForm">

    <!-- add button ------------------------------------------------------------------------------------------------ -->
    <td v-if="isView">
      <div class="flex mt-1 mb-1 mr-2 ml-4">
        <add-button @click="editForm"/>
      </div>
    </td>

    <!-- edit mode ------------------------------------------------------------------------------------------------- -->
    <td v-if="!isView">&nbsp;</td>
    <td v-if="!isView">
      <span class="p-input-icon-right w-full">
        <InputText class="w-full p-inputtext-sm"
                   placeholder="name"
                   v-model="commandAdd.code"/>
        <i v-if="!v$.code.$invalid" class="pi pi-check text-green-600"/>
        <i v-if="v$.code.$invalid" class="pi pi-times text-red-600"/>
      </span>
    </td>
    <td v-if="!isView">
      <div class="pl-2">
        <span class="p-input-icon-right w-full">
          <InputText class="w-full p-inputtext-sm"
                     placeholder="value"
                     v-model="commandAdd.value"/>
          <i v-if="!v$.value.$invalid" class="pi pi-check text-green-600"/>
          <i v-if="v$.value.$invalid" class="pi pi-times text-red-600"/>
        </span>
      </div>
    </td>
    <td v-if="!isView">
      <div class="flex mt-2 mb-2">
        <div class="mr-2 ml-2">
          <save-button @click="save"></save-button>
        </div>
        <div>
          <close-button @click="viewForm"></close-button>
        </div>
      </div>
    </td>

  </tr>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import AddButton from "@/components/common/addButton.vue";
import SaveButton from "@/components/common/saveButton.vue";
import CloseButton from "@/components/common/closeButton.vue";
import {useModelStore} from "@/stores/model";
import type {AddEnumOptionCommand} from "@/api/command/interface/enumCommands";
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {addEnumOption} from "@/api/command/model/addEnumOption";
import {useToast} from "primevue/usetoast";

// -- props

const isView = ref<boolean>(true);
const isSaving = ref<boolean>(false);
const modelStore = useModelStore();
const toaster = useToast();
const commandAdd = ref<AddEnumOptionCommand>({
  enumId: modelStore.enumSelectedId,
  code: 'code',
  value: 'value',
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
const v$ = useVuelidate(rules, commandAdd);

// -- functions

function editForm() {
  isView.value = false;
  commandAdd.value.code = '';
  commandAdd.value.value = '';
}

function viewForm() {
  isView.value = true;
  commandAdd.value.code = 'code';
  commandAdd.value.value = 'value';
}

async function save() {
  v$.value.$touch();
  if (!v$.value.$invalid) {
    isSaving.value = true;
    isView.value = true;
    await addEnumOption(commandAdd.value);
    toaster.add({
      severity: "success",
      summary: "Option added",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadEnum();
    commandAdd.value.code = 'code';
    commandAdd.value.value = 'value';
    isSaving.value = false;
  }
}

</script>