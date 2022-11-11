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
  <div id="listWrapper" :style="'height:' + wrapperHeight">
    <slot>
      <div class="p-4 text-sm">No results found.</div>
    </slot>
  </div>
</template>

<script setup lang="ts">
import {onBeforeMount, onUnmounted, ref} from "vue";

const wrapperHeight = ref("400px");
const props = defineProps<{ estateHeight: number }>();

function listWrapperResizeHandler() {
  wrapperHeight.value = `${window.innerHeight - props.estateHeight}px`;
}

onBeforeMount(() => {
  wrapperHeight.value = `${window.innerHeight - props.estateHeight}px`;
  window.addEventListener("resize", listWrapperResizeHandler);
});

onUnmounted(() => {
  window.removeEventListener("resize", listWrapperResizeHandler);
});
</script>

<style scoped>
#listWrapper {
  display: block;
  overflow-y: auto;
}
</style>
