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
  <div id="navigationNodeRenderer"
       @dblclick="selectNode"
       class="text-sm">

    <div class="flex gap-2 p-2" :class="selectedClass">
      <div class="text-gray-300 hover:text-green-600 cursor-move mr-2 dark:text-gray-600">
        <i class="pi pi-bars handle"></i>
      </div>
      <div v-if="nodeRef.level !== 0" class="flex-none">
        <span v-for="i in nodeRef.level">
          &nbsp;&nbsp;&nbsp;
        </span>
      </div>
      <div class="flex-none" @click="scrollToNode">
        <font-awesome-icon :icon="['far', 'circle-right']"/>
      </div>
      <div class="flex-none" @click="scrollToNode">
        {{ nodeRef.number }}.
      </div>
      <div class="flex-none cursor-pointer" @click="scrollToNode">
        <tracker-artefact-status-label :node="nodeRef"/>
      </div>
      <div class="flex-grow line-clamp-1" @click="scrollToNode">
        {{ nodeRef.name }}
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import type {TrackerNode} from "@/_lodocio/api/interface/tracker";
import {toRef} from "vue";
import TrackerArtefactStatusLabel from "@/_lodocio/components/tracker/navigation/trackerArtefactStatusLabel.vue";
import {computed} from "vue";

const trackerStore = useTrackerStore();
const props = defineProps<{
  node: TrackerNode,
}>();
const nodeRef = toRef(props, 'node');

function selectNode() {
  trackerStore.selectNode(nodeRef.value.id);
  scrollToNode();
}

const selectedClass = computed(() => {
  return (nodeRef.value.id === trackerStore.trackerNodeId)
      ? "border-2 border-indigo-500"
      : "border-b-[1px] border-gray-300 dark:border-gray-600"
});

function scrollToNode() {
  document
      .getElementById(`content-node-${nodeRef.value.uuid}`)
      ?.scrollIntoView({behavior: "smooth", block: "start"});
}

</script>

<style scoped>

</style>