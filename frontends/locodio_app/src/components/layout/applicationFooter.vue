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
  <div id="applicationFooter" class="flex flex-row">
    <div class="basis-3/12 p-2">
      <div class="text-xs text-gray-500 flex">
        <div>
          <span class="border-[1px] rounded-3xl border-gray-500 p-1">CC</span>
          &nbsp;2022 &mdash; Made with
          <span class="text-red-600">&#10084;</span>
          in Belgium
        </div>
        <div class="flex ml-1.5">
          <div class="bg-black w-2">&nbsp;</div>
          <div class="bg-yellow-400 w-2">&nbsp;</div>
          <div class="bg-red-600 w-2">&nbsp;</div>
        </div>
      </div>
    </div>
    <div class="basis-9/12 text-right p-2">
      <div class="text-xs text-gray-300">
        <router-link to="/about">locod.io app ({{ version }})</router-link>
        using Vue {{ vueVersion }} - Pinia {{ piniaVersion }} - Router {{ routerVersion }}
        - PrimeVue {{ primeVersion }} - Typescript {{ typescriptVersion }} - Tailwind {{ tailwindVersion }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {version as piniaVersion} from "pinia/package.json";
import {version as vueVersion} from "vue/package.json";
import {version as primeVersion} from "primevue/package.json";
import {version as routerVersion} from "vue-router/package.json";
import {version as typescriptVersion} from "typescript/package.json";
import {version as tailwindVersion} from "tailwindcss/package.json";

const version = import.meta.env.VITE_VERSION as string;
</script>

<style scoped>

#applicationFooter {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
}

</style>