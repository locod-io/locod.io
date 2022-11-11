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
  <div id="userHeading" :style="'background-color:'+appStore.user.color"
       class="p-1 text-center text-white text-sm font-bold">
    {{label}}
  </div>
</template>

<script setup lang="ts">
import {useAppStore} from "@/stores/app";

const props = defineProps<{
  label: string,
}>();

const appStore = useAppStore();

</script>
