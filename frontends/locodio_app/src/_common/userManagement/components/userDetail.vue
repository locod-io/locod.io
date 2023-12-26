<template>

  <!-- toolbar --------------------------------------------------------------------------------------------- -->
  <div class="flex gap-2 gap-0 border-b-[1px] border-gray-300 dark:border-gray-600 h-12">
    <div class="flex-none my-2 pl-2">
      <Button
          v-if="!isSaving"
          @click="changeRoles"
          icon="pi pi-save"
          label="SAVE"
          class="p-button-success p-button-sm w-36"/>
      <Button
          v-else
          disabled
          icon="pi pi-spin pi-spinner"
          label="SAVE"
          class="p-button-success p-button-sm w-36"/>
    </div>
    <div class="flex-none my-2 pl-2">
      <div class="flex">
        <div>
          <Button
              @click="reload"
              v-if="!userManagementStore.userDetailReloading"
              icon="pi pi-refresh"
              class="p-button-sm"/>
          <Button
              v-else
              disabled
              icon="pi pi-spin pi-spinner"
              class="p-button-sm"/>
        </div>
      </div>
    </div>
    <div class="flex-grow">&nbsp;</div>
  </div>

  <!-- form ------------------------------------------------------------------------------------------------ -->
  <DetailWrapper :estate-height="125">
    <div class="p-2">
      <div class="flex gap-4">
        <div class="flex-none">
          <div class="rounded-full px-2 py-2 text-xs text-white"
               :style="'background-color:'+userManagementStore.userDetail.color">
            {{ userManagementStore.userDetail.initials }}
          </div>
        </div>
        <div class="mt-1 flex-none font-bold">
          {{ userManagementStore.userDetail.firstname }} {{ userManagementStore.userDetail.lastname }}
        </div>
        <div class="mt-1 flex-none">
          ->
        </div>
        <div class="mt-1 flex-none">
          {{ userManagementStore.userDetail.email }}
        </div>
      </div>
      <div class="mt-4">
        <div>
          <div class="flex gap-4">
            <div class="ml-12 w-16">
              Role
            </div>
            <div class="w-48 mt-2.5">
              <Slider v-model="userRoleValue" :step="33"/>
            </div>
            <div class="ml-4">
              <div v-if="userRoleValue === 100" class="flex gap-4">
                <div><font-awesome-icon :icon="['fas', 'user-gear']" title="Administrator"/></div>
                <div>Admin</div>
              </div>
              <div v-if="userRoleValue === 67" class="flex gap-4">
                <div><font-awesome-icon :icon="['fas', 'user']" title="User"/></div>
                <div>User</div>
              </div>
              <div v-if="userRoleValue === 34" class="flex gap-2">
                <div><font-awesome-icon :icon="['fas', 'glasses']" title="Viewer"/></div>
                <div>Viewer</div>
              </div>
              <div v-if="userRoleValue === 1">
                In-active
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DetailWrapper>

  <!-- // bottom navigation -->
  <div class="flex gap-2 border-t-[1px] border-gray-300 dark:border-gray-600 h-12">
    &nbsp;
  </div>

</template>

<script setup lang="ts">
import {useAppStore} from "@/stores/app";
import {useUserManagementStore} from "@/_common/userManagement/store/userManagementStore";
import {computed, onMounted, ref} from "vue";
import DetailWrapper from "@/components/wrapper/detailWrapper.vue";
import {
  changeOrganisationUserRoles,
  type ChangeOrganisationUserRolesCommand
} from "@/_common/userManagement/api/command/changeUserRoles";
import {useToast} from "primevue/usetoast";

const appStore = useAppStore();
const userManagementStore = useUserManagementStore();
const toaster = useToast();
const isSaving = ref<boolean>(false);

const userRoles = ref<Array<string>>([]);
const userRoleValue = ref<Number>(1);

onMounted(() => {
  calculateUserRoleValue()
});

function calculateUserRoleValue() {
  userRoleValue.value = 1;
  if (userManagementStore.userDetail) {
    userRoles.value = userManagementStore.userDetail.organisationPermissions[0].roles ?? [];
    const roles: Array<string> = userManagementStore.userDetail.organisationPermissions[0].roles;
    if (roles.includes('ROLE_ORGANISATION_ADMIN')) {
      userRoleValue.value = 100;
    } else if (roles.includes('ROLE_ORGANISATION_USER')) {
      userRoleValue.value = 67;
    } else if (roles.includes('ROLE_ORGANISATION_VIEWER')) {
      userRoleValue.value = 34;
    } else {
      userRoleValue.value = 1;
    }
  }
}

const userRolesComputed = computed((): Array<string> => {
  if (userRoleValue.value === 100) {
    return ['ROLE_ORGANISATION_ADMIN', 'ROLE_ORGANISATION_USER', 'ROLE_ORGANISATION_VIEWER'];
  } else if (userRoleValue.value === 67) {
    return ['ROLE_ORGANISATION_USER', 'ROLE_ORGANISATION_VIEWER'];
  } else if (userRoleValue.value === 34) {
    return ['ROLE_ORGANISATION_VIEWER'];
  } else {
    return [];
  }
});

const command = ref<ChangeOrganisationUserRolesCommand>({
  userId: userManagementStore.userDetail?.id ?? 0,
  organisationId: userManagementStore.userDetail?.organisations[0].id ?? 0,
  roles: userRolesComputed,
});

async function changeRoles() {
  isSaving.value = true;
  command.value.userId = userManagementStore.userDetail?.id ?? 0;
  command.value.organisationId = userManagementStore.userDetail?.organisations[0].id ?? 0;
  command.value.roles = userRolesComputed;
  await changeOrganisationUserRoles(command.value);
  toaster.add({
    severity: "success",
    summary: "User roles updated.",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await userManagementStore.reloadUsers();
  await userManagementStore.reloadUser();
  isSaving.value = false;
}

async function reload() {
  await userManagementStore.reloadUser();
}

</script>

<style scoped>

</style>