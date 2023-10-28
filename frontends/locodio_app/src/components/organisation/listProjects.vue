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
  <div>
    <Draggable
        v-model="listProjects"
        tag="div"
        :group="'project'+organisationId"
        item-key="name"
        @end="saveProjectOrder"
        handle=".handle-project"
        ghost-class="ghost">
      <template #item="{ element }">

        <div class="flex border-b-[1px] pt-2 pb-2 border-gray-300 dark:border-gray-600">
          <div class="ml-2 mt-1.5">
            <div class="mt-1 text-gray-200 hover:text-green-600 cursor-move mr-2 dark:text-gray-600">
              <i class="pi pi-bars handle-project"></i>
            </div>
          </div>
          <div class="mt-1.5">
            <edit-button @click="editProject(element)"/>
          </div>
          <div class="ml-2 mt-1">
            <div :style="'background-color:'+element.color+';'"
                 class="rounded-full text-white text-xs mt-0.5 py-1 px-2">{{ element.code }}
            </div>
          </div>
          <div class="flex-grow ml-2 mt-1.5 line-clamp-1 font-bold">{{ element.name }}</div>
          <div class="mt-1.5 ml-2 pr-4">
            <a :href="'/api/model/project/'+element.id+'/download'"
               class="text-gray-300 hover:text-gray-500 dark:text-gray-600"
               title="download project as json">
              <font-awesome-icon icon="fa-solid fa-cloud-arrow-down" />
            </a>
          </div>
        </div>
      </template>
    </Draggable>
  </div>

  <!-- -- dialog add project ----------------------------------------- -->
  <div class="mt-4 text-center pb-4">
    <Button label="ADD PROJECT"
            icon="pi pi-plus"
            @click="toggle"
            aria-haspopup="true"
            aria-controls="overlay_panel"
            class="p-button-sm p-button-outlined p-button-secondary"/>
  </div>
  <OverlayPanel ref="op" :showCloseIcon="true" :dismissable="true">
    <add-project :organisation-id="organisationId" v-on:added="added"/>
  </OverlayPanel>

  <!-- -- dialog edit project ----------------------------------------- -->
  <Dialog
      v-model:visible="isDialogProject"
      header="&nbsp;"
      :modal="true"
  >
    <change-project
        style="width: 400px;"
        :project="selectedProject"
        v-on:changed="changed"/>
  </Dialog>

</template>

<script setup lang="ts">
import {computed, onMounted, ref, watch} from "vue";
import Draggable from "vuedraggable";
import EditButton from "@/components/common/editButton.vue";
import AddProject from "@/components/organisation/addProject.vue";
import type {UserProject} from "@/api/query/interface/user";
import type {OrderProjectCommand} from "@/api/command/interface/userCommands";
import {useAppStore} from "@/stores/app";
import {useToast} from "primevue/usetoast";
import {orderProjects} from "@/api/command/user/orderProject";
import ChangeProject from "@/components/organisation/changeProject.vue";

const props = defineProps<{
  projects: Array<UserProject>,
  organisationId: number,
}>();

const appStore = useAppStore();
const toaster = useToast();
const isDialogProject = ref<boolean>(false);
const selectedProject = ref<UserProject>(undefined);
const listProjects = ref<Array<UserProject>>([]);

onMounted((): void => {
  listProjects.value = JSON.parse(JSON.stringify(props.projects))
});

function editProject(project: UserProject) {
  selectedProject.value = project;
  isDialogProject.value = true;
}

function changed() {
  isDialogProject.value = false;
  listProjects.value = JSON.parse(JSON.stringify(props.projects));
}

function added() {
  toggle('event');
  listProjects.value = JSON.parse(JSON.stringify(props.projects));
}

const sequenceProjects = computed((): OrderProjectCommand => {
  let sequence = [];
  for (let i = 0; i < listProjects.value.length; i++) {
    sequence.push(listProjects.value[i].id);
  }
  return {sequence: sequence};
});

async function saveProjectOrder() {
  await orderProjects(sequenceProjects.value);
  toaster.add({
    severity: "success",
    summary: "Project order saved",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await appStore.reloadUserProjects();
}

// -- toggle form
const op = ref();
const toggle = (event: any) => {
  op.value.toggle(event);
};

</script>