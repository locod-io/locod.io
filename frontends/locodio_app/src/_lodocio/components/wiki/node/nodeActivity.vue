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
  <div class="border-b-[1px] border-gray-300 dark:border-gray-600 h-12">
    &nbsp;
  </div>
  <div v-for="auditItem in auditItems" :key="auditItem.createdAtNumber"
       class="border-b-[1px] border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
    <audit-item-renderer :audit-item="auditItem"/>
  </div>
</template>

<script setup lang="ts">
import {useWikiStore} from "@/_lodocio/stores/wiki";
import {onMounted, ref} from "vue";
import type {AuditItem} from "@/api/query/interface/audit";
import {getWikiNodeActivity} from "@/_lodocio/api/query/wiki/getWikiNodeActivity";
import AuditItemRenderer from "@/_lodocio/components/audit/auditItemRenderer.vue";

const wikiStore = useWikiStore();
const auditItems = ref<Array<AuditItem>>([]);

onMounted((): void => {
  loadAuditItems();
});

async function loadAuditItems() {
  if (wikiStore.wikiNodeId !== 0) {
    auditItems.value = await getWikiNodeActivity(wikiStore.wikiNodeId);
  }
}

</script>

<style scoped>

</style>