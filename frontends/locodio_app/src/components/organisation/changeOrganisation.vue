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
  <div id="changeOrganisation">
    <div class="flex flex-row">
      <div class="basis-1/4 text-right">
        <div class="mt-1">Name *</div>
      </div>
      <div class="basis-3/4 ml-4">
        <span class="p-input-icon-right w-full">
           <InputText class="w-full p-inputtext-sm" v-model="command.name"></InputText>
           <i v-if="!v$.name.$invalid" class="pi pi-check text-green-600"/>
           <i v-if="v$.name.$invalid" class="pi pi-times text-red-600"/>
        </span>
      </div>
    </div>
    <div class="flex flex-row mt-4">
      <div class="basis-1/4 text-right">
        <div class="mt-1">Code *</div>
      </div>
      <div class="basis-3/4 ml-4">
        <span class="p-input-icon-right w-full">
           <InputText class="w-full p-inputtext-sm" v-model="command.code"></InputText>
           <i v-if="!v$.code.$invalid" class="pi pi-check text-green-600"/>
           <i v-if="v$.code.$invalid" class="pi pi-times text-red-600"/>
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
      <div class="basis-2/4 ml-4">
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
                icon="pi pi-building"
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
import type {ChangeOrganisationCommand, ChangeProfileCommand} from "@/api/command/interface/userCommands";
import {useAppStore} from "@/stores/app";
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {useToast} from "primevue/usetoast";
import {changeOrganisation} from "@/api/command/user/changeOrganisation";
import type {UserOrganisation} from "@/api/query/interface/user";

const props = defineProps<{ organisation: UserOrganisation }>();
const emit = defineEmits(["changed"]);
const appStore = useAppStore();
const toaster = useToast();
const isSaving = ref<boolean>(false);
const command = ref<ChangeOrganisationCommand>({
  color: props.organisation.color.replace('#', '') ?? '',
  name: props.organisation.name ?? '',
  code: props.organisation.code ?? '',
  id: props.organisation.id ?? 0,
});

// -- validation

const rules = {
  name: {required},
  code: {required},
  color: {required}
};
const v$ = useVuelidate(rules, command);

async function save() {
  if (!v$.value.$invalid) {
    isSaving.value = true;
    await changeOrganisation(command.value);
    toaster.add({
      severity: "success",
      summary: "Organisation changed",
      detail: "",
      life: appStore.toastLifeTime,
    });
    await appStore.reloadUserProjects();
    isSaving.value = false;
    emit("changed")
  }
}

</script>