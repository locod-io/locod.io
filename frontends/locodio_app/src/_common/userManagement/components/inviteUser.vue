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
  <div style="width: 450px;">
    <form v-on:submit.prevent="invite">
      <div class="flex flex-row w-full">
        <div class="basis-4/5">
        <span class="p-input-icon-right w-full">
          <InputText class="w-full p-inputtext-sm"
                     placeholder="Email"
                     v-model="command.email"/>
            <i v-if="!v$.email.$invalid" class="pi pi-check text-green-600"/>
            <i v-if="v$.email.$invalid" class="pi pi-times text-red-600"/>
        </span>
        </div>
        <div class="basis-1/5 ml-2">
          <Button
              v-if="!isSaving"
              type="submit"
              icon="pi pi-user-plus"
              label="INVITE"
              class="p-button-sm p-button-success"/>
          <Button
              v-else
              icon="pi pi-spin pi-spinner"
              label="INVITE"
              disabled
              class="p-button-sm p-button-success"/>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import {email, required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {useToast} from "primevue/usetoast";
import type {InviteUserToOrganisationCommand} from "@/_common/userManagement/api/command/inviteUserToOrganisation";
import {inviteUserToOrganisation} from "@/_common/userManagement/api/command/inviteUserToOrganisation";
import {useAppStore} from "@/stores/app";
import {useUserManagementStore} from "@/_common/userManagement/store/userManagementStore";

// -- props, store and emits

const emit = defineEmits(["invited"]);
const appStore = useAppStore();
const userManagementStore = useUserManagementStore();
const toaster = useToast();
const isSaving = ref<boolean>(false);
const command = ref<InviteUserToOrganisationCommand>({
  organisationId: userManagementStore.organisationId,
  email: ''
});

// -- mounted

onMounted((): void => {
  v$.value.$touch();
});

// -- validation

const rules = {
  email: {required,email},
};
const v$ = useVuelidate(rules, command);

// -- invite user

async function invite() {
  if (!v$.value.$invalid) {
    isSaving.value = true;
    await inviteUserToOrganisation(command.value);
    toaster.add({
      severity: "success",
      summary: "User '" + command.value.email + "' invited",
      detail: "",
      life: appStore.toastLifeTime,
    });
    await userManagementStore.reloadUsers();
    isSaving.value = false;
    emit("invited");
  }
}

</script>