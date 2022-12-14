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
  <div style="width: 400px;">

    <form v-on:submit.prevent="add">

      <div class="flex flex-row w-full p-inputtext-sm">
        <div class="basis-4/5">
          <span class="p-input-icon-right w-full">
            <InputText class="w-full p-inputtext-sm"
                       placeholder="Name"
                       v-model="command.name"/>
              <i v-if="!v$.name.$invalid" class="pi pi-check text-green-600"/>
              <i v-if="v$.name.$invalid" class="pi pi-times text-red-600"/>
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
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {useToast} from "primevue/usetoast";
import {useAppStore} from "@/stores/app";
import type {AddOrganisationCommand} from "@/api/command/interface/userCommands";
import {addOrganisation} from "@/api/command/user/addOrganisation";

// -- props, store and emits

const emit = defineEmits(["added"]);
const appStore = useAppStore();
const toaster = useToast();

const isSaving = ref<boolean>(false);
const command = ref<AddOrganisationCommand>({
  userId: appStore.user?.id ?? 0,
  name: ''
});

// -- mounted

onMounted((): void => {
  v$.value.$touch();
});

// -- validation

const rules = {
  name: {required}
};
const v$ = useVuelidate(rules, command);

// -- add

async function add() {
  if (!v$.value.$invalid) {
    isSaving.value = true;
    await addOrganisation(command.value);
    toaster.add({
      severity: "success",
      summary: "Organisation added",
      detail: "",
      life: appStore.toastLifeTime,
    });
    await appStore.reloadUserProjects()
    isSaving.value = false;
    emit("added");
  }
}

</script>