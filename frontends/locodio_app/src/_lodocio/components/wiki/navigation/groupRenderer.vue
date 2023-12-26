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
  <div id="navigationGroupRenderer"
       :class="selectedClass"
       class="text-sm">

    <!-- // render the group -->
    <div class="flex gap-2 font-bold p-2 dark:bg-gray-800 bg-gray-100"
         @dblclick="selectGroup">

      <div class="text-gray-300 hover:text-green-600 cursor-move mr-2 dark:text-gray-600">
        <i class="pi pi-bars handle"></i>
      </div>
      <div v-if="groupRef.level !== 0" class="pl-0.5">
        <span v-for="i in groupRef.level">
          &nbsp;&nbsp;&nbsp;
        </span>
      </div>
      <div>
        <div v-if="!groupRef.isOpen" @click="openGroup()">
          <font-awesome-icon :icon="['fas', 'circle-right']"/>
        </div>
        <div v-if="groupRef.isOpen" @click="closeGroup()">
          <font-awesome-icon :icon="['fas', 'circle-down']"/>
        </div>
      </div>
      <div @click="scrollToGroup">{{ groupRef.number }}.</div>
      <div @click="scrollToGroup" class="flex-grow line-clamp-1">{{ groupRef.name }}</div>
      <div @click="scrollToGroup" v-if="!groupRef.isOpen" class="flex-none line-clamp-1">
        <div class="text-xs mt-1 font-normal">
          {{ groupRef.nodes.length + groupRef.groups.length }} items
        </div>
      </div>
    </div>

    <div v-show="groupRef.isOpen">

      <!-- // the containing nodes  -->
      <Draggable :list="groupRef.nodes"
                 group="node"
                 v-bind="dragOptions"
                 tag="div"
                 item-key="uuid"
                 @change="renumberTree"
                 handle=".handle">
        <template #item="{ element }">
          <node-renderer :node="element"/>
        </template>
      </Draggable>

      <!-- // render the starting groups -->
      <Draggable tag="div"
                 class="py-2 border-b-[1px] dark:border-gray-700 border-gray-300 border-dashed"
                 :list="groupRef.groups"
                 v-bind="dragOptions"
                 group="group"
                 handle=".handle"
                 @change="renumberTree"
                 item-key="uuid">
        <template #item="{ element }">
          <group-renderer :group="element"/>
        </template>
      </Draggable>

    </div>
  </div>
</template>

<script setup lang="ts">
import {useWikiStore} from "@/_lodocio/stores/wiki";
import type {WikiNodeGroup} from "@/_lodocio/api/interface/wiki";
import {toRef} from "vue";
import Draggable from "vuedraggable";
import NodeRenderer from "@/_lodocio/components/wiki/navigation/nodeRenderer.vue";
import {computed} from "vue";

const wikiStore = useWikiStore();
const props = defineProps<{ group: WikiNodeGroup }>();
const groupRef = toRef(props, 'group');

const dragOptions = {
  // animation: 200,
  // group: "description",
  // disabled: false,
  ghostClass: "ghost"
};

const selectedClass = computed(() => {
  return (groupRef.value.id === wikiStore.wikiNodeGroupId)
      ? "border-2 border-indigo-500"
      : "border-b-[1px] border-gray-300 dark:border-gray-600"
});

function renumberTree() {
  wikiStore.renumberTree();
}

function closeGroup() {
  groupRef.value.isOpen = false;
  wikiStore.renumberTree();
}

function openGroup() {
  groupRef.value.isOpen = true;
  wikiStore.renumberTree();
}

function selectGroup() {
  wikiStore.selectGroup(groupRef.value.id);
  scrollToGroup();
}

function scrollToGroup() {
  document
      .getElementById(`content-group-${groupRef.value.uuid}`)
      ?.scrollIntoView({behavior: "smooth", block: "start"});
}

</script>

<style scoped>

</style>