<template>
  <div id="listTemplate">

    <!-- search & refresh --------------------------------------------------------- -->

    <div class="flex border-b-[1px] border-gray-300 dark:border-gray-600 h-12">
      <div class="flex-none p-2">
        <Button
            v-if="!userManagementStore.isUsersLoading"
            icon="pi pi-refresh"
            class="p-button-sm"
            @click="refreshUsers"/>
        <Button
            v-else
            class="p-button-sm"
            disabled
            icon="pi pi-spin pi-spinner"/>
      </div>
      <div class="flex-grow pr-2.5 pt-2">
        <div class="p-input-icon-right w-full">
          <InputText
              type="text"
              class="w-full p-inputtext-sm"
              v-model="search"/>
          <i class="pi pi-search"/>
        </div>
      </div>
    </div>

    <!-- user list ------------------------------------------------------------ -->

    <list-wrapper :estate-height="125">
      <div v-for="user in filteredUsers">
        <div class="w-full bg-white hover:bg-indigo-100 h-12 dark:bg-gray-900 dark:hover:bg-indigo-900"
             :class="selectedClass(user.id)"
             @dblclick="selectUser(user.id)">
          <div class="flex gap-2 h-12 p-3">
            <div class="w-6">
              <font-awesome-icon :icon="['fas', getIcon(user)]"/>
            </div>
            <div class="flex-grow line-clamp-1 text-sm h-5 font-semibold">
              {{ user.email }}
            </div>
            <div class="flex-none" v-if="user.id != userManagementStore.userId">
              <edit-button @click="selectUser(user.id)"></edit-button>
            </div>
          </div>
        </div>
      </div>
      <div v-for="invitation in userManagementStore.invitations">
        <div class="w-full bg-white hover:bg-indigo-100 h-12 dark:bg-gray-900 dark:hover:bg-indigo-900 border-b-[1px] border-gray-300 dark:border-gray-600">
          <div class="flex gap-2 h-12 p-3">
            <div class="w-6"><font-awesome-icon :icon="['fas', 'user']" title="User"/></div>
            <div class="flex-grow line-clamp-1 text-sm h-5  italic">
              {{invitation.email }}
            </div>
          </div>
        </div>
      </div>
    </list-wrapper>

    <!-- -- footer --------------------------------------------------------------- -->

    <div class="border-t-[1px] border-gray-300 dark:border-gray-600 h-12 p-2 flex gap-2">
      <!-- add -->
      <div class="flex-none">
        <Button
            icon="pi pi-plus"
            class="p-button-sm p-button-icon"
            @click="toggle"
            aria-haspopup="true"
            aria-controls="overlay_panel"
        />
      </div>
      <div class="flex-grow">&nbsp;</div>
      <div class="flex-none">
        <!-- remove this user form this organisation  -->
        <remove-user/>
      </div>

    </div>
  </div>

  <OverlayPanel ref="op" :showCloseIcon="true" :dismissable="true">
    <invite-user v-on:invited="userInvited"/>
  </OverlayPanel>

</template>

<script setup lang="ts">
import {useAppStore} from "@/stores/app";
import {useUserManagementStore} from "@/_common/userManagement/store/userManagementStore";
import {computed, onMounted, ref, watch} from "vue";
import type {Template} from "@/api/query/interface/model";
import EditButton from "@/components/common/editButton.vue";
import ListWrapper from "@/components/wrapper/listWrapper.vue";
import {useToast} from "primevue/usetoast";
import type {User} from "@/api/query/interface/user";
import InviteUser from "@/_common/userManagement/components/inviteUser.vue";
import RemoveUser from "@/_common/userManagement/components/removeUser.vue";

const appStore = useAppStore();
const userManagementStore = useUserManagementStore();
const toaster = useToast();
const search = ref<string>('');
const list = ref<Array<User>>([]);

// -- loading

onMounted((): void => {
  if (appStore.organisation) {
    void userManagementStore.loadUsers(appStore.organisation.id);
  }
});

const organisationId = computed((): number | null => {
  if (appStore.organisation) {
    return appStore.organisation.id;
  }
  return null;
});

watch(organisationId, (value) => {
  if (value) {
    void userManagementStore.loadUsers(value);
  }
});

// -- search on the users

const filteredUsers = computed((): Array<User> => {
  if (search.value === '') {
    return userManagementStore.users;
  } else {
    const filterValue = search.value.toLowerCase();
    const filter = event => event.firstname.toLowerCase().includes(filterValue)
        || event.lastname.toLowerCase().includes(filterValue)
        || event.email.toLowerCase().includes(filterValue);
    return userManagementStore.users.filter(filter);
  }
});

// -- functions

function refreshUsers() {
  void userManagementStore.reloadUsers();
}

function selectUser(id: number) {
  void userManagementStore.loadUser(id);
}

function selectedClass(id: number) {
  return (id === userManagementStore.userId)
      ? "border-2 border-indigo-500"
      : "border-b-[1px] border-gray-300 dark:border-gray-600"
}

// -- get the icon for the user

function getIcon(user:User):string {
  if (user.organisationPermissions[0].roles.includes('ROLE_ORGANISATION_ADMIN')) {
    return 'user-gear';
  } else if (user.organisationPermissions[0].roles.includes('ROLE_ORGANISATION_USER')) {
    return 'user';
  } else if (user.organisationPermissions[0].roles.includes('ROLE_ORGANISATION_VIEWER')) {
    return 'glasses';
  } else {
    return 'user-xmark';
  }
}

// -- toggle interface to invite a user

const op = ref();

const toggle = (event: any) => {
  op.value.toggle(event);
};

function userInvited() {
  search.value = '';
  toggle('event');
}

</script>

<style scoped>

</style>