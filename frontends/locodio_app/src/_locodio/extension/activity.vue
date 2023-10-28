<template>
  <div class="border-b-[1px] border-gray-300 dark:border-gray-600 h-12">
    &nbsp;
  </div>
  <div v-for="auditItem in auditTrail" :key="auditItem.createdAtNumber"
       class="border-b-[1px] border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
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
          <div class="rounded-full px-0.5 py-0.5 text-white font-bold" :style="'background-color:'+auditItem.color">
            {{ auditItem.initials }}
          </div>
        </div>
      </div>
      <div class="capitalize mt-0.5">
        <div v-if="(auditItem.subject == 'domain-model')">domain model</div>
        <div v-else-if="(auditItem.subject == 'documentor')">workflow / documentation</div>
        <div v-else>
          {{ auditItem.subject }}
        </div>
      </div>
      <div class="mt-0.5">
        {{ auditItem.type }}
      </div>
      <div class="mt-0.5">
        <Timeago :datetime="auditItem.createdAt"/>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {useModelStore} from "@/stores/model";
import {useAppStore} from "@/stores/app";
import {onMounted, computed} from "vue";
import type {AuditItem} from "@/api/query/interface/audit";
import {Timeago} from 'vue2-timeago';

const props = defineProps<{
  type: 'module' | 'domain-model' | 'enum' | 'query' | 'command',
  id: number
}>();

const modelStore = useModelStore();
const appStore = useAppStore();

onMounted((): void => {
  void modelStore.loadAuditTrails(props.type, props.id);
});

const auditTrail = computed((): Array<AuditItem> | undefined => {
  if (props.type == 'domain-model') return modelStore.domainModelAuditTrails;
  if (props.type == 'enum') return modelStore.enumAuditTrails;
  if (props.type == 'query') return modelStore.queryAuditTrails;
  if (props.type == 'command') return modelStore.commandAuditTrails;
  return modelStore.moduleAuditTrails;
});

</script>

<style scoped>

</style>