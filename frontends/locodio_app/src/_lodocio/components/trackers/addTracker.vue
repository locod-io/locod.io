<!--
/*
* This file is part of the Lodoc.io software.
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

      <div class="flex gap-2">
        <div class="flex-grow">
          <span class="p-input-icon-right w-full">
            <InputText class="w-full p-inputtext-sm"
                       placeholder="name"
                       v-model="command.name"/>
            <i v-if="!v$.name.$invalid" class="pi pi-check text-green-600"/>
            <i v-if="v$.name.$invalid" class="pi pi-times text-red-600"/>
          </span>
        </div>
        <div class="flex-none w-32">
          <span class="p-input-icon-right w-full">
            <InputText class="w-full p-inputtext-sm"
                       placeholder="code"
                       v-model="command.code"/>
            <i v-if="!v$.code.$invalid" class="pi pi-check text-green-600"/>
            <i v-if="v$.code.$invalid" class="pi pi-times text-red-600"/>
          </span>
        </div>
        <div class="flex-none w-8">
          <ColorPicker v-model="command.color"></ColorPicker>
        </div>
        <div class="flex-none w-32">
          <span class="p-input-icon-right w-full">
             <InputText class="w-full p-inputtext-sm" v-model="command.color"></InputText>
             <i v-if="!v$.color.$invalid" class="pi pi-check text-green-600"/>
             <i v-if="v$.color.$invalid" class="pi pi-times text-red-600"/>
          </span>
        </div>

        <div class="flex-none w-32">
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
import useVuelidate from "@vuelidate/core";
import {required} from "@vuelidate/validators";
import {useToast} from "primevue/usetoast";
import type {AddTrackerCommand} from "@/_lodocio/api/command/tracker/addTracker";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import {useAppStore} from "@/stores/app";
import {addTracker} from "@/_lodocio/api/command/tracker/addTracker";

// -- props, store and emits

const emit = defineEmits(["added"]);
const projectStore = useDocProjectStore();
const appStore = useAppStore();
const toaster = useToast();
const isSaving = ref<boolean>(false);
const command = ref<AddTrackerCommand>({
  docProjectId: projectStore.docProjectId,
  name: '',
  code: '',
  color: 'C0C0C0',
});

// -- mounted

onMounted((): void => {
  v$.value.$touch();
});

// -- validation

const rules = {
  name: {required},
  code: {required},
  color: {required},
};

const v$ = useVuelidate(rules, command);

// -- add

async function add() {
  if (!v$.value.$invalid) {
    isSaving.value = true;
    await addTracker(command.value);
    toaster.add({
      severity: "success",
      summary: "Tracker added",
      detail: "",
      life: appStore.toastLifeTime,
    });
    await projectStore.reloadProject();
    isSaving.value = false;
    emit("added");
  }
}

</script>