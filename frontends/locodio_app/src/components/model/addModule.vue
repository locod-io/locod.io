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
  <tr id="Component_Add_Module"
      v-on:keyup.enter="save"
      v-on:keyup.esc="viewForm">

    <!-- add button ------------------------------------------------------------------------------------------------ -->
    <td v-if="isView" colspan="2">
      <div class="flex mt-1 mb-1 mr-2 ml-4">
        <add-button @click="editForm"/>
      </div>
    </td>

    <!-- edit mode ------------------------------------------------------------------------------------------------- -->
    <td v-if="!isView" colspan="2">
      <span class="p-input-icon-right w-full">
        <InputText class="w-full p-inputtext-sm"
                   placeholder="name"
                   v-model="commandAdd.name"/>
        <i v-if="!vModuleAdd$.name.$invalid" class="pi pi-check text-green-600"/>
        <i v-if="vModuleAdd$.name.$invalid" class="pi pi-times text-red-600"/>
      </span>
    </td>
    <td v-if="!isView">
      <div class="pl-2">
        <span class="p-input-icon-right w-full">
          <InputText class="w-full p-inputtext-sm"
                     placeholder="namespace"
                     v-model="commandAdd.namespace"/>
          <i v-if="!vModuleAdd$.namespace.$invalid" class="pi pi-check text-green-600"/>
          <i v-if="vModuleAdd$.namespace.$invalid" class="pi pi-times text-red-600"/>
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
import SaveButton from "@/components/common/saveButton.vue";
import CloseButton from "@/components/common/closeButton.vue";
import AddButton from "@/components/common/addButton.vue";
import {onMounted, ref} from "vue";
import {useModelStore} from "@/stores/model";
import type {AddModuleCommand} from "@/api/command/interface/modelConfiguration";
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {useToast} from "primevue/usetoast";
import {addModule} from "@/api/command/model/addModule";

// -- props
const isView = ref<boolean>(true);
const isSaving = ref<boolean>(false);
const modelStore = useModelStore();
const toaster = useToast();
const commandAdd = ref<AddModuleCommand>({
  projectId: modelStore.projectId,
  name: '',
  namespace: '',
});

// -- mounted
onMounted((): void => {
  vModuleAdd$.value.$touch();
});

// -- validation
const rules = {name: {required}, namespace: {required}};
const vModuleAdd$ = useVuelidate(rules, commandAdd);

// -- functions

function editForm() {
  isView.value = false;
}

function viewForm() {
  isView.value = true;
  commandAdd.value.name = '';
  commandAdd.value.namespace = '';
}

async function save() {
  vModuleAdd$.value.$touch();
  if (!vModuleAdd$.value.$invalid) {
    isSaving.value = true;
    isView.value = true;
    await addModule(commandAdd.value);
    toaster.add({
      severity: "success",
      summary: "Module added",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadProject();
    commandAdd.value.name = '';
    commandAdd.value.namespace = '';
    isSaving.value = false;
  }
}

</script>