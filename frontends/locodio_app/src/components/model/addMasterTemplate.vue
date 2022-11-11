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
  <div style="width: 600px;">

    <form v-on:submit.prevent="add">

      <div class="flex flex-row w-full">
        <div class="basis-4/5">
          <div class="flex">
            <div class="mr-2">
              <RadioButton name="template-type" value="domain_model" input-id="domain-model-add"
                           v-model="command.type"/>
            </div>
            <div class="mr-2 mt-1 text-xs">
              <label for="domain-model-add">
                <i class="pi pi-database"></i> Model
              </label>
            </div>
            <div class="mr-2">
              <RadioButton name="template-type" value="enum" input-id="enum-add" v-model="command.type"/>
            </div>
            <div class="mr-2 mt-1 text-xs">
              <label for="enum-add">
                <i class="pi pi-align-justify"></i> Enum
              </label>
            </div>
            <div class="mr-2">
              <RadioButton name="template-type" value="query" input-id="query-add" v-model="command.type"/>
            </div>
            <div class="mr-2 mt-1 text-xs">
              <label for="query-add">
                <i class="pi pi-upload"></i> Query
              </label>
            </div>
            <div class="mr-2">
              <RadioButton name="template-type" value="command" input-id="command-add" v-model="command.type"/>
            </div>
            <div class="mr-2 mt-1 text-xs">
              <label for="command-add">
                <i class="pi pi-download"></i> Command
              </label>
            </div>
          </div>
        </div>
        <div class="basis-1/5 ml-2">
          <i class="pi pi-check-circle text-green-600"></i>
        </div>
      </div>

      <div class="flex flex-row w-full mt-4">
        <div class="basis-3/5 mr-2">
          <span class="p-input-icon-right w-full">
            <InputText class="w-full p-inputtext-sm"
                       placeholder="name"
                       v-model="command.name"/>
            <i v-if="!v$.name.$invalid" class="pi pi-check text-green-600"/>
            <i v-if="v$.name.$invalid" class="pi pi-times text-red-600"/>
          </span>
        </div>
        <div class="basis-1/5">
          <span class="p-input-icon-right w-full">
            <InputText class="w-full p-inputtext-sm"
                       placeholder="language"
                       v-model="command.language"/>
            <i v-if="!v$.language.$invalid" class="pi pi-check text-green-600"/>
            <i v-if="v$.language.$invalid" class="pi pi-times text-red-600"/>
          </span>
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
import {onMounted, ref} from "vue";
import {useModelStore} from "@/stores/model";
import useVuelidate from "@vuelidate/core";
import {required} from "@vuelidate/validators";
import {useToast} from "primevue/usetoast";
import type {AddMasterTemplateCommand} from "@/api/command/interface/masterTemplateCommands";
import {useAppStore} from "@/stores/app";
import {addMasterTemplate} from "@/api/command/model/addMasterTempate";

// -- props, store and emits

const emit = defineEmits(["added"]);
const modelStore = useModelStore();
const appStore = useAppStore();
const toaster = useToast();
const isSaving = ref<boolean>(false);
const command = ref<AddMasterTemplateCommand>({
  userId: appStore.user?.id ?? 0,
  type: 'domain_model',
  name: '',
  language: ''
});

// -- mounted

onMounted((): void => {
  v$.value.$touch();
});

// -- validation

const rules = {
  type: {required},
  name: {required},
  language: {required},
};
const v$ = useVuelidate(rules, command);

// -- add

async function add() {
  if (!v$.value.$invalid) {
    isSaving.value = true;
    await addMasterTemplate(command.value);
    toaster.add({
      severity: "success",
      summary: "Template added",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.loadMasterTemplates();
    isSaving.value = false;
    emit("added");
  }
}

</script>