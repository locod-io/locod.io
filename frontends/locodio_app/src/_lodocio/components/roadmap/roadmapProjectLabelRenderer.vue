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
  <div class="flex gap-2">
    <div>
      <div class="flex rounded-full px-1 py-0.5 pt-1 text-xs font-bold bg-white text-gray-600 gap-1">
        <div style="font-size:1.3em;">
          <i class="projectstatus-backlog" style="font-size:1.2em;" v-if="status=='backlog'"></i>
          <i class="projectstatus-planned" style="font-size:1.2em;" v-if="status=='planned'"></i>
          <i class="projectstatus-cancelled" style="font-size:1.2em;" v-if="status=='cancelled'"></i>
          <i class="projectstatus-completed" style="font-size:1.2em;" v-if="status=='completed'"></i>
          <i class="projectstatus-paused" style="font-size:1.2em;" v-if="status=='paused'"></i>
          <i class="projectstatus-in-progress" style="font-size:1.2em;" v-if="status=='started'"></i>
        </div>
        <div class="flex-grow line-clamp-1 mt-0.5 pr-1" v-if="projectCode !== ''">
          {{ projectCode }}
        </div>
      </div>
    </div>
    <div class="flex-grow line-clamp-1 text-white">
      {{ projectName }}
    </div>
  </div>
</template>

<script setup lang="ts">
import {computed} from "vue";

const props = defineProps<{ label: string, status: string }>();

const projectCode = computed((): string => {
  let _array = props.label.split(" - ");
  if (_array.length === 2) {
    return _array[0];
  } else {
    return "";
  }
});

const projectName = computed((): string => {
  let _array = props.label.split(" - ");
  if (_array.length === 2) {
    return _array[1];
  } else {
    return props.label;
  }
});

</script>

<style scoped>

</style>