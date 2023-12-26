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
  <general-top-bar type="my-organisations"/>
  <div class="overflow-auto">
    <div class="flex flex-wrap p-2">

      <Draggable
          v-model="appStore.userProjects.collection"
          tag="div"
          group="organisation"
          item-key="name"
          class="flex flex-wrap"
          handle=".handle"
          @end="saveOrganisationOrder"
          ghost-class="ghost">
        <template #item="{ element }">

          <div class="border-2 rounded-xl w-96 ml-2 mt-2 bg-white dark:bg-gray-900" :style="'border-color:'+element.color">
            <div class="p-2 text-white rounded-t-lg flex" :style="'background-color:'+element.color">
              <div class="ml-2 mt-1">
                <div class="mt-1 text-gray-200 hover:text-green-600 cursor-move mr-2">
                  <i class="pi pi-bars handle"></i>
                </div>
              </div>
              <div class="mt-1.5" v-if="isOrganisationAdmin(element)">
                <edit-button @click="editOrganisation(element)"/>
              </div>
              <div class="ml-2 mt-1.5">{{ element.name }}</div>
            </div>

            <!-- // projects  -->
            <list-projects :projects="element.projects" :organisation-id="element.id"/>

            <!-- // users -->
            <list-users :users="element.users"/>

          </div>
        </template>
      </Draggable>

      <div class="ml-8 mt-8">
        <Button :label="'ADD '+ appStore.user.organisationLabel.toUpperCase() "
                icon="pi pi-plus"
                @click="toggle"
                aria-haspopup="true"
                aria-controls="overlay_panel"
                class="p-button-sm p-button-outlined p-button-secondary"/>
      </div>

    </div>
  </div>

  <OverlayPanel ref="op" :showCloseIcon="true" :dismissable="true">
    <add-organisation v-on:added="toggle"/>
  </OverlayPanel>

  <Dialog
      v-model:visible="isDialogOrganisation"
      header="&nbsp;"
      :modal="true"
  >
    <change-organisation
        style="width: 400px;"
        :organisation="selectedOrganisation"
        v-on:changed="(isDialogOrganisation = false)"/>
  </Dialog>

</template>

<script setup lang="ts">
import EditButton from "@/components/common/editButton.vue";
import {computed, ref} from "vue";
import Draggable from "vuedraggable";
import ListProjects from "@/components/organisation/listProjects.vue";
import ListUsers from "@/components/organisation/listUsers.vue";
import AddOrganisation from "@/components/organisation/addOrganisation.vue";
import {useAppStore} from "@/stores/app";
import type {OrderProjectCommand} from "@/api/command/interface/userCommands";
import {useToast} from "primevue/usetoast";
import {orderOrganisation} from "@/api/command/user/orderOrganisation";
import type {UserOrganisation} from "@/api/query/interface/user";
import ChangeOrganisation from "@/components/organisation/changeOrganisation.vue";
import UserHeading from "@/components/user/userHeading.vue";
import GeneralTopBar from "@/_common/topBar/generalTopBar.vue";

const appStore = useAppStore();
const toaster = useToast();
const isDialogOrganisation = ref<boolean>(false);
const selectedOrganisation = ref<UserOrganisation>(undefined);

function editOrganisation(organisation: UserOrganisation) {
  selectedOrganisation.value = organisation;
  isDialogOrganisation.value = true;
}

function isOrganisationAdmin(organisation: UserOrganisation): boolean {
  return appStore.isOrganisationAdmin(organisation.id);
}

// -- change the order

const sequenceOrganisations = computed((): OrderProjectCommand => {
  let sequence = [];
  if (appStore.userProjects) {
    for (let i = 0; i < appStore.userProjects.collection.length; i++) {
      sequence.push(appStore.userProjects.collection[i].id);
    }
  }
  return {sequence: sequence};
});

async function saveOrganisationOrder() {
  await orderOrganisation(sequenceOrganisations.value);
  toaster.add({
    severity: "success",
    summary: "Organisation order saved",
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