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

<style scoped>
#logo img {
  display: block;
  max-width: 100px !important;
  width: 100px !important;
}
</style>

<template>

  <div id="applicationHeader"
       class="flex flex-row border-b-[1px] border-gray-300 bg-gradient-to-r from-zinc-800 to-zinc-600 text-gray-50">

    <!--    <div class="basis-1/12 p-2">-->
    <!--      <div class="mt-1 ml-2">           -->
    <!--         <i class="pi pi-bars cursor-pointer" @click="toggleNavigation"></i> -->
    <!--      </div>-->
    <!--    </div>-->

    <div class="basis-1/12 p-2 ml-1">
      <div class="font-bold mt-1 text-gray-400">
        <router-link to="/" id="logo">
          <div class="flex">
            <div class="mr-4 mt-1"><font-awesome-icon icon="fa-solid fa-house" /></div>
            <img src="../../assets/locodio_w.svg">
          </div>
        </router-link>
      </div>
    </div>
    <div class="basis-4/12 p-2">
      <working-version-indicator/>
    </div>

    <div class="basis-3/12 p-2">
      <div class="flex flex-row-reverse text-xs mt-2">
        <div class="ml-2 pl-2 border-l-[1px]">
          <a class="hover:border-blue-500 hover:border-b-[3px]"
              href="/docs" target="_blank">Docs
            <font-awesome-icon icon="fa-solid fa-arrow-up-right-from-square" />
          </a>
        </div>
        <div>
          <a class="hover:border-blue-500 hover:border-b-[3px]"
              href="/app#/browse-templates" target="_blank">Browse Templates
            <font-awesome-icon icon="fa-solid fa-arrow-up-right-from-square" />
          </a>
        </div>
      </div>
    </div>

    <div class="basis-4/12 p-2 text-right">
      <div class="flex flex-row-reverse mt-1 mr-4" v-if="appStore.user">
        <div class="mr-2">
          <div @click="toggle"
               aria-haspopup="true"
               aria-controls="user_menu"
               class="rounded-full px-2 py-1 text-xs cursor-pointer"
               :style="'background-color:'+appStore.user.color">
            <i class="pi pi-user cursor-pointer"></i>&nbsp;&nbsp;{{appStore.user.initials}}
          </div>
        </div>
        <div class="mr-4 text-xs mt-1">
          Logged in as <strong>{{appStore.user.firstname}} {{appStore.user.lastname}}</strong>
        </div>
      </div>

    </div>
  </div>

  <!--  <Sidebar v-model:visible="showNavigation" position="left">-->
  <!--    <left-navigation/>-->
  <!--  </Sidebar>-->

  <Menu id="user_menu" ref="menu" :model="items" :popup="true" ></Menu>

</template>

<script setup lang="ts">
import {ref} from 'vue';
import LeftNavigation from "@/components/layout/leftNavigation.vue";
import router from "@/router";
import WorkingVersionIndicator from "@/components/layout/workingVersionIndicator.vue";
import {useAppStore} from "@/stores/app";

// -- variables

const showNavigation = ref(false);
const appStore = useAppStore();
const menu = ref();
const items = ref([
  {
    label: 'My organisations',
    icon: 'pi pi-building',
    command: () => {
      gotoRoute('myOrganisations')
    }
  },
  {
    label: 'My master templates',
    icon: 'pi pi-file',
    command: () => {
      gotoRoute('myMasterTemplates')
    }
  },
  {
    label: 'My profile',
    icon: 'pi pi-user',
    command: () => {
      gotoRoute('myProfile')
    }
  },
  {
    label: 'Logout',
    icon: 'pi pi-power-off',
    command: () => {
      logout()
    }
  },
]);

// -- functions

function gotoRoute(routeName: string) {
  router.push({name: routeName})
}

function logout() {
  window.location = '/logout'
}

function toggleNavigation() {
  showNavigation.value = true
}

const toggle = (event: any) => {
  menu.value.toggle(event);
};

</script>