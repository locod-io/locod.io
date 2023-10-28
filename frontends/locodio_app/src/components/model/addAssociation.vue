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
  <tr id="Component_Add_Relation" class="h-12"
      v-on:keyup.enter="save"
      v-on:keyup.esc="viewForm">

    <!-- add button ------------------------------------------------------------------------------------------------ -->
    <td v-if="isView">
      <div class="flex mt-2 mb-1 mr-2 ml-4">
        <add-button @click="editForm"/>
      </div>
    </td>

    <!-- add form -------------------------------------------------------------------------------------------------- -->
    <td v-if="!isView" colspan="3" class="pl-2">
      <Dropdown optionLabel="type"
                v-model="commandAdd.type"
                option-label="label"
                option-value="code"
                :options="modelStore.lists.associationTypes"
                class="w-full p-dropdown-sm"/>

    </td>
    <td v-if="!isView" colspan="2">
      <div v-if="modelStore.project">
        <Dropdown optionLabel="type"
                  v-model="commandAdd.targetDomainModelId"
                  option-label="name"
                  option-value="id"
                  :options="modelStore.project.domainModels"
                  class="w-full p-dropdown-sm"/>
      </div>
    </td>
    <td v-if="!isView" colspan="2">
      <Dropdown optionLabel="type"
                v-model="commandAdd.fetch"
                option-label="label"
                option-value="code"
                :options="modelStore.lists.fetchTypes"
                class="w-full p-dropdown-sm"/>

    </td>
    <td v-if="!isView" colspan="2" align="right">
      <div class="flex gap-2">
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
import {useToast} from "primevue/usetoast";
import type {AddAssociationCommand} from "@/api/command/interface/domainModelCommands";
import {minValue, required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {addAssociation} from "@/api/command/model/addAssociation";

// -- props, stores and usages

const modelStore = useModelStore();
const toaster = useToast();
const isView = ref<boolean>(true);
const isSaving = ref<boolean>(false);
const commandAdd = ref<AddAssociationCommand>({
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
  targetDomainModelId: {required, minValueValue: minValue(1)}
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
    await addAssociation(commandAdd.value);
    toaster.add({
      severity: "success",
      summary: "Association added",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadDomainModel();
    commandAdd.value.type = '';
    commandAdd.value.fetch = '';
    commandAdd.value.targetDomainModelId = 0;
    isSaving.value = false;
    isView.value = true;
  }
}

</script>