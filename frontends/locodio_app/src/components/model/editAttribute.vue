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

    <!-- view mode -------------------------------------------------------------------- -->
    <td v-if="isView">
      <div class="flex mt-1 mr-2 ml-2" v-if="!modelStore.isDomainModelFinal">
        <div class="mt-1.5 mb-1 text-gray-200 hover:text-green-600 cursor-move mr-2 dark:text-gray-600">
          <i class="pi pi-bars handle"></i>
        </div>
        <div class="mt-0.5">
          <edit-button @click="editForm" v-if="!modelStore.isDomainModelFinal"/>
        </div>
      </div>
    </td>
    <td v-if="isView">
      <div class="pt-1 pb-1">
        <strong>{{ item.name }}</strong>
      </div>
    </td>
    <td v-if="isView">{{ item.type }}</td>
    <td v-if="isView">
      <div v-if="item.type === 'enum'" class="text-xs">
        {{ item.enum.name }}
      </div>
      <div v-else>
        <div v-if="item.length !== 0">{{ item.length }}</div>
      </div>
    </td>
    <td v-if="isView" align="center"><i class="pi pi-check" v-if="(item.identifier)"></i></td>
    <td v-if="isView" align="center"><i class="pi pi-check" v-if="(item.required)"></i></td>
    <td v-if="isView" align="center"><i class="pi pi-check" v-if="(item.unique)"></i></td>
    <td v-if="isView" align="center"><i class="pi pi-check" v-if="(item.make)"></i></td>
    <td v-if="isView" align="center"><i class="pi pi-check" v-if="(item.change)"></i></td>
    <td v-if="isView" align="right" class="pr-2 pt-1.5">
      <delete-button @deleted="deleteItem" v-if="!modelStore.isDomainModelFinal" />
    </td>

    <!-- edit mode ------------------------------------------------------------------- -->

    <td v-if="!isView" colspan="2" class="pl-2">
      <span class="p-input-icon-right w-full">
        <InputText class="w-full p-inputtext-sm"
                   v-model="commandEdit.name"
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
                  v-model="commandEdit.type"
                  :options="modelStore.lists.attributeTypes"/>
    </td>
    <td v-if="!isView">
      <span class="p-input-icon-right w-full" v-if="showLength">
        <InputText class="w-full p-inputtext-sm"
                   v-model="commandEdit.length"
                   placeholder="length"/>
        <i v-if="!v$.length.$invalid" class="pi pi-check text-green-600"/>
        <i v-if="v$.length.$invalid" class="pi pi-times text-red-600"/>
      </span>
      <div v-if="showEnum">
          <Dropdown optionLabel="type"
                    class="w-full p-dropdown-sm"
                    option-label="name"
                    option-value="id"
                    v-model="commandEdit.enumId"
                    :options="enums"/>
      </div>
    </td>
    <td v-if="!isView" align="center" class="pt-1">
      <InputSwitch v-model="commandEdit.identifier"/>
    </td>
    <td v-if="!isView" align="center"  class="pt-1">
      <InputSwitch v-model="commandEdit.required"/>
    </td>
    <td v-if="!isView" align="center" class="pt-1">
      <InputSwitch v-model="commandEdit.unique"/>
    </td>
    <td v-if="!isView" align="center" class="pt-1">
      <InputSwitch v-model="commandEdit.make"/>
    </td>
    <td v-if="!isView" align="center" class="pt-1">
      <InputSwitch v-model="commandEdit.change"/>
    </td>
    <td v-if="!isView" align="right" class="pt-1.5">
      <div class="flex">
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
        <div class="mt-0.5">
          <close-button @click="viewForm"></close-button>
        </div>
      </div>
    </td>
  </tr>
</template>

<script setup lang="ts">
import {computed, onMounted, ref} from "vue";
import EditButton from "@/components/common/editButton.vue";
import SaveButton from "@/components/common/saveButton.vue";
import DeleteButton from "@/components/common/deleteButton.vue";
import CloseButton from "@/components/common/closeButton.vue";
import type {Attribute, Enum} from "@/api/query/interface/model";
import {useModelStore} from "@/stores/model";
import {useToast} from "primevue/usetoast";
import {required, integer} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import type {ChangeAttributeCommand} from "@/api/command/interface/domainModelCommands";
import {changeAttribute} from "@/api/command/model/changeAttribute";
import {deleteAttribute} from "@/api/command/model/deleteAttribute";

// -- props

const props = defineProps<{
  item: Attribute,
}>();

const isView = ref<boolean>(true);
const isSaving = ref<boolean>(false);
const modelStore = useModelStore();
const toaster = useToast();
const commandEdit = ref<ChangeAttributeCommand>({
  id: props.item.id,
  name: props.item.name,
  type: props.item.type,
  length: props.item.length,
  identifier: props.item.identifier,
  required: props.item.required,
  unique: props.item.unique,
  make: props.item.make,
  change: props.item.change,
  enumId: props.item.enum?.id ?? 0
});

// -- mounted

onMounted((): void => {
  v$.value.$touch();
});

// -- validation

const enumTypeValidator = function (value: any):boolean {
  if(showEnum.value) {
    if(value === 0 || value === '' || value === undefined) {
      return false;
    }
  }
  return true;
};

const rules = {
  name: {required},
  type: {required},
  length: {integer},
  enumId: {enumTypeValidator}
};
const v$ = useVuelidate(rules, commandEdit);

// -- functions

function editForm() {
  isView.value = false;
  commandEdit.value.id = props.item.id;
  commandEdit.value.name = props.item.name;
  commandEdit.value.type = props.item.type;
  commandEdit.value.length = props.item.length;
  commandEdit.value.identifier = props.item.identifier;
  commandEdit.value.required = props.item.required;
  commandEdit.value.unique = props.item.unique;
  commandEdit.value.make = props.item.make;
  commandEdit.value.change = props.item.change;
  commandEdit.value.enumId = props.item.enum?.id ?? 0
}

function viewForm() {
  isView.value = true;
}

async function save() {
  v$.value.$touch();
  if (!v$.value.$invalid) {
    isSaving.value = true;
    await changeAttribute(commandEdit.value);
    toaster.add({
      severity: "success",
      summary: "Attribute changed",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadDomainModel();
    isSaving.value = false;
    isView.value = true;
  }
}

const showLength = computed((): boolean => {
  if (commandEdit.value.type == 'integer') return false;
  if (commandEdit.value.type == 'enum') return false;
  return true;
});

const showEnum = computed((): boolean => {
  if (commandEdit.value.type == 'enum') return true;
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

async function deleteItem() {
  isSaving.value = true;
  await deleteAttribute({id: props.item.id});
  toaster.add({
    severity: "success",
    summary: "Attribute deleted.",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadDomainModel();
  isSaving.value = false;
}

</script>