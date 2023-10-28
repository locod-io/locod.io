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
  <div id="ChangePassword">
    <general-top-bar type="change-password"/>
    <div style="max-width:500px;" class="px-8 py-8 border-b-[1px] border-r-[1px] border-gray-300 dark:border-gray-600">

    <form v-on:submit.prevent="setNewPassword">
      <div class="mb-6">
        <router-link to="/my-profile">
          <Button label="BACK"
                  icon="pi pi-chevron-left"
                  class="p-button-sm p-button-outlined p-button-secondary"/>
        </router-link>
      </div>
      <div class="mt-2 text-sm">
        <div>
          Change the password for
          <strong>{{ appStore.user.email }}</strong> :
        </div>
      </div>
      <div class="mt-4">
        <InputText placeholder="enter new password"
                   v-model="command.password1"
                   class="w-full p-inputtext-sm"
                   @input="resetPassword2"
                   @change="resetPassword2"
                   type="password"/>
      </div>
      <div>
        <PasswordScore
            :value="command.password1"
            class="mt-2 mb-6"
            @passed="isPasswordStrong = true"
            @failed="isPasswordStrong = false"
        />
      </div>
      <div v-if="isPasswordStrong">
        <div class="pt-4 border-t-[1px]">
          Repeat your password:
        </div>
        <div class="mt-4">
          <InputText placeholder="repeat password" v-model="command.password2"
                     class="w-full p-inputtext-sm" type="password"/>
          <div>
            <span class="text-xs" v-if="command.password2 === ''">Please repeat your password.</span>
            <span class="text-xs text-red-500" v-else-if="!isPasswordMatching">The passwords don't match.</span>
          </div>
        </div>
      </div>
      <div class="mt-4" v-if="canReset">
        <Button :disabled="!canReset"
                type="submit"
                class="w-full p-button-sm p-button-success"
                icon="pi pi-lock"
                v-if="!isSaving"
                label="SET PASSWORD"/>
        <Button v-else class="w-full p-button-sm p-button-success"
                icon="pi pi-spin pi-spinner"
                label="SET PASSWORD"
                disabled="true"/>
      </div>
    </form>

  </div>
  </div>
</template>

<script setup lang="ts">
import PasswordScore from "@/components/user/PasswordScore.vue";
import {computed, onMounted, ref, watch} from "vue";
import router from "@/router";
import type {ChangePasswordCommand} from "@/api/command/interface/userCommands";
import {useAppStore} from "@/stores/app";
import {changePassword} from "@/api/command/user/changePassword";
import {useToast} from "primevue/usetoast";
import UserHeading from "@/components/user/userHeading.vue";
import GeneralTopBar from "@/_common/topBar/generalTopBar.vue";

const appStore = useAppStore();
const toaster = useToast();
const isSaving = ref<boolean>(false);
const command = ref<ChangePasswordCommand>({
  userId: appStore.user?.id ?? 0,
  password1: '',
  password2: '',
});

router.beforeEach((to, from, next) => {
  if (to.name === "changeMyPassword") {
    command.value.password1 = '';
    command.value.password2 = '';
  }
  next();
});

const isPasswordStrong = ref<boolean>(false);

function resetPassword2() {
  command.value.password2 = '';
}

const strongPasswordClass = computed(() => {
  if (command.value.password1 === '') return 'border-gray-200';
  if (!isPasswordStrong.value) return 'border-red-500';
  if (isPasswordStrong.value) return 'border-green-500';
})

const passwordMatchClass = computed(() => {
  if (command.value.password1 !== command.value.password1) return 'border-gray-200'
  if (command.value.password1 === command.value.password2) return 'border-green-500'
})

const isPasswordMatching = computed(() => {
  return command.value.password1 === command.value.password2;
});

const canReset = computed(() => {
  return command.value.password1 === command.value.password2 && isPasswordStrong.value;
});

async function setNewPassword() {
  if (canReset) {
    isSaving.value = true;
    const result = await changePassword(command.value);
    isSaving.value = false;
    command.value.password1 = '';
    command.value.password2 = '';
    toaster.add({
      severity: "success",
      summary: "Password changed.",
      detail: "",
      life: appStore.toastLifeTime,
    });
  }
}

</script>