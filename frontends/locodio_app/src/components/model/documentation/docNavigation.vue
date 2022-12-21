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
  <div id="docNavigation">

    <!-- toolbar -->
    <div class="p-1 flex">
      <div>
        <Button @click="openAllItems"
                icon="pi pi-folder-open"
                class="p-button-sm p-button-outlined"
                label="open all"/>
      </div>
      <div class="ml-2">
        <Button @click="closeAllItems"
                icon="pi pi-folder"
                class="p-button-sm p-button-outlined"
                label="close all"/>
      </div>
      <div class="ml-2">
        <Button @click="showDialogSequenceItem"
                title="manage the sequences"
                class="p-button-sm p-button-outlined"
                icon="pi pi-sort-amount-down-alt"/>
      </div>
    </div>

    <!-- navigation -->
    <list-wrapper :estate-height="190">
      <div class="p-2">
        <div v-for="item in navigation">
          <doc-nav-item :item="item"/>
        </div>
      </div>
    </list-wrapper>

  </div>

  <!-- dialog for sequence items -->
  <Dialog
      v-model:visible="showSequenceDialog"
      header="Sort items" position="topleft"
      :modal="true"
  >
    <dialog-sequence-items/>
  </Dialog>

</template>

<script setup lang="ts">
import ListWrapper from "@/components/wrapper/listWrapper.vue";
import {useModelStore} from "@/stores/model";
import {computed, onMounted, ref, watch} from "vue";
import type {NavigationItem} from "@/components/model/documentation/model";
import DocNavItem from "@/components/model/documentation/docNavItem.vue";
import DialogSequenceItems from "@/components/model/documentation/dialogSequenceItems.vue";

const modelStore = useModelStore();
const navigation = ref<NavigationItem[]>([]);

// -- dialog for sequencing -----------------------------------------

const showSequenceDialog = ref<boolean>(false);

function showDialogSequenceItem() {
  showSequenceDialog.value = true;
}

// -- create a navigation -------------------------------------------

onMounted((): void => {
  fillNavigation();
});

const project = computed(() => {
  return modelStore.project;
});

watch(project, (): void => {
  fillNavigation();
});

function closeAllItems(): void {
  openItems(navigation.value,false);
}

function openAllItems(): void {
  openItems(navigation.value,true);
}

function openItems(items: NavigationItem[], isOpen: boolean) {
  for (const item of items) {
    item.isOpen = isOpen;
    if(item.children && item.children.length > 0) {
      openItems(item.children, isOpen);
    }
  }
}

function fillNavigation(): void {
  navigation.value = [];
  if (modelStore.project) {
    let index = 1;
    // -- loop over the modules ----------------------------------------------------------
    for (const module of modelStore.project.modules) {
      navigation.value.push({
        id: module.id,
        level: 1,
        levelLabel: index + '.',
        status: module.documentor.status,
        label: module.name,
        type: 'module',
        isOpen: true,
        children: getDomainModelsByModule(module.id, index + '.')
      })
      index++;
    }
  }
}

function getDomainModelsByModule(id: number, parentLevelLabel: string): NavigationItem[] {
  let result = [];
  if (modelStore.project) {
    let index = 1;
    for (const domainModel of modelStore.project.domainModels) {
      if (domainModel.module.id === id) {
        result.push({
          id: domainModel.id,
          level: 2,
          levelLabel: parentLevelLabel + '' + index + '.',
          status: domainModel.documentor.status,
          label: domainModel.name,
          type: 'domainModel',
          isOpen: true,
          children: getDomainRelations(domainModel.id, parentLevelLabel + '' + index + '.'),
        })
        index++;
      }
    }
  }
  return result;
}

function getDomainRelations(id: number, parentLevelLabel: string): NavigationItem[] {
  let result = [];

  if (modelStore.project) {

    // -- attributes ---------------------------------------------------
    let index = 1;
    result.push({
      id: 0,
      level: 3,
      levelLabel: parentLevelLabel + '' + index + '.',
      status: undefined,
      label: 'Attributes',
      type: 'attributes',
      isOpen: true,
    })

    // -- associations --------------------------------------------------
    index++;
    result.push({
      id: 0,
      level: 3,
      levelLabel: parentLevelLabel + '' + index + '.',
      status: undefined,
      label: 'Associations',
      type: 'associations',
      isOpen: true,
    })

    // -- enums --------------------------------------------------------
    index++;
    let enums = getEnumsByDomainModel(id, parentLevelLabel + '' + index + '.');
    if (enums.length > 0) {
      result.push({
        id: 0,
        level: 3,
        levelLabel: parentLevelLabel + '' + index + '.',
        status: undefined,
        label: 'Enums',
        type: 'enums',
        isOpen: true,
        children: enums,
      })
    } else {
      index--;
    }

    // -- queries ------------------------------------------------------
    index++;
    let queries = getQueriesByDomainModel(id, parentLevelLabel + '' + index + '.');
    if (queries.length > 0) {
      result.push({
        id: 0,
        level: 3,
        levelLabel: parentLevelLabel + '' + index + '.',
        status: undefined,
        label: 'Queries',
        type: 'queries',
        isOpen: true,
        children: queries,
      })
    } else {
      index--;
    }

    // -- commands -----------------------------------------------------
    index++;
    let commands = getCommandsByDomainModel(id, parentLevelLabel + '' + index + '.');
    if (commands.length > 0) {
      result.push({
        id: 0,
        level: 3,
        levelLabel: parentLevelLabel + '' + index + '.',
        status: undefined,
        label: 'Commands',
        type: 'commands',
        isOpen: true,
        children: commands,
      })
    } else {
      index--;
    }
  }
  return result;
}

function getEnumsByDomainModel(id: number, parentLevelLabel: string): NavigationItem[] {
  let result = [];
  if (modelStore.project) {
    let index = 1;
    for (const item of modelStore.project.enums) {
      if (item.domainModel.id === id) {
        result.push({
          id: item.id,
          level: 4,
          levelLabel: parentLevelLabel + '' + index + '.',
          status: item.documentor.status,
          label: item.name,
          type: 'enum',
          isOpen: true,
        })
        index++;
      }
    }
  }
  return result;
}

function getQueriesByDomainModel(id: number, parentLevelLabel: string): NavigationItem[] {
  let result = [];
  if (modelStore.project) {
    let index = 1;
    for (const item of modelStore.project.queries) {
      if (item.domainModel.id === id) {
        result.push({
          id: item.id,
          level: 4,
          levelLabel: parentLevelLabel + '' + index + '.',
          status: item.documentor.status,
          label: item.name,
          type: 'query',
          isOpen: true,
        })
        index++;
      }
    }
  }
  return result;
}

function getCommandsByDomainModel(id: number, parentLevelLabel: string): NavigationItem[] {
  let result = [];
  if (modelStore.project) {
    let index = 1;
    for (const item of modelStore.project.commands) {
      if (item.domainModel.id === id) {
        result.push({
          id: item.id,
          level: 4,
          levelLabel: parentLevelLabel + '' + index + '.',
          status: item.documentor.status,
          label: item.name,
          type: 'command',
          isOpen: true,
        })
        index++;
      }
    }
  }
  return result;

}

</script>