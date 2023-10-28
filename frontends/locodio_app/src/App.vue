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

  <!-- -- progress bar -->
  <vue-progress-bar/>

  <!-- -- application -->
  <div v-if="appStore.configLoaded">
    <Splitter :style="'background-color:'+appStore.backgroundColor+';'">
      <SplitterPanel :size="15">
        <!-- -- left navigation -->
        <left-side-bar/>
      </SplitterPanel>
      <SplitterPanel :size="85">
        <!-- -- application router -->
        <RouterView v-slot="{ Component }">
          <KeepAlive>
            <component :is="Component"/>
          </KeepAlive>
        </RouterView>
      </SplitterPanel>
    </Splitter>
  </div>

  <!-- -- loading screen -->
  <div v-else>
    <div id="background-color" class="bg-gradient-to-r from-black from-1% via-indigo-900 via-30% to-green-700">&nbsp;
    </div>
    <div style="position: absolute; top:5%;left:0;right:0">
      <div>
        <div>
          <div style="max-width: 450px;" class="mx-auto">
            <div class="bg-white mt-12 p-8 drop-shadow-xl">
              <loading-spinner/>
              <div class="mt-16 text-center dark:text-gray-900">
                Patience, as the gears of innovation turn,
                <br>your experience is just moments away.
              </div>
              <br><br><br><br>&nbsp;
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- -- toaster -->
  <Toast position="bottom-left"/>

</template>

<style scoped>

#background-color {
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  height: 40%;
}

</style>

<script setup lang="ts">
import {getCurrentInstance, onMounted} from "vue";
import axios from "axios";
import {useToast} from "primevue/usetoast";
import {useAppStore} from "@/stores/app";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import {useRouter} from "vue-router";
import LeftSideBar from "@/_common/leftSideBar/leftSideBar.vue";

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