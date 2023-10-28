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
  <div id="listEnum">

    <!-- search & refresh ------------------------------------------------------------------------------------------ -->
    <div class="flex border-b-[1px] border-gray-300 dark:border-gray-600 h-12 p-2">
      <div>
        <Button
            v-if="!modelStore.isProjectLoading"
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
          @end="saveEnumOrder"
          ghost-class="ghost">
        <template #item="{ element }">

          <div class="bg-white hover:bg-indigo-50 h-12 p-2 dark:bg-gray-900 dark:hover:bg-indigo-900"
               :class="selectedClass(element.id)"
               @dblclick="selectDetail(element.id)">
            <div class="flex gap-2">
              <div class="flex-none mt-0.5 text-gray-300 hover:text-green-600 cursor-move mr-2 dark:text-gray-600"
                   v-if="search.length === 0">
                <i class="pi pi-bars handle"></i>
              </div>
              <div class="flex-none">
                <status-badge-small class="mt-1 mr-1"
                                    :id="element.artefactId"
                                    :status="element.documentor.status"/>
              </div>
              <div class="flex-grow line-clamp-1 h-6">
                <span class="font-bold text-sm">
                  {{ element.name }}
                </span>
                <span class="text-xs">
                  / {{ element.domainModel.name }} / {{ element.domainModel.module.name }}
                </span>
              </div>
              <div v-if="element.id != modelStore.enumSelectedId">
                <edit-button @click="selectDetail(element.id)"></edit-button>
              </div>
            </div>
          </div>

        </template>
      </Draggable>

    </list-wrapper>

    <!-- footer ---------------------------------------------------------------------------------------------------- -->
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
      <div class="flex-grow">&nbsp;</div>
      <div class="flex-none">
        <delete-enum/>
      </div>
    </div>
  </div>

  <OverlayPanel ref="op" :showCloseIcon="true" :dismissable="true">
    <add-enum v-on:added="enumAdded"/>
  </OverlayPanel>

</template>

<script setup lang="ts">
import ListWrapper from "@/components/wrapper/listWrapper.vue";
import {computed, onMounted, ref, watch} from "vue";
import EditButton from "@/components/common/editButton.vue";
import AddEnum from "@/components/model/addEnum.vue";
import {useModelStore} from "@/stores/model";
import type {Enum} from "@/api/query/interface/model";
import {useToast} from "primevue/usetoast";
import type {OrderEnumCommand} from "@/api/command/interface/enumCommands";
import Draggable from "vuedraggable";
import {orderEnums} from "@/api/command/model/orderEnum";
import StatusBadgeSmall from "@/components/common/statusBadgeSmall.vue";
import DeleteEnum from "@/components/model/deleteEnum.vue";

const modelStore = useModelStore();
const toaster = useToast();
const search = ref<string>('');
const list = ref<Array<Enum>>([]);

// -- refresh list

const filteredList = computed((): Array<Enum> => {
  if (modelStore.project) {
    if (search.value === '') {
      return modelStore.project.enums;
    } else {
      const filterValue = search.value.toLowerCase();
      const filter = event => event.name.toLowerCase().includes(filterValue)
          || event.namespace.toLowerCase().includes(filterValue)
          || event.domainModel.name.toLowerCase().includes(filterValue);
      return modelStore.project.enums.filter(filter);
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
  void modelStore.reLoadProject();
}

function selectDetail(id: number) {
  void modelStore.loadEnum(id);
}

// -- save order of the domain models ----------------------------------------------------------------------------------

const sequenceEnums = computed((): OrderEnumCommand => {
  let sequence = [];
  for (let i = 0; i < list.value.length; i++) {
    sequence.push(list.value[i].id);
  }
  return {sequence: sequence};
});

async function saveEnumOrder() {
  await orderEnums(sequenceEnums.value);
  toaster.add({
    severity: "success",
    summary: "Enum order saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadProject();
}

function selectedClass(id: number) {
  return (id === modelStore.enumSelectedId)
      ? "border-2 border-indigo-500"
      : "border-b-[1px] border-gray-300 dark:border-gray-600"
}

function correctionStyle(id: number) {
  return (id === modelStore.enumSelectedId)
      ? "margin-top:0.1rem;"
      : ""
}

// -- toggle add form

function enumAdded() {
  search.value = '';
  document
      .getElementById('enum-start')
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