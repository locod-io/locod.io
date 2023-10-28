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
  <div id="sideBarWrapper" :style="'height:' + wrapperHeight">
    <slot>
    </slot>
  </div>
</template>

<script setup lang="ts">
import {onBeforeMount, onUnmounted, ref} from "vue";

const wrapperHeight = ref("400px");
const props = defineProps<{ estateHeight: number }>();

function sideBarWrapperResizeHandler() {
  wrapperHeight.value = `${window.innerHeight - props.estateHeight}px`;
}

onBeforeMount(() => {
  wrapperHeight.value = `${window.innerHeight - props.estateHeight}px`;
  window.addEventListener("resize", sideBarWrapperResizeHandler);
});

onUnmounted(() => {
  window.removeEventListener("resize", sideBarWrapperResizeHandler);
});
</script>

<style scoped>
#sideBarWrapper {
  display: block;
  overflow-y: auto;
}
</style>
