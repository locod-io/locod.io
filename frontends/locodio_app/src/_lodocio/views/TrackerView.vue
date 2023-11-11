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

  <div v-if="trackerStore.trackerId !== 0">
    <RouterView v-slot="{ Component }">
      <KeepAlive>
        <component :is="Component"/>
      </KeepAlive>
    </RouterView>
  </div>
  <div v-else>
    <loading-spinner/>
  </div>

</template>

<script setup lang="ts">
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import {useAppStore} from "@/stores/app";
import {onMounted, watch} from "vue";
import {useOrganisationStore} from "@/stores/organisation";
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import {useLinearStore} from "@/stores/linear";

const docProjectStore = useDocProjectStore();
const appStore = useAppStore();
const organisationStore = useOrganisationStore();
const trackerStore = useTrackerStore();
const linearStore = useLinearStore();

const props = defineProps<{
  organisationId: number;
  docProjectId: number;
  trackerId: number;
}>();

onMounted((): void => {
  loadTrackerView();
});

watch(props, (value) => {
  if (trackerStore.trackerId != value.trackerId) {
    trackerStore.trackerId = 0;
    loadTrackerView();
  }
});

async function loadTrackerView() {
  let _organisation = undefined;
  let _docProject = undefined;
  if (appStore.configLoaded) {
    if (appStore.userProjects) {
      for (const organisation of appStore.userProjects?.collection) {
        if (organisation.id === props.organisationId) {
          _organisation = organisation;
          for (const project of organisation.projects) {
            if (project.docProject.id === props.docProjectId) {
              _docProject = project.docProject;
              await appStore.setCurrentWorkspaceById(props.organisationId, project.id);
              break;
            }
          }
          break;
        }
      }
    }
    if (_organisation && _docProject) {
      void docProjectStore.setCurrentWorkspace(_organisation, _docProject);
    }
  } else {
    await docProjectStore.loadWorkspace(props.organisationId, props.organisationId);
    organisationStore.setWorkingVersion(props.organisationId, docProjectStore.docProject.project.id);
    await appStore.setCurrentWorkspaceById(props.organisationId, docProjectStore.docProject.project.id);
  }
  trackerStore.trackerNodeId = 0;
  trackerStore.trackerNodeGroupId = 0;
  await trackerStore.loadTracker(props.trackerId);
  await cacheLinearIssues();
}

async function cacheLinearIssues() {
  console.log('-- cache linear issues');
  await linearStore.cacheIssuesByTracker(props.trackerId);
}

async function cacheLinearDocuments() {
  if(appStore.projectId != 0) {
    console.log('-- cache linear documents');
    await linearStore.cacheDocumentsByProject(appStore.projectId);
  }
}

</script>

<style scoped>

</style>