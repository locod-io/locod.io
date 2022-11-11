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
  <div id="detailWrapper" :style="'height:' + wrapperHeight">
    <slot>Default content for property slot</slot>
  </div>
</template>

<script setup lang="ts">
import {onBeforeMount, onUnmounted, ref} from "vue";

const wrapperHeight = ref("400px");
const props = defineProps<{ estateHeight: number }>();

function detailWrapperResizeHandler() {
  wrapperHeight.value = `${window.innerHeight - props.estateHeight}px`;
}

onBeforeMount(() => {
  wrapperHeight.value = `${window.innerHeight - props.estateHeight}px`;
  window.addEventListener("resize", detailWrapperResizeHandler);
});

onUnmounted(() => {
  window.removeEventListener("resize", detailWrapperResizeHandler);
});
</script>

<style scoped>
#detailWrapper {
  display: block;
  overflow-y: auto;
}
</style>
