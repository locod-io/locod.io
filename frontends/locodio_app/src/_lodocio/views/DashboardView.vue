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

  <div v-if="docProjectStore.docProjectId !== 0">
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
import {useDocProjectStore} from "@/_lodocio/stores/project";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import {onMounted, watch} from "vue";
import {useAppStore} from "@/stores/app";
import {useModelStore} from "@/stores/model";
import {useOrganisationStore} from "@/stores/organisation";
import {useTrackerStore} from "@/_lodocio/stores/tracker";

const docProjectStore = useDocProjectStore();
const appStore = useAppStore();
const modelStore = useModelStore();
const organisationStore = useOrganisationStore();
const trackerStore = useTrackerStore();

const props = defineProps<{
  organisationId: number;
  docProjectId: number;
}>();

onMounted((): void => {
  loadDocProject();
});

watch(props, (value) => {
  if (docProjectStore.docProjectId != value.docProjectId) {
    docProjectStore.docProjectId = 0;
    loadDocProject();
  }
});

async function loadDocProject() {
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
      trackerStore.reset();
      await docProjectStore.setCurrentWorkspace(_organisation, _docProject);
    }
  } else {
      await docProjectStore.loadWorkspace(props.organisationId,props.organisationId);
      organisationStore.setWorkingVersion(props.organisationId, docProjectStore.docProject.project.id);
      await appStore.setCurrentWorkspaceById(props.organisationId, docProjectStore.docProject.project.id);
  }
}

</script>

<style scoped>

</style>