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

  <vue-progress-bar></vue-progress-bar>

  <!-- -- header  -->
  <header>
    <application-header/>
  </header>

  <!-- -- application router -->
  <div v-if="appStore.configLoaded">
    <RouterView v-slot="{ Component }">
      <KeepAlive>
        <component :is="Component"/>
      </KeepAlive>
    </RouterView>
  </div>
  <div v-else>
    <loading-spinner/>
  </div>

  <!-- -- footer -->
  <footer>
    <application-footer/>
  </footer>

  <!-- -- toaster -->
  <Toast position="bottom-right"/>

</template>

<script setup lang="ts">
import ApplicationHeader from "@/components/layout/applicationHeader.vue";
import ApplicationFooter from "@/components/layout/applicationFooter.vue";
import {getCurrentInstance, onMounted} from "vue";
import axios from "axios";
import {useToast} from "primevue/usetoast";
import {useAppStore} from "@/stores/app";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import {useRouter} from "vue-router";

const toaster = useToast();
const appStore = useAppStore();
const app = getCurrentInstance();
const progress = app?.appContext.config.globalProperties.$Progress ?? '';
const router = useRouter();

/* eslint-disable no-unused-vars */
router.beforeEach((to, from, next) => {
  progress.start();
  next();
});
router.afterEach((to, from) => {
  progress.finish();
});
/* eslint-enable no-unused-vars */

onMounted((): void => {
  // -- some error feedback for the user
  axios.interceptors.response.use(
      response => {
        progress.finish();
        return response;
      }
      , function (error) {
        toaster.add({
          severity: "error",
          summary: "Oops, something went wrong.",
          detail: "Please contact the administrator",
          life: appStore.toastLifeTime,
        });
        progress.fail();
        return Promise.reject(error);
      }
  );

  axios.interceptors.request.use((request) => {
    progress.start();
    return request;
  });

  // -- load user data
  loadUserData();
});

async function loadUserData() {
  await appStore.loadUser();
  await appStore.loadUserProjects();
}

</script>