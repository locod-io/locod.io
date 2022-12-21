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
  <div id="overviewNavigation">
    <list-wrapper :estate-height="197">

      <table cellpadding="5" class="w-full" border="1">

        <tr class="border-b-[1px] border-gray-500">
          <td>
            <div v-if="isAllDeselected">
              <div class="cursor-pointer" @click="selectAllItems">
                <font-awesome-icon icon="fa-solid fa-toggle-off"/>
              </div>
            </div>
            <div v-else-if="isAllSelected">
              <div class="cursor-pointer" @click="deSelectAllItems">
                <font-awesome-icon icon="fa-solid fa-toggle-on"/>
              </div>
            </div>
            <div v-else>
              <div class="cursor-pointer" @click="selectAllItems">
                <font-awesome-icon icon="fa-solid fa-toggle-on"/>
              </div>
            </div>
          </td>
          <td width="80%">
            <!-- show modules -->
            <div class="flex ml-4">
              <div v-if="schemaStore.showModules">
                <div class="cursor-pointer" @click="showModules(false)">
                  <font-awesome-icon icon="fa-solid fa-toggle-on"/>
                </div>
              </div>
              <div v-else>
                <div class="cursor-pointer" @click="showModules(true)">
                  <font-awesome-icon icon="fa-solid fa-toggle-off"/>
                </div>
              </div>
              <div class="ml-2 text-sm mt-0.5">Modules?</div>
            </div>
          </td>
          <td align="center">
            <div class="cursor-pointer" title="only show name" @click="toggleAll('basic')">
              <font-awesome-icon icon="fa-solid fa-square"/>
            </div>
          </td>
          <td align="center">
            <div class="cursor-pointer" title="show basic information" @click="toggleAll('regular')">
              <font-awesome-icon icon="fa-solid fa-table-cells-large"/>
            </div>
          </td>
          <td align="center">
            <div class="cursor-pointer" title="show all information" @click="toggleAll('full')">
              <font-awesome-icon icon="fa-solid fa-table-cells"/>
            </div>
          </td>
          <td>&nbsp;</td>
        </tr>

        <tr v-for="item in navigation" class="border-b-[1px]">
          <td>
            <div v-if="item.isSelected">
              <div class="cursor-pointer" @click="selectItem(item,false)">
                <font-awesome-icon icon="fa-solid fa-toggle-on"/>
              </div>
            </div>
            <div v-else>
              <div class="cursor-pointer" @click="selectItem(item,true)">
                <font-awesome-icon icon="fa-solid fa-toggle-off"/>
              </div>
            </div>
          </td>
          <td class="text-sm" width="80%">
            <div v-if="item.isSelected">
              <div class="cursor-pointer flex" @click="selectItem(item,false)">
                <status-badge-very-small :status="item.status" class="mt-0.5"/>
                <div class="ml-2"><strong>{{ item.name }}</strong></div>
                <div v-if="item.module && schemaStore.showModules" class="text-xs ml-1 mt-1"> ({{ item.module }})</div>
              </div>
            </div>
            <div v-else>
              <div class="cursor-pointer flex" @click="selectItem(item,true)">
                <status-badge-very-small :status="item.status" class="mt-0.5"/>
                <div class="ml-2">{{ item.name }}</div>
                <div v-if="item.module && schemaStore.showModules" class="text-xs ml-1 mt-1"> ({{ item.module }})</div>
              </div>
            </div>
          </td>
          <td>
            <div v-if="item.isSelected" title="only show name" @click="toggleType(item,'basic')">
              <div v-if="item.isBasic" class="px-1 rounded-sm cursor-pointer">
                <font-awesome-icon icon="fa-solid fa-square"/>
              </div>
              <div v-else class="px-1 cursor-pointer text-gray-300">
                <font-awesome-icon icon="fa-solid fa-square"/>
              </div>
            </div>
          </td>
          <td>
            <div v-if="item.isSelected" title="show basic information" @click="toggleType(item,'regular')">
              <div v-if="item.isRegular" class="px-1 rounded-sm cursor-pointer">
                <font-awesome-icon icon="fa-solid fa-table-cells-large"/>
              </div>
              <div v-else class="px-1 cursor-pointer text-gray-300">
                <font-awesome-icon icon="fa-solid fa-table-cells-large"/>
              </div>
            </div>
          </td>
          <td>
            <div v-if="item.subjectType == 'model'">
              <div v-if="item.isSelected" title="show all information" @click="toggleType(item,'full')">
                <div v-if="item.isFull" class="px-1 rounded-sm cursor-pointer">
                  <font-awesome-icon icon="fa-solid fa-table-cells"/>
                </div>
                <div v-else class="px-1 cursor-pointer text-gray-300">
                  <font-awesome-icon icon="fa-solid fa-table-cells"/>
                </div>
              </div>
            </div>
          </td>
          <td>&nbsp;</td>
        </tr>

      </table>

    </list-wrapper>
  </div>
</template>

<script setup lang="ts">
import {useModelStore} from "@/stores/model";
import ListWrapper from "@/components/wrapper/listWrapper.vue";
import {computed, onMounted, ref, watch} from "vue";
import type {navigationItem} from "@/components/overview/model";
import {useSchemaStore} from "@/stores/schema";
import StatusBadgeVerySmall from "@/components/common/statusBadgeVerySmall.vue";

const modelStore = useModelStore();
const schemaStore = useSchemaStore();
const navigation = ref<Array<navigationItem>>([]);

// -- set the navigation items

onMounted((): void => {
  fillNavigation();
});

const project = computed(() => {
  return modelStore.project;
});

watch(project, (): void => {
  fillNavigation();
});

function fillNavigation(): void {
  navigation.value = [];
  if (modelStore.project) {
    for (const domainModel of modelStore.project.domainModels) {
      navigation.value.push({
        id: domainModel.uuid,
        name: domainModel.name,
        module: domainModel.module?.name ?? '',
        status: domainModel.documentor.status,
        subject: domainModel,
        subjectType: "model",
        isSelected: true,
        isBasic: false,
        isRegular: false,
        isFull: true,
      })
    }
    for (const enumType of modelStore.project.enums) {
      navigation.value.push({
        id: enumType.uuid,
        name: enumType.name,
        module: enumType.domainModel.module?.name ?? '',
        status: enumType.documentor.status,
        subject: enumType,
        subjectType: "enum",
        isSelected: true,
        isBasic: false,
        isRegular: true,
        isFull: false,
      })
    }
    // -- sort along module
    if(schemaStore.showModules) {
      navigation.value.sort(compare);
    }

    schemaStore.configuration = navigation.value;
    schemaStore.incrementCounter();
  }
}

function compare(a, b) {
  let comparison = 0;
  if (a.module > b.module) {
    comparison = 1;
  } else {
    comparison = -1;
  }
  return comparison;
}

// -- selection functions

function selectItem(item: navigationItem, value: boolean) {
  item.isSelected = value;
  schemaStore.configuration = navigation.value;
  schemaStore.incrementCounter();
}

// -- toggle functions

function toggleAll(type: string): void {
  for (const item of navigation.value) {
    toggleType(item, type);
  }
}

function toggleType(item: navigationItem, type: string): void {
  switch (type) {
    case 'basic':
      item.isBasic = true;
      item.isRegular = false;
      item.isFull = false;
      break;
    case 'regular':
      item.isBasic = false;
      item.isRegular = true;
      item.isFull = false;
      break;
    case 'full':
      if (item.subjectType === 'model') {
        item.isBasic = false;
        item.isRegular = false;
        item.isFull = true;
      } else {
        item.isBasic = false;
        item.isRegular = true;
        item.isFull = false;
      }
      break;
  }
  schemaStore.configuration = navigation.value;
  schemaStore.incrementCounter();
}

// -- bulk functions

function showModules(show:boolean):void {
  schemaStore.showModules = show;
  fillNavigation();
}

function selectAllItems(): void {
  for (const item of navigation.value) {
    item.isSelected = true;
  }
  schemaStore.configuration = navigation.value;
  schemaStore.incrementCounter();
}

function deSelectAllItems(): void {
  for (const item of navigation.value) {
    item.isSelected = false;
  }
  schemaStore.configuration = navigation.value;
  schemaStore.incrementCounter();
}

// -- computed properties

const isAllSelected = computed((): boolean => {
  for (const item of navigation.value) {
    if (!item.isSelected) return false;
  }
  return true;
});

const isAllDeselected = computed((): boolean => {
  for (const item of navigation.value) {
    if (item.isSelected) return false;
  }
  return true;
});

</script>