<template>
  <div id="statusBadge" class="text-xs">
    <div class="bg-gray-300 rounded-full flex w-16">
      <div class="w-10 text-center font-bold" style="font-size:10px;">{{id}}</div>
      <div :style="'background-color:#'+status.color" :title="status.name"
           class="rounded-full w-6 text-white text-center font-bold">
        {{ statusLetter }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type {ModelStatus} from "@/api/query/interface/model";
import {computed} from "vue";

// -- props
const props = defineProps<{
  status: ModelStatus,
  id: string,
}>();

// -- computed
const statusLetter = computed(() => {
  return props.status.name.substring(0, 1).toUpperCase();
});
</script>