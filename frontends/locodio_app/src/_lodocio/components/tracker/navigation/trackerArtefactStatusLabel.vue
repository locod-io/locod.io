<!--
/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
-->

<template>
  <div id="statusBadge" class="text-xs">
    <div class="bg-gray-300 rounded-full flex w-24 dark:bg-gray-600">
      <div class="w-20 text-center font-bold pl-1" style="margin-top: 1px;">{{ trackerStore.tracker.code }}-{{ node.artefactId }}</div>
      <div :style="'padding-top:1px;background-color:#'+node.status.color" :title="node.status.name"
           class="rounded-full w-6 text-white text-center font-bold">
        {{ statusLetter }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import type {TrackerNode} from "@/_lodocio/api/interface/tracker";
import {computed} from "vue";

const trackerStore = useTrackerStore();
const props = defineProps<{
  node: TrackerNode,
}>();

const statusLetter = computed(() => {
  return props.node.status.name.substring(0, 1).toUpperCase();
});
</script>

<style scoped>

</style>