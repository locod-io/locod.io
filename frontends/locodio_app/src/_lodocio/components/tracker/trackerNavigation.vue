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

  <div class="flex gap-2 border-b-[1px] border-gray-300 dark:border-gray-600 h-12" style="min-width:350px;">
    <div class="p-2">
      <Button
          v-if="!trackerStore.trackerIsReloading"
          icon="pi pi-refresh"
          class="p-button-sm"
          @click="refreshTracker"/>
      <Button
          v-else
          class="p-button-sm"
          disabled
          icon="pi pi-spin pi-spinner"/>
    </div>
    <div class="py-2">
      <Button @click="openGroups"
              title="open all groups"
              icon="pi pi-folder-open"
              class="p-button-sm p-button-outlined p-button-secondary p-button-icon-only"
      />
    </div>
    <div class="py-2">
      <Button @click="closeGroups"
              title="close all groups"
              icon="pi pi-folder"
              class="p-button-sm p-button-outlined p-button-secondary p-button-icon-only"
      />
    </div>

    <div class="flex-grow">&nbsp;</div>
    <navigation-add-group/>
    <navigation-add-node class="mr-2"/>
  </div>

  <list-wrapper :estate-height="125" class="bg-white dark:bg-gray-900">

    <!-- // render the starting nodes  -->
    <Draggable :list="trackerStore.tracker.nodes"
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
               :list="trackerStore.tracker.groups"
               v-bind="dragOptions"
               group="group"
               handle=".handle"
               @change="renumberTree"
               item-key="uuid">
      <template #item="{ element }">
        <div>
          <group-renderer :group="element"/>
        </div>
      </template>
    </Draggable>

  </list-wrapper>

  <div class="flex gap-2 border-t-[1px] border-gray-300 dark:border-gray-600 h-12">
    &nbsp;
  </div>

</template>

<script setup lang="ts">
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import NodeRenderer from "@/_lodocio/components/tracker/navigation/nodeRenderer.vue";
import GroupRenderer from "@/_lodocio/components/tracker/navigation/groupRenderer.vue";
import Draggable from "vuedraggable";
import NavigationAddGroup from "@/_lodocio/components/tracker/navigation/navigationAddGroup.vue";
import NavigationAddNode from "@/_lodocio/components/tracker/navigation/navigationAddNode.vue";
import ListWrapper from "@/components/wrapper/listWrapper.vue";

const trackerStore = useTrackerStore();

const dragOptions = {
  // animation: 200,
  // group: "description",
  // disabled: false,
  ghostClass: "ghost"
};

function renumberTree() {
  trackerStore.renumberTree();
}

function refreshTracker() {
  trackerStore.renumberTree();
}

function openGroups() {
  trackerStore.openGroups();
}

function closeGroups() {
  trackerStore.closeGroups();
}


</script>

<style scoped>

</style>