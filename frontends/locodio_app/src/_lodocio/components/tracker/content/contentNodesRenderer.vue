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
  <div>
    <content-node-renderer v-for="node in nodesRef"
                           :node="node"
                           :key="node.uuid"/>
  </div>
</template>

<script setup lang="ts">
import ContentNodeRenderer from "@/_lodocio/components/tracker/content/contentNodeRenderer.vue";
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import type {TrackerNode} from "@/_lodocio/api/interface/tracker";
import {toRef} from 'vue';

const props = defineProps<{ nodes: Array<TrackerNode>, }>();
const trackerStore = useTrackerStore();
const nodesRef = toRef(props, 'nodes');

</script>

<style scoped>

</style>