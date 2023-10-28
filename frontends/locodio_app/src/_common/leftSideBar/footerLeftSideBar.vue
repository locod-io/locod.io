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
  <div class="flex text-xs p-2 gap-2 border-t-[1px] border-gray-300 h-12 pt-4 dark:border-gray-600">
    <div class="flex-none text-gray-400">
      <div class="cursor-pointer pl-1" @click="toggleTheme">
        <div v-if="appStore.theme === 'light'">
          <font-awesome-icon :icon="['fas', 'moon']"/>
        </div>
        <div v-else>
          <font-awesome-icon :icon="['fas', 'sun']"/>
        </div>
      </div>
    </div>
    <div class="flex-grow text-center text-gray-400 line-clamp-1">
      <router-link to="/about">v{{ version }}</router-link>
    </div>
    <div class="flex-grow text-center">
      <a class="hover:border-blue-500 hover:border-b-[1px] pb-1"
         href="/docs" target="_blank">Docs
      </a>
    </div>
    <div class="flex-grow text-center">
      <a class="hover:border-blue-500 hover:border-b-[1px] pb-1"
         href="/app#/browse-templates" target="_blank">Browse&nbsp;Templates
      </a>
    </div>
  </div>
</template>

<script setup lang="ts">
import {useAppStore} from "@/stores/app";
import {getCurrentInstance} from "vue";

const version = import.meta.env.VITE_VERSION as string;
const appStore = useAppStore();
const app = getCurrentInstance();

function toggleTheme() {
  if (appStore.theme === 'light') {
    appStore.setTheme('dark');

  } else {
    appStore.setTheme('light');
  }
}

</script>