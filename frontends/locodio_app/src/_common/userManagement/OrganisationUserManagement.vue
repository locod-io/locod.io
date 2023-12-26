<template>
  <Splitter :style="'background-color:'+appStore.backgroundColor+';'">
    <!-- list of users -->
    <SplitterPanel :size="30">
      <list-users/>
    </SplitterPanel>
    <!-- central control panel -->
    <SplitterPanel :size="70">
      <div v-if="userManagementStore.userDetailLoading">
        <loading-spinner></loading-spinner>
      </div>
      <div v-else-if="userManagementStore.userId !== 0">
        <user-detail/>
      </div>
    </SplitterPanel>
  </Splitter>
</template>

<script setup lang="ts">
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import {useAppStore} from "@/stores/app";
import {useUserManagementStore} from "@/_common/userManagement/store/userManagementStore";
import ListUsers from "@/_common/userManagement/components/listUsers.vue";
import UserDetail from "@/_common/userManagement/components/userDetail.vue";

const appStore = useAppStore();
const userManagementStore = useUserManagementStore();

</script>

<style scoped>

</style>