<!--
/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
-->

<template>
  <div v-if="modelStore.projectId !== 0">
    <RouterView v-slot="{ Component }">
      <KeepAlive>
        <component :is="Component"/>
      </KeepAlive>
    </RouterView>
  </div>
  <div v-else>
    <loading-spinner/>
  </div>

  <!-- -- documentor -->
  <Dialog v-model:visible="modelStore.showDocumentor"
          header="&nbsp;"
          :modal="true">
    <detail-documentor/>
  </Dialog>

</template>

<script setup lang="ts">
import {useModelStore} from "@/stores/model";
import {onMounted, watch} from "vue";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import {useOrganisationStore} from "@/stores/organisation";
import {useAppStore} from "@/stores/app";
import DetailDocumentor from "@/components/documentor/detailDocumentor.vue";
import {useLinearStore} from "@/stores/linear";

const props = defineProps<{
  organisationId: number;
  projectId: number;
}>();

const modelStore = useModelStore();
const organisationStore = useOrganisationStore();
const appStore = useAppStore();
const linearStore = useLinearStore();

onMounted((): void => {
  loadProject();
  loadMasterTemplates();
});

async function loadProject() {
  organisationStore.setWorkingVersion(props.organisationId, props.projectId);
  await modelStore.loadLists();
  await modelStore.loadProject(props.projectId);
  await linearStore.cacheIssuesByProject(props.projectId);
  appStore.setCurrentWorkspaceById(props.organisationId, props.projectId);
}

async function loadMasterTemplates() {
  await modelStore.loadMasterTemplates();
}

watch(props, (value) => {
  if (modelStore.projectId != value.projectId) {
    modelStore.projectId = 0;
    loadProject();
  }
});

</script>

<style scoped>

</style>