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
  <tr id="Component_Add_Relation"
      v-on:keyup.enter="save"
      v-on:keyup.esc="viewForm">

    <!-- add button ------------------------------------------------------------------------------------------------ -->
    <td v-if="isView">
      <div class="flex mt-1 mb-1 mr-2 ml-4">
        <add-button @click="editForm"/>
      </div>
    </td>

    <!-- add form -------------------------------------------------------------------------------------------------- -->
    <td v-if="!isView" colspan="3">
      <div class="p-inputtext-sm">
        <Dropdown optionLabel="type"
                  v-model="commandAdd.type"
                  option-label="label"
                  option-value="code"
                  :options="modelStore.lists.relationTypes"
                  class="w-full p-dropdown-sm"/>
      </div>
    </td>
    <td v-if="!isView" colspan="2">
      <div class="p-inputtext-sm" v-if="modelStore.project">
        <Dropdown optionLabel="type"
                  v-model="commandAdd.targetDomainModelId"
                  option-label="name"
                  option-value="id"
                  :options="modelStore.project.domainModels"
                  class="w-full p-dropdown-sm"/>
      </div>
    </td>
    <td v-if="!isView" colspan="2">
      <div class="p-inputtext-sm">
        <Dropdown optionLabel="type"
                  v-model="commandAdd.fetch"
                  option-label="label"
                  option-value="code"
                  :options="modelStore.lists.fetchTypes"
                  class="w-full p-dropdown-sm"/>
      </div>
    </td>
    <td v-if="!isView" colspan="2" align="right">
      <div class="flex">
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
import {onMounted, ref} from "vue";
import AddButton from "@/components/common/addButton.vue";
import SaveButton from "@/components/common/saveButton.vue";
import CloseButton from "@/components/common/closeButton.vue";
import {useModelStore} from "@/stores/model";
import {useToast} from "primevue/usetoast";
import type {AddRelationCommand} from "@/api/command/interface/domainModelCommands";
import {minValue, required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {addRelation} from "@/api/command/model/addRelation";

// -- props, stores and usages

const modelStore = useModelStore();
const toaster = useToast();
const isView = ref<boolean>(true);
const isSaving = ref<boolean>(false);
const commandAdd = ref<AddRelationCommand>({
  domainModelId: modelStore.domainModelSelectedId,
  type: 'type',
  mappedBy: '*',
  inversedBy: '*',
  fetch: 'extra_lazy',
  orderBy: 'id',
  orderDirection: 'asc',
  targetDomainModelId: 1
});

// -- mounted

onMounted((): void => {
  v$.value.$touch();
});

// -- validation

const rules = {
  type: {required},
  fetch: {required},
  targetDomainModelId: {required,minValueValue: minValue(1)}
};
const v$ = useVuelidate(rules, commandAdd);

// -- functions

function editForm() {
  isView.value = false;
  commandAdd.value.type = '';
  commandAdd.value.fetch = '';
  commandAdd.value.mappedBy = '';
  commandAdd.value.inversedBy = '';
  commandAdd.value.targetDomainModelId = 0;
}

function viewForm() {
  isView.value = true;
  commandAdd.value.type = 'type';
  commandAdd.value.fetch = 'extra_lazy';
  commandAdd.value.mappedBy = '*';
  commandAdd.value.inversedBy = '*';
  commandAdd.value.targetDomainModelId = 1;
}

async function save() {
  v$.value.$touch();
  if (!v$.value.$invalid) {
    isSaving.value = true;
    isView.value = true;
    await addRelation(commandAdd.value);
    toaster.add({
      severity: "success",
      summary: "Relation added",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadDomainModel();
    commandAdd.value.type = '';
    commandAdd.value.fetch = '';
    commandAdd.value.targetDomainModelId = 0;
    isSaving.value = false;
  }
}

</script>