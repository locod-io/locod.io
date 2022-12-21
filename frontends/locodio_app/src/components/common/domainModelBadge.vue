<template>
  <div class="rounded-full text-xs rounded-full bg-blue-200 bold flex">
    <div class="px-2 py-1" v-if="domainModel.module">{{ domainModel.module.name }}</div>
    <div class="rounded-full text-xs px-2 py-1 rounded-full bg-blue-100 font-bold">
      {{ domainModel.name }}
    </div>
  </div>
</template>

<script setup lang="ts">
import type {DomainModel} from "@/api/query/interface/model";

const props = defineProps<{ domainModel: DomainModel }>();
</script>
