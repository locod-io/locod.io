<template>
  <span id="emailLabel">
    {{ emailPrefix }}
  </span>
</template>

<script setup lang="ts">
import {computed} from "vue";

const props = defineProps<{
  email: string,
}>();

const emailPrefix = computed(() => props.email.split('@')[0]);
</script>
