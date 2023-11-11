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
  <div id="navigationAddNode">

    <Draggable v-bind="dragOptions"
               v-model="library"
               :group="{ name: 'node', pull: 'clone', put: false }"
               :clone="cloneElement"
               item-key="uuid"
               handle=".handle">

      <template #item="{ element }">
        <div class="h-12">
          <div class="flex gap-2 py-0.5 px-2 mt-2 handle cursor-move rounded-full bg-gray-200 dark:bg-gray-500">
            <div class="mt-0.5">
              <i class="pi pi-plus-circle"/>
            </div>
            <div class="text-sm mt-0.5 font-bold">{{ element.name }}</div>
          </div>
        </div>
      </template>

    </Draggable>

  </div>
</template>

<script setup lang="ts">
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import Draggable from "vuedraggable";
import {ref} from "vue";
import {v4 as uuidv4} from 'uuid';

const trackerStore = useTrackerStore();

const nodeId = ref<number>(0);
const library = ref([{name: 'Add item', uuid: uuidv4()}]);

function cloneElement() {
  trackerStore.renumberTree();
  return {
    name: `${trackerStore.tracker.code} item ${nodeId.value++}`,
    id: 0,            // ID generated from the database
    uuid: uuidv4(),
    number: '0',
    level: 0,
    isOpen: true,
    description: "",
    artefactId:0,
    relatedIssues: [],
    status: {name: 'New', color:'CCC', flowOut:[]},
  }
};

const dragOptions = {
  // animation: 200,
  group: "library-group",
  disabled: false,
  // ghostClass: "ghost"
};

</script>

<style scoped>

</style>