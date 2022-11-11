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
  <div style="width: 650px;">

    <form v-on:submit.prevent="add">

      <div class="flex flex-row w-full p-inputtext-sm">
        <div class="basis-2/5">
        <span class="p-input-icon-right w-full">
          <InputText class="w-full p-inputtext-sm"
                     placeholder="Name"
                     v-model="command.name"/>
            <i v-if="!v$.name.$invalid" class="pi pi-check text-green-600"/>
            <i v-if="v$.name.$invalid" class="pi pi-times text-red-600"/>
        </span>
        </div>
        <div class="basis-2/5 ml-2">
          <div class="flex">
            <Dropdown optionLabel="name"
                      v-model="command.domainModelId"
                      option-value="id"
                      :options="modelStore.project.domainModels"
                      placeholder="Select a domain model"
                      class="w-full p-dropdown-sm"/>
            <div class="mx-2 mt-1.5">
              <i v-if="!v$.domainModelId.$invalid" class="pi pi-check-circle text-green-600"/>
              <i v-if="v$.domainModelId.$invalid" class="pi pi-times-circle text-red-600"/>
            </div>
          </div>
        </div>
        <div class="basis-1/5 ml-2">
          <Button
              v-if="!isSaving"
              type="submit"
              icon="pi pi-save"
              label="ADD"
              class="p-button-sm p-button-success"/>
          <Button
              v-else
              icon="pi pi-spin pi-spinner"
              label="ADD"
              disabled
              class="p-button-sm p-button-success"/>
        </div>
      </div>

    </form>

  </div>
</template>

<script setup lang="ts">
import {useModelStore} from "@/stores/model";
import {onMounted, ref} from "vue";
import {minValue, required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {useToast} from "primevue/usetoast";
import {addQuery} from "@/api/command/model/addQuery";
import type {AddQueryCommand} from "@/api/command/interface/queryCommands";

// -- props, store and emits

const emit = defineEmits(["added"]);
const modelStore = useModelStore();
const toaster = useToast();
const isSaving = ref<boolean>(false);
const command = ref<AddQueryCommand>({
  projectId: modelStore.projectId,
  domainModelId: 0,
  name: ''
});

// -- mounted

onMounted((): void => {
  v$.value.$touch();
});

// -- validation

const rules = {
  name: {required},
  domainModelId: {minValueValue: minValue(1)},
};
const v$ = useVuelidate(rules, command);

// -- add

async function add() {
  if (!v$.value.$invalid) {
    isSaving.value = true;
    await addQuery(command.value);
    toaster.add({
      severity: "success",
      summary: "Query added",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadProject();
    isSaving.value = false;
    emit("added");
  }
}

</script>