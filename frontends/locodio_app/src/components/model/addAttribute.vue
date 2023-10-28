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
  <tr id="Component_Add_Field" class="h-12"
      v-on:keyup.enter="save"
      v-on:keyup.esc="viewForm">

    <!-- add button ------------------------------------------------------------------------------------------------ -->
    <td v-if="isView">
      <div class="flex mt-2 mr-2 ml-4">
        <add-button @click="editForm"/>
      </div>
    </td>

    <!-- edit mode ------------------------------------------------------------------------------------------------- -->
    <td v-if="!isView" colspan="2">
      <span class="p-input-icon-right w-full pl-2">
        <InputText class="w-full p-inputtext-sm"
                   v-model="commandAdd.name"
                   placeholder="name"/>
        <i v-if="!v$.name.$invalid" class="pi pi-check text-green-600"/>
        <i v-if="v$.name.$invalid" class="pi pi-times text-red-600"/>
      </span>
    </td>
    <td v-if="!isView">
      <Dropdown optionLabel="type"
                class="w-full p-dropdown-sm"
                option-label="label"
                option-value="code"
                v-model="commandAdd.type"
                :options="modelStore.lists.attributeTypes"/>
    </td>
    <td v-if="!isView">
      <span class="p-input-icon-right w-full" v-if="showLength">
        <InputText class="w-full p-inputtext-sm"
                   v-model="commandAdd.length"
                   placeholder="length"/>
        <i v-if="!v$.length.$invalid" class="pi pi-check text-green-600"/>
        <i v-if="v$.length.$invalid" class="pi pi-times text-red-600"/>
      </span>
      <div v-if="showEnum">
        <div>
          <Dropdown optionLabel="type"
                    class="w-full p-dropdown-sm"
                    option-label="name"
                    option-value="id"
                    v-model="commandAdd.enumId"
                    :options="enums"/>
        </div>
      </div>
    </td>
    <td v-if="!isView" align="center">
      <InputSwitch v-model="commandAdd.identifier" class="mt-1.5"/>
    </td>
    <td v-if="!isView" align="center">
      <InputSwitch v-model="commandAdd.required" class="mt-1.5"/>
    </td>
    <td v-if="!isView" align="center">
      <InputSwitch v-model="commandAdd.unique" class="mt-1.5"/>
    </td>
    <td v-if="!isView" align="center">
      <InputSwitch v-model="commandAdd.make" class="mt-1.5"/>
    </td>
    <td v-if="!isView" align="center">
      <InputSwitch v-model="commandAdd.change" class="mt-1.5"/>
    </td>
    <td v-if="!isView" class="right">
      <div class="flex">
        <div class="mr-2 ml-2 mt-0.5">
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
import {computed, onMounted, ref} from "vue";
import AddButton from "@/components/common/addButton.vue";
import CloseButton from "@/components/common/closeButton.vue";
import {useModelStore} from "@/stores/model";
import {useToast} from "primevue/usetoast";
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import type {AddAttributeCommand,} from "@/api/command/interface/domainModelCommands";
import {addAttribute} from "@/api/command/model/addAttribute";
import type {Enum} from "@/api/query/interface/model";

// -- props

const isView = ref<boolean>(true);
const isSaving = ref<boolean>(false);
const modelStore = useModelStore();
const toaster = useToast();
const commandAdd = ref<AddAttributeCommand>({
  domainModelId: modelStore.domainModelSelectedId,
  name: 'some-field',
  length: 0,
  type: 'integer',
  identifier: false,
  required: false,
  unique: false,
  make: false,
  change: false,
  enumId: 0
});

// -- mounted

onMounted((): void => {
  v$.value.$touch();
});

// -- validation

const enumTypeValidator = function (value: any): boolean {
  if (showEnum.value) {
    if (value === 0 || value === '' || value === undefined) {
      return false;
    }
  }
  return true;
};

const rules = {
  name: {required},
  length: {required},
  type: {required},
  enumId: {enumTypeValidator}
};
const v$ = useVuelidate(rules, commandAdd);

// -- functions

function editForm() {
  isView.value = false;
  commandAdd.value.name = '';
}

function viewForm() {
  isView.value = true;
  commandAdd.value.name = 'some-attribute';
}

async function save() {
  v$.value.$touch();
  if (!v$.value.$invalid) {
    isSaving.value = true;
    await addAttribute(commandAdd.value);
    toaster.add({
      severity: "success",
      summary: "Attribute added",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadDomainModel();
    commandAdd.value.name = 'some-attribute';
    commandAdd.value.length = 0;
    commandAdd.value.type = 'integer';
    commandAdd.value.identifier = false;
    commandAdd.value.required = false;
    commandAdd.value.unique = false;
    commandAdd.value.make = false;
    commandAdd.value.change = false;
    commandAdd.value.enumId = 0
    isSaving.value = false;
    isView.value = true;
  }
}

const showLength = computed((): boolean => {
  if (commandAdd.value.type == 'integer') return false;
  if (commandAdd.value.type == 'float') return false;
  if (commandAdd.value.type == 'enum') return false;
  return true;
});

const showEnum = computed((): boolean => {
  if (commandAdd.value.type == 'enum') return true;
  return false;
});

const enums = computed((): Array<Enum> => {
  if (modelStore.project) {
    let result = [];
    for (let i = 0; i < modelStore.project.enums.length; i++) {
      if (modelStore.project.enums[i].domainModel.id === modelStore.domainModelSelectedId) {
        result.push(modelStore.project.enums[i]);
      }
    }
    return result;
  } else {
    return [];
  }
});

</script>