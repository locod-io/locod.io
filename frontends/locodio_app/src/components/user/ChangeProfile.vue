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

  <user-heading label="My Profile"/>

  <div id="ChangePassword" style="max-width:600px;" class="mx-auto mt-8">

    <div>
      <div class="text-white rounded-full w-16 h-10 pt-2 font-bold p-1 mx-auto text-center"
           :style="'background-color:'+color">
        <i class="pi pi-user cursor-pointer"></i>&nbsp;{{ initials }}
      </div>
    </div>

    <div class="border-b-[1px] mt-4"></div>

    <div class="flex flex-row mt-6">
      <div class="basis-1/4 text-right">Email</div>
      <div class="basis-2/4 ml-4"><strong>{{ appStore.user.email }}</strong></div>
      <div class="basis-1/4 text-right">
        <div class="text-sm text-blue-600">
          <router-link to="/change-my-password">Change password?</router-link>
        </div>
      </div>

    </div>
    <div class="flex flex-row mt-6">
      <div class="basis-1/4 text-right">
        <div class="mt-3">Firstname *</div>
      </div>
      <div class="basis-3/4 ml-4">
        <span class="p-input-icon-right w-full">
           <InputText class="w-full" v-model="command.firstname"></InputText>
           <i v-if="!v$.firstname.$invalid" class="pi pi-check text-green-600"/>
           <i v-if="v$.firstname.$invalid" class="pi pi-times text-red-600"/>
        </span>
      </div>
    </div>
    <div class="flex flex-row mt-4">
      <div class="basis-1/4 text-right">
        <div class="mt-3">Lastname *</div>
      </div>
      <div class="basis-3/4 ml-4">
        <span class="p-input-icon-right w-full">
           <InputText class="w-full" v-model="command.lastname"></InputText>
           <i v-if="!v$.lastname.$invalid" class="pi pi-check text-green-600"/>
           <i v-if="v$.lastname.$invalid" class="pi pi-times text-red-600"/>
        </span>
      </div>
    </div>
    <div class="flex flex-row mt-4">
      <div class="basis-1/4 text-right">
        <div class="mt-1">Color *</div>
      </div>
      <div class="basis-1/12 ml-4">
        <ColorPicker v-model="command.color"></ColorPicker>
      </div>
      <div class="basis-3/12 ml-4">
        <span class="p-input-icon-right w-full">
           <InputText class="w-full p-inputtext-sm" v-model="command.color"></InputText>
           <i v-if="!v$.color.$invalid" class="pi pi-check text-green-600"/>
           <i v-if="v$.color.$invalid" class="pi pi-times text-red-600"/>
        </span>
      </div>
    </div>
    <div class="flex flex-row mt-4">
      <div class="basis-1/4 text-right">&nbsp;</div>
      <div class="basis-3/4 ml-4">
        <Button v-if="!isSaving"
                @click="save"
                label="SAVE"
                :disabled="v$.$invalid"
                icon="pi pi-user"
                class="w-full"/>
        <Button v-else class="w-full"
                icon="pi pi-spin pi-spinner"
                label="SAVING"
                disabled="true"/>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import {computed, ref} from "vue";
import type {ChangeProfileCommand} from "@/api/command/interface/userCommands";
import {useAppStore} from "@/stores/app";
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {useToast} from "primevue/usetoast";
import {changeProfile} from "@/api/command/user/changeProfile";
import UserHeading from "@/components/user/userHeading.vue";

const appStore = useAppStore();
const toaster = useToast();
const isSaving = ref<boolean>(false);
const command = ref<ChangeProfileCommand>({
  color: appStore.user?.color.replace('#', '') ?? "CCCCCC",
  firstname: appStore.user?.firstname ?? '',
  lastname: appStore.user?.lastname ?? '',
  userId: appStore.user?.id ?? 0,
});

// -- validation

const rules = {
  firstname: {required},
  lastname: {required},
  color: {required}
};
const v$ = useVuelidate(rules, command);

const initials = computed((): string => {
  let _initials = '';
  _initials += command.value.firstname.substring(0, 1);
  _initials += command.value.lastname.substring(0, 1);
  return _initials;
});

const color = computed((): string => {
  return '#' + command.value.color;
});

async function save() {
  if (!v$.value.$invalid) {
    isSaving.value = true;
    await changeProfile(command.value);
    toaster.add({
      severity: "success",
      summary: "Profile changed",
      detail: "",
      life: appStore.toastLifeTime,
    });
    await appStore.loadUser();
    isSaving.value = false;
    await appStore.reloadUserProjects();
  }
}

</script>