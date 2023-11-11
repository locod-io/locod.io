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
  <div id="listExample">

    <!-- search & refresh ------------------------------------------------------------------------------------------ -->
    <div class="flex border-b-[1px] border-gray-300 dark:border-gray-600 h-12 p-2">
      <div>
        <Button
            v-if="!projectStore.isLoading"
            icon="pi pi-refresh"
            class="p-button-sm"
            @click="refreshList"/>
        <Button
            v-else
            class="p-button-sm"
            disabled
            icon="pi pi-spin pi-spinner"/>
      </div>
      <div class="w-full mr-2">
        <div class="p-input-icon-right w-full ml-2">
          <InputText
              type="text"
              class="w-full p-inputtext-sm"
              v-model="search"/>
          <i class="pi pi-search"/>
        </div>
      </div>
    </div>

    <!-- template list --------------------------------------------------------------------------------------------- -->
    <list-wrapper :estate-height="125">

      <Draggable
          v-model="list"
          tag="div"
          item-key="id"
          handle=".handle"
          @end="saveTrackerOrder"
          ghost-class="ghost">
        <div id="tracker-start"></div>
        <template #item="{ element }">
          <div class="bg-white hover:bg-indigo-50 h-12 p-2 dark:bg-gray-900 dark:hover:bg-indigo-900"
               :id="'dm-'+element.id"
               :class="selectedClass(element.id)"
               @dblclick="selectDetail(element.id)">

            <div class="flex gap-2">

              <div class="flex-none">
                <div class="mt-1 text-gray-300 hover:text-green-600 cursor-move mr-2 dark:text-gray-600"
                     v-if="search.length === 0">
                  <i class="pi pi-bars handle"></i>
                </div>
              </div>

              <div class="flex-none w-12">
                <span class="rounded-full px-2 text-white font-bold text-xs py-1 mt-0.5"
                      :style="'background-color:'+element.color">
                  {{ element.code }}
                </span>
              </div>

              <div class="flex-grow line-clamp-1 gap-2 h-6 mt-0.5" :style="correctionStyle(element.id)">
                <span class="font-bold text-sm">
                  {{ element.name }}
                </span>
              </div>

              <div class="flex-none text-sm mt-1 mr-1" v-if="element.id != projectStore.selectedTrackerId">
                <edit-button @click="selectDetail(element.id)"/>
              </div>

            </div>
          </div>
        </template>
      </Draggable>

    </list-wrapper>

    <!-- add button ------------------------------------------------------------------------------------------------ -->
    <div class="border-t-[1px] border-gray-300 dark:border-gray-600 h-12 p-2 flex gap-2">
      <div class="flex-none">
        <Button
            icon="pi pi-plus"
            class="p-button-sm p-button-icon"
            @click="toggle"
            aria-haspopup="true"
            aria-controls="overlay_panel"
        />
      </div>
      <div class="flex-grow">
        &nbsp;
      </div>
      <div class="flex-none">
        <delete-tracker/>
      </div>
    </div>
  </div>

  <OverlayPanel ref="op" :showCloseIcon="true" :dismissable="true">
    <add-tracker v-on:added="trackerAdded"/>
  </OverlayPanel>

</template>

<script setup lang="ts">
import ListWrapper from "@/components/wrapper/listWrapper.vue";
import {computed, onMounted, ref, watch} from "vue";
import EditButton from "@/components/common/editButton.vue";
import Draggable from "vuedraggable";
import {useToast} from "primevue/usetoast";
import type {Tracker} from "@/_lodocio/api/interface/tracker";
import {useAppStore} from "@/stores/app";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import type {OrderTrackerCommand} from "@/_lodocio/api/command/tracker/orderTracker";
import {orderTracker} from "@/_lodocio/api/command/tracker/orderTracker";
import AddTracker from "@/_lodocio/components/trackers/addTracker.vue";
import DeleteTracker from "@/_lodocio/components/trackers/deleteTracker.vue";

const appStore = useAppStore();
const projectStore = useDocProjectStore();
const toaster = useToast();
const search = ref<string>('');
const list = ref<Array<Tracker>>([]);

// -- refresh list -----------------------------------------------------------------------------------------------------

const filteredList = computed((): Array<Tracker> => {
  if (projectStore.docProject) {
    if (search.value === '') {
      return projectStore.docProject.trackers;
    } else {
      const filterValue = search.value.toLowerCase();
      const filter = event => event.name.toLowerCase().includes(filterValue)
          || event.code.toLowerCase().includes(filterValue)
      return projectStore.docProject.trackers.filter(filter);
    }
  } else {
    return [];
  }
});

watch(filteredList, (): void => {
  list.value = JSON.parse(JSON.stringify(filteredList.value))
});

onMounted((): void => {
  list.value = JSON.parse(JSON.stringify(filteredList.value))
});

function refreshList() {
  void projectStore.reloadProject();
}

function selectDetail(id: number) {
  void projectStore.loadTrackerDetail(id);
}

function selectedClass(id: number) {
  return (id === projectStore.selectedTrackerId)
      ? "border-2 border-indigo-500"
      : "border-b-[1px] border-gray-300 dark:border-gray-600"
}

function correctionStyle(id: number) {
  return (id === projectStore.selectedTrackerId)
      ? "margin-top:0.1rem;"
      : ""
}

// -- save order of the domain models ----------------------------------------------------------------------------------

const sequenceTrackers = computed((): OrderTrackerCommand => {
  let sequence = [];
  for (let i = 0; i < list.value.length; i++) {
    sequence.push(list.value[i].id);
  }
  return {sequence: sequence};
});

async function saveTrackerOrder() {
  await orderTracker(sequenceTrackers.value);
  toaster.add({
    severity: "success",
    summary: "Tracker order saved",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await projectStore.reloadProject();
}

// -- toggle add form --------------------------------------------------------------------------------------------------

function trackerAdded() {
  search.value = '';
  document
      .getElementById('tracker-start')
      ?.scrollIntoView({behavior: "smooth", block: "nearest"});
  toggle('event');
}

const op = ref();
const toggle = (event: any) => {
  op.value.toggle(event);
};

</script>

<style scoped>
</style>