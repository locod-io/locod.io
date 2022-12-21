<template>
  <div id="statusBadgeVerySmall" class="text-xs">
    <div :style="'font-size:8px;background-color:#'+status.color" :title="status.name"
         class="rounded-full w-4 text-white font-semibold text-center">
      {{ statusLetter }}
    </div>
  </div>
</template>

<script setup lang="ts">
import type {ModelStatus} from "@/api/query/interface/model";
import {computed} from "vue";

// -- props
const props = defineProps<{
  status: ModelStatus,
}>();

// -- computed
const statusLetter = computed(() => {
  return props.status.name.substring(0, 1).toUpperCase();
});
</script>