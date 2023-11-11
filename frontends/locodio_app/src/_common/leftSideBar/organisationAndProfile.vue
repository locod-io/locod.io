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
  <div class="p-2 border-b-[1px] border-gray-300 h-12 dark:border-gray-600">
    <div class="w-full flex">

      <div class="grow">
        <div id="workingVersionIndicator"
             class="flex p-1 gap-2 cursor-pointer"
             @click="toggleOrganisationsMenu"
             v-if="appStore.organisation && appStore.project">
          <div class="rounded-full text-white text-xs py-1 px-2"
               :style="'background-color:'+appStore.organisation.color">
            {{ appStore.organisation.code }}
          </div>
          <div class="flex-grow line-clamp-1">{{ appStore.organisation.name }}</div>
        </div>
        <div v-else>
          <div class="pointer p-1.5">
            <router-link to="/">
              <div><i class="pi pi-home"/></div>
            </router-link>
          </div>
        </div>
      </div>

      <div class="no-flex">
        <div @click="toggle" v-if="appStore.user"
             aria-haspopup="true"
             aria-controls="user_menu"
             class="rounded-full px-2 py-2 text-xs cursor-pointer text-white"
             :style="'background-color:'+appStore.user.color">
          {{ appStore.user.initials }}
        </div>
      </div>
    </div>
  </div>

  <Menu id="user_menu" ref="menu" :model="items" :popup="true"></Menu>
  <Menu id="organisation_menu" ref="menuOrganisation" :model="itemsOrganisation" :popup="true"></Menu>

  <Dialog
      v-if="appStore.organisation"
      v-model:visible="isDialogOrganisation"
      header="&nbsp;"
      :modal="true"
  >
    <change-organisation
        style="width: 400px;"
        :organisation="appStore.organisation as UserOrganisation"
        v-on:changed="organisationChanged"/>
  </Dialog>

</template>

<script setup lang="ts">
import {ref} from "vue";
import {useAppStore} from "@/stores/app";
import router from "@/router";
import ChangeOrganisation from "@/components/organisation/changeOrganisation.vue";
import type {UserOrganisation} from "@/api/query/interface/user";

const appStore = useAppStore();
const menu = ref();
const menuOrganisation = ref();
const isDialogOrganisation = ref<boolean>(false);

function editOrganisation() {
  isDialogOrganisation.value = true;
}

async function organisationChanged() {
  await appStore.reloadUserProjects();
  if (appStore.organisation && appStore.project) {
    appStore.setCurrentWorkspaceById(appStore.organisation.id, appStore.project.id);
  }
  isDialogOrganisation.value = false;
}

const items = ref(createUserNavigation());

function createUserNavigation() {
  let _items = [];
  // -- my organisation
  _items.push({
    label: 'My ' + appStore.user.organisationLabel + 's',
    icon: 'pi pi-building',
    command: () => {
      gotoRoute('myOrganisations')
    }
  });
  // -- my mega roadmap
  if (appStore.user.hasLodocio) {
    _items.push({
      label: 'My Roadmap',
      icon: 'pi pi-map',
      command: () => {
        gotoRoute('myMegaRoadMap')
      }
    });
  }
  // -- my master templates
  if (appStore.user.hasLocodio) {
    _items.push({
      label: 'My Master Templates',
      icon: 'pi pi-file',
      command: () => {
        gotoRoute('myMasterTemplates')
      }
    });
  }
  // -- my profile
  _items.push({
    label: 'My Profile',
    icon: 'pi pi-user',
    command: () => {
      gotoRoute('myProfile')
    }
  });
  _items.push({
    label: 'Logout',
    icon: 'pi pi-power-off',
    command: () => {
      logout()
    }
  });
  return _items;
}

const itemsOrganisation = ref(createOrganisationNavigation());

function createOrganisationNavigation() {
  let _items = [];
  _items.push({
    label: 'Switch ' + appStore.user.organisationLabel,
    icon: 'pi pi-building',
    command: () => {
      gotoRoute('home')
    }
  });
  _items.push({
    label: appStore.user.organisationLabel.charAt(0).toUpperCase() + appStore.user.organisationLabel.slice(1) + ' Settings',
    icon: 'pi pi-pencil',
    command: () => {
      if (appStore.organisation) {
        editOrganisation();
      }
    }
  });

  // -- my organisation roadmap
  if (appStore.user.hasLodocio) {
    _items.push({
      label: appStore.user.organisationLabel.charAt(0).toUpperCase() + appStore.user.organisationLabel.slice(1) + ' Roadmap',
      icon: 'pi pi-map',
      command: () => {
        if (appStore.organisation) {
          goToDocumentationPart('full-roadmap')
        }
      }
    });
  }

  _items.push({
    label: 'Logout',
    icon: 'pi pi-power-off',
    command: () => {
      logout()
    }
  });

  return _items;
}

// -- functions ----------------------------------------------------------

function gotoRoute(routeName: string) {
  router.push({name: routeName})
}

function logout() {
  window.location = '/logout'
}

const toggle = (event: any) => {
  menu.value.toggle(event);
};

const toggleOrganisationsMenu = (event: any) => {
  menuOrganisation.value.toggle(event);
};

function goToDocumentationPart(part: string) {
  if (appStore.organisation && appStore.project) {
    router.push('/doc/o/' + appStore.organisation.id + '/p/' + appStore.project.docProject.id + '/' + part);
  }
}

</script>