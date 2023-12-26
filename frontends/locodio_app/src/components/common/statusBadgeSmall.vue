<template>
  <div id="statusBadge" class="text-xs">
    <div class="bg-gray-200 rounded-full flex w-20 dark:bg-gray-600">
      <div class="w-14 text-center font-bold pl-1" style="margin-top: 1px;">{{id}}</div>
      <div :style="'padding-top:1px;background-color:#'+status.color" :title="status.name"
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