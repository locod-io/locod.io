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
  <div id="contentGroupRenderer">
    <div v-for="group in groupsRef">
      <div :class="selectedClass(group.id)">
        <content-group-renderer :group="group"/>
        <div v-show="group.isOpen">
          <content-nodes-renderer :nodes="group.nodes"/>
          <content-groups-renderer :groups="group.groups"/>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import type {TrackerNodeGroup} from "@/_lodocio/api/interface/tracker";
import ContentNodesRenderer from "@/_lodocio/components/tracker/content/contentNodesRenderer.vue";
import {toRef} from 'vue';
import ContentGroupRenderer from "@/_lodocio/components/tracker/content/contentGroupRenderer.vue";

const trackerStore = useTrackerStore();
const props = defineProps<{
  groups: Array<TrackerNodeGroup>,
}>();

const groupsRef = toRef(props, 'groups');

function selectedClass(groupId:number) {
  return (groupId === trackerStore.trackerNodeGroupId)
      ? "border-2 border-indigo-500"
      : "border-b-[1px] border-gray-300 dark:border-gray-600"
}

</script>

<style scoped>

</style>