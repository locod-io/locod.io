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
  <div class="flex gap-1 text-xs px-2 py-1">
    <div>
      <div v-if="(auditItem.initials == 'XX')">
        <div class="rounded-full px-0.5 py-0.5 bg-gray-300 dark:bg-gray-600">
          <font-awesome-icon :icon="['fas', 'user-secret']"/>
        </div>
      </div>
      <div v-else-if="(auditItem.initials == 'BOT')">
        <div class="rounded-full px-0.5 py-0.5 bg-gray-300 dark:bg-gray-600">
          <font-awesome-icon :icon="['fas', 'robot']"/>
        </div>
      </div>
      <div v-else>
        <div class="rounded-full px-0.5 py-0.5 text-white font-bold"
             :style="'background-color:'+auditItem.color">
          {{ auditItem.initials }}
        </div>
      </div>
    </div>
    <div class="capitalize mt-0.5">
      {{trackerStore.tracker.code}} item
    </div>
    <div class="mt-0.5">
      {{ auditItem.type }}
    </div>
    <div class="mt-0.5">
      <Timeago :datetime="auditItem.createdAt"/>
    </div>
  </div>
</template>

<script setup lang="ts">
import type {AuditItem} from "@/api/query/interface/audit";
import {Timeago} from 'vue2-timeago';
import {useTrackerStore} from "@/_lodocio/stores/tracker";

const props = defineProps<{ auditItem: AuditItem }>();
const trackerStore = useTrackerStore();

</script>

<style scoped>

</style>