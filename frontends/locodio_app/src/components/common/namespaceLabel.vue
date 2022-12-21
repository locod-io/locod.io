<template>
  <div class="text-xs text-gray-400">
    {{ namespace }}
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{ namespace: string }>();
</script>