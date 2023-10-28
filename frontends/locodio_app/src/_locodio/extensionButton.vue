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
  <div @click="toggleRightExtension"
      class="border-l-[1px] border-gray-300 dark:border-gray-600 h-12 w-12 m-02 py-2.5 px-5 cursor-pointer hover:bg-indigo-100 dark:hover:bg-indigo-800">
    <div v-if="!isExtended">
      &gt;
    </div>
    <div v-else>
      &lt;
    </div>
 </div>
</template>

<script setup lang="ts">
import {computed} from "vue";
import {useModelStore} from "@/stores/model";

const props = defineProps<{
  type: 'module' | 'domain-model' | 'enum' | 'query' | 'command',
  id: number
}>();

const modelStore = useModelStore();

function toggleRightExtension(){
  switch (props.type) {
    case "domain-model":
      modelStore.domainModelExtendedView = !modelStore.domainModelExtendedView;
      break;
    case "command":
      modelStore.commandExtendedView = !modelStore.commandExtendedView;
      break;
    case "enum":
      modelStore.enumExtendedView = !modelStore.enumExtendedView;
      break;
    case "module":
      modelStore.moduleExtendedView = !modelStore.moduleExtendedView;
      break;
    case "query":
      modelStore.queryExtendedView = !modelStore.queryExtendedView
      break;
  }
}

const isExtended = computed((): boolean => {
  if (props.type == 'domain-model') return modelStore.domainModelExtendedView;
  if (props.type == 'enum') return modelStore.enumExtendedView;
  if (props.type == 'query') return modelStore.queryExtendedView;
  if (props.type == 'command') return modelStore.commandExtendedView;
  return modelStore.moduleExtendedView;
});

</script>

<style scoped>

</style>