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
  <div id="rightExtensionWrapper" :style="'height:' + wrapperHeight">
    <slot>
    </slot>
  </div>
</template>

<script setup lang="ts">
import {onBeforeMount, onUnmounted, ref} from "vue";

const wrapperHeight = ref("400px");
const props = defineProps<{ estateHeight: number }>();

function rightExtensionWrapperResizeHandler() {
  wrapperHeight.value = `${window.innerHeight - props.estateHeight}px`;
}

onBeforeMount(() => {
  wrapperHeight.value = `${window.innerHeight - props.estateHeight}px`;
  window.addEventListener("resize", rightExtensionWrapperResizeHandler);
});

onUnmounted(() => {
  window.removeEventListener("resize", rightExtensionWrapperResizeHandler);
});
</script>

<style scoped>
#rightExtensionWrapper {
  display: block;
  overflow-y: auto;
}
</style>