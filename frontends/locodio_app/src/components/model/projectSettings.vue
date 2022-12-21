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
  <div id="projectSettings">
    <Fieldset legend="Settings">
      <div class="text-sm">
        Domain Layer
      </div>
      <div class="mt-1">
        <span class="p-input-icon-right w-full">
          <InputText class="w-full p-inputtext-sm"
                     v-model="command.domainLayer"></InputText>
          <i v-if="!vSettings$.domainLayer.$invalid" class="pi pi-check text-green-600"/>
          <i v-if="vSettings$.domainLayer.$invalid" class="pi pi-times text-red-600"/>
        </span>
      </div>
      <div class="text-sm mt-2">
        Application Layer
      </div>
      <div class="mt-1">
        <span class="p-input-icon-right w-full">
          <InputText class="w-full p-inputtext-sm"
                     v-model="command.applicationLayer"></InputText>
          <i v-if="!vSettings$.applicationLayer.$invalid" class="pi pi-check text-green-600"/>
          <i v-if="vSettings$.applicationLayer.$invalid" class="pi pi-times text-red-600"/>
        </span>
      </div>
      <div class="text-sm mt-2">
        Infrastructure Layer
      </div>
      <div class="mt-1">
        <span class="p-input-icon-right w-full">
          <InputText class="w-full p-inputtext-sm"
                     v-model="command.infrastructureLayer"></InputText>
          <i v-if="!vSettings$.infrastructureLayer.$invalid" class="pi pi-check text-green-600"/>
          <i v-if="vSettings$.infrastructureLayer.$invalid" class="pi pi-times text-red-600"/>
        </span>
      </div>
      <div class="mt-2">
        <Button
            v-if="!isSaving"
            @click="saveSettings"
            type="submit"
            icon="pi pi-save"
            label="SAVE"
            class="p-button-success p-button-sm w-full"/>
        <Button
            v-else
            disabled
            icon="pi pi-spin pi-spinner"
            label="SAVE"
            class="p-button-success p-button-sm w-full"/>
      </div>
    </Fieldset>
  </div>
</template>

<script setup lang="ts">
import {useModelStore} from "@/stores/model";
import {onMounted, ref} from "vue";
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {changeModelSettings} from "@/api/command/model/changeModelSettings";
import {useToast} from "primevue/usetoast";
import type {ChangeModelSettingsCommand} from "@/api/command/interface/modelConfiguration";

// -- stores & properties

const modelStore = useModelStore();
const toaster = useToast();

// -- mounted

onMounted((): void => {
  vSettings$.value.$touch();
});

// -- command

const command = ref<ChangeModelSettingsCommand>({
  id: modelStore.project ? modelStore.project.modelSettings?.id ?? 0 : 0,
  projectId: modelStore.projectId,
  domainLayer: modelStore.project ? modelStore.project.modelSettings?.domainLayer ?? modelStore.project.domainLayer : "",
  applicationLayer: modelStore.project ? modelStore.project.modelSettings?.applicationLayer ?? modelStore.project.applicationLayer : "",
  infrastructureLayer: modelStore.project ? modelStore.project.modelSettings?.infrastructureLayer ?? modelStore.project.infrastructureLayer : "",
});

// -- validation

const rules = {
  domainLayer: {required},
  applicationLayer: {required},
  infrastructureLayer: {required},
};
const vSettings$ = useVuelidate(rules, command);

// -- saving

const isSaving = ref<boolean>(false);

async function saveSettings() {
  vSettings$.value.$touch();
  if (!vSettings$.value.$invalid) {
    isSaving.value = true;
    await changeModelSettings(command.value);
    toaster.add({
      severity: "success",
      summary: "Model settings saved.",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadProject();
    // update the command id with the settings ID
    command.value.id = modelStore.project ? modelStore.project.modelSettings?.id ?? 0 : 0;
    isSaving.value = false;
  }
}

</script>