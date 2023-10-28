<template>

  <project-navigation/>

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
import ProjectNavigation from "@/_lodocio/views/project/ProjectNavigation.vue";
import {onMounted, watch} from "vue";
import {useAppStore} from "@/stores/app";

const docProjectStore = useDocProjectStore();
const appStore = useAppStore();

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
              break;
            }
          }
          break;
        }
      }
    }
    if (_organisation && _docProject) {
      docProjectStore.setCurrentWorkspace(_organisation, _docProject);
    }
  }
}

</script>

<style scoped>

</style>