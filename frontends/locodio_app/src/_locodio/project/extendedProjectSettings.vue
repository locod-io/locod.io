<template>
  <div class="flex flex-row">
    <!-- general settings ------------------------------------------------------------------------------------------ -->
    <div class="basis-1/3 border-r-[1px] border-gray-300 dark:border-gray-600">
      <div class="flex border-b-[1px] border-gray-300 dark:border-gray-600 h-12 p-3 text-sm font-bold">
        General
      </div>
      <div class="p-4 border-b-[1px] border-gray-300 dark:border-gray-600">
        <change-project :project="appStore.project"/>
      </div>
    </div>

    <!-- -- description -------------------------------------------------------------------------------------------- -->
    <div class="basis-1/3 border-r-[1px] border-b-[1px] border-gray-300 dark:border-gray-600">
      <div class="flex border-b-[1px] border-gray-300 dark:border-gray-600 h-12 p-3  text-sm font-bold">
        Description
      </div>
      <div class="border-b-[1px] border-gray-300 dark:border-gray-600">
        <div class="text-sm">
          <simple-editor v-model="command.description"/>
        </div>
        <div class="p-4">
          <Button v-if="!isSaving"
                  @click="saveDescription"
                  icon="pi pi-save"
                  class="p-button-sm p-button-success"/>
          <Button v-else
                  disabled
                  icon="pi pi-spin pi-spinner"
                  class="p-button-sm p-button-success"/>
        </div>
      </div>

      <!-- -- related roadmaps ------------------------------------------------------------------------------------- -->
      <div class="flex border-b-[1px] border-gray-300 dark:border-gray-600 h-12 p-3 text-sm font-bold">
        Related Roadmaps
      </div>
      <div class="flex gap-2 border-b-[1px] border-gray-300 dark:border-gray-600 px-4 py-2 text-sm">
        <div class="flex flex-grow">
          <MultiSelect v-model="commandRelatedRoadmaps.relatedRoadmaps"
                       display="chip"
                       :options="appStore.organisation?.relatedRoadmaps"
                       optionLabel="name"
                       placeholder="Select roadmaps"
                       :maxSelectedLabels="3"
                       class="w-full"
          />
        </div>
        <div class="flex-none">
          <div class="mt-1.5">
            <Button v-if="!isSaving"
                    @click="saveRelatedRoadmaps"
                    icon="pi pi-save"
                    class="p-button-sm p-button-success"/>
            <Button v-else
                    disabled
                    icon="pi pi-spin pi-spinner"
                    class="p-button-sm p-button-success"/>
          </div>
        </div>
      </div>

      <!-- -- related projects ------------------------------------------------------------------------------------- -->
      <div class="flex border-b-[1px] border-gray-300 dark:border-gray-600 h-12 p-3 text-sm font-bold">
        Related projects
      </div>
      <div class="flex gap-2 border-b-[1px] border-gray-300 dark:border-gray-600 px-4 py-2 text-sm">
        <div class="flex flex-grow">
          <MultiSelect v-model="commandRelatedProjects.relatedProjects"
                       display="chip"
                       :options="appStore.organisation?.relatedProjects"
                       optionLabel="name"
                       placeholder="Select projects"
                       :maxSelectedLabels="1"
                       class="w-full"
          />
        </div>
        <div class="flex-none">
          <div class="mt-1.5">
            <Button v-if="!isSaving"
                    @click="saveRelatedProjects"
                    icon="pi pi-save"
                    class="p-button-sm p-button-success"/>
            <Button v-else
                    disabled
                    icon="pi pi-spin pi-spinner"
                    class="p-button-sm p-button-success"/>
          </div>
        </div>
      </div>
      <div>
        <div class="flex gap-2 border-b-[1px] border-gray-300 dark:border-gray-600 h-12 bg-white dark:bg-gray-900"
             v-for="project in appStore.project.relatedProjects">
          <div class="flex-none py-3 pl-3">
            <a :href="project.url" target="_blank"><i class="pi pi-link"></i></a>
          </div>
          <div class="flex-grow line-clamp-1 py-3">
            <related-project-renderer :project="project"/>
          </div>
          <div class="flex-none line-clamp-1 py-3">
            {{ project.teams.length }} <i class="pi pi-users"></i>
          </div>
          <div class="flex-none line-clamp-1 py-3 pr-3">
            <div class="cursor-pointer" @click="showDialogProjectDetailFn(project.id)">
              <i class="pi pi-eye"/>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="basis-1/3"></div>

  </div>

  <!-- -- detail related project -->
  <div>
    <Dialog
        header="&nbsp;"
        v-model:visible="showDialogProjectDetail"
        :modal="true">
      <dialog-project-detail :project-id="selectedProjectId"/>
    </Dialog>
  </div>

</template>

<script setup lang="ts">
import {useAppStore} from "@/stores/app";
import {ref} from "vue";
import ChangeProject from "@/components/organisation/changeProject.vue";
import SimpleEditor from "@/_common/editor/simpleEditor.vue";
import type {ChangeProjectDescriptionCommand} from "@/api/command/organisation/changeProjectDescription";
import {changeProject} from "@/api/command/user/changeProject";
import {useToast} from "primevue/usetoast";
import {changeProjectDescription} from "@/api/command/organisation/changeProjectDescription";
import type {ChangeProjectRelatedProjectsCommand} from "@/api/command/organisation/changeProjectRelatedProjects";
import {changeProjectRelatedProjects} from "@/api/command/organisation/changeProjectRelatedProjects";
import RelatedProjectRenderer from "@/_locodio/project/relatedProjectRenderer.vue";
import type {ChangeProjectRelatedRoadmapsCommand} from "@/api/command/organisation/changeProjectRelatedRoadmaps";
import {changeProjectRelatedRoadmaps} from "@/api/command/organisation/changeProjectRelatedRoadmaps";
import DialogProjectDetail from "@/_lodocio/components/roadmap/dialogProjectDetail.vue";

const appStore = useAppStore();
const isSaving = ref<boolean>(false);
const toaster = useToast();

// -- project detail -------------------------------------------------------

const showDialogProjectDetail = ref<boolean>(false);
const selectedProjectId = ref<string>('');

function showDialogProjectDetailFn(projectId: string) {
  selectedProjectId.value = projectId;
  showDialogProjectDetail.value = true;
}

// -- change description ----------------------------------------------------

const command = ref<ChangeProjectDescriptionCommand>({
  id: appStore.project.id ?? 0,
  description: appStore.project.description ?? '',
});

async function saveDescription() {
  isSaving.value = true;
  await changeProjectDescription(command.value);
  toaster.add({
    severity: "success",
    summary: "Description saved",
    detail: "",
    life: appStore.toastLifeTime,
  });
  isSaving.value = false;
  await appStore.loadFullProject(appStore.projectId);
}

// -- related projects --------------------------------------------------------

const commandRelatedProjects = ref<ChangeProjectRelatedProjectsCommand>({
  id: appStore.project.id ?? 0,
  relatedProjects: appStore.project?.relatedProjects ?? [],
});

async function saveRelatedProjects() {
  isSaving.value = true;
  await changeProjectRelatedProjects(commandRelatedProjects.value);
  toaster.add({
    severity: "success",
    summary: "Related projects saved",
    detail: "",
    life: appStore.toastLifeTime,
  });
  isSaving.value = false;
  await appStore.loadFullProject(appStore.projectId);
}

// -- related roadmaps

const commandRelatedRoadmaps = ref<ChangeProjectRelatedRoadmapsCommand>({
  id: appStore.project.id ?? 0,
  relatedRoadmaps: appStore.project?.relatedRoadmaps ?? [],
});

async function saveRelatedRoadmaps() {
  isSaving.value = true;
  await changeProjectRelatedRoadmaps(commandRelatedRoadmaps.value);
  toaster.add({
    severity: "success",
    summary: "Related roadmaps saved",
    detail: "",
    life: appStore.toastLifeTime,
  });
  isSaving.value = false;
  await appStore.loadFullProject(appStore.projectId);
}

</script>

<style scoped>

</style>