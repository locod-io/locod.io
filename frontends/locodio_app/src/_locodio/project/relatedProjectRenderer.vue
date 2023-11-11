<template>
  <div class="flex gap-2">
    <div v-if="projectCode !== ''">
      <div class="rounded-full px-2 py1 text-xs text-white mt-0.5 font-bold"
           :style="'background-color:'+project.color">{{ projectCode }}
      </div>
    </div>
    <div class="flex-grow line-clamp-1" style="margin-top:-2px;">
      {{ projectName }}
    </div>
  </div>
</template>

<script setup lang="ts">
import type {Project} from "@/api/query/interface/linear";
import {computed} from "vue";

const props = defineProps<{ project: Project }>();

const projectCode = computed((): string => {
  let _array = props.project.name.split(" - ");
  if (_array.length === 2) {
    return _array[0];
  } else {
    return "";
  }
});

const projectName = computed((): string => {
  let _array = props.project.name.split(" - ");
  if (_array.length === 2) {
    return _array[1];
  } else {
    return props.project.name;
  }
});

</script>

<style scoped>

</style>