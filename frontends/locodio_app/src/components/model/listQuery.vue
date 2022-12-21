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
  <div id="listExample">

    <!-- search & refresh ------------------------------------------------------------------------------------------ -->
    <div class="flex p-2">
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
    <list-wrapper :estate-height="249">

      <div id="query-start"></div>
      <Draggable
          v-model="list"
          tag="div"
          item-key="id"
          handle=".handle"
          @end="saveSequence"
          ghost-class="ghost">
        <template #item="{ element }">

          <div class="m-2 p-2 border-2 rounded-xl bg-white hover:bg-indigo-100"
               :class="selectedClass(element.id)"
               @dblclick="selectDetail(element.id)">
            <div class="flex flex-row-reverse">
              <edit-button @click="selectDetail(element.id)"></edit-button>
              <div>
                <div class="mt-1.5 text-gray-300 hover:text-green-600 cursor-move mr-2" v-if="search.length === 0">
                  <i class="pi pi-bars handle"></i>
                </div>
              </div>
              <div class="w-full">
                <div class="flex">
                  <status-badge-small class="mt-1 mr-1"
                                      :id="'Q-'+element.id"
                                      :status="element.documentor.status"/>
                  <div class="font-semibold">
                    {{ element.name }}
                  </div>
                </div>
                <namespace-label :namespace="element.namespace"/>
                <div class="mt-2 flex">
                  <domain-model-badge :domain-model="element.domainModel"/>
                </div>
              </div>
            </div>
          </div>

        </template>
      </Draggable>

    </list-wrapper>

    <!-- add button ------------------------------------------------------------------------------------------------ -->
    <div class="p-2">
      <div class="text-right">
        <Button
            label="ADD"
            icon="pi pi-plus"
            class="p-button-sm"
            @click="toggle"
            aria-haspopup="true"
            aria-controls="overlay_panel"
        />
      </div>
    </div>
  </div>

  <OverlayPanel ref="op" :showCloseIcon="true" :dismissable="true">
    <add-query v-on:added="queryAdded"/>
  </OverlayPanel>

</template>

<script setup lang="ts">
import ListWrapper from "@/components/wrapper/listWrapper.vue";
import {computed, onMounted, ref, watch} from "vue";
import EditButton from "@/components/common/editButton.vue";
import {useModelStore} from "@/stores/model";
import type {Enum, Query} from "@/api/query/interface/model";
import {useToast} from "primevue/usetoast";
import Draggable from "vuedraggable";
import AddQuery from "@/components/model/addQuery.vue";
import type {OrderQueryCommand} from "@/api/command/interface/queryCommands";
import {orderQueries} from "@/api/command/model/orderQuery";
import NamespaceLabel from "@/components/common/namespaceLabel.vue";
import DomainModelBadge from "@/components/common/domainModelBadge.vue";
import StatusBadgeSmall from "@/components/common/statusBadgeSmall.vue";

const modelStore = useModelStore();
const toaster = useToast();
const search = ref<string>('');
const list = ref<Array<Query>>([]);

// -- refresh list

const filteredList = computed((): Array<Query> => {
  if (modelStore.project) {
    if (search.value === '') {
      return modelStore.project.queries;
    } else {
      const filterValue = search.value.toLowerCase();
      const filter = event => event.name.toLowerCase().includes(filterValue)
          || event.namespace.toLowerCase().includes(filterValue)
          || event.domainModel.name.toLowerCase().includes(filterValue);
      return modelStore.project.queries.filter(filter);
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
  void modelStore.loadQuery(id);
}

// -- save order of the domain models ----------------------------------------------------------------------------------

const sequenceQueries = computed((): OrderQueryCommand => {
  let sequence = [];
  for (let i = 0; i < list.value.length; i++) {
    sequence.push(list.value[i].id);
  }
  return {sequence: sequence};
});

async function saveSequence() {
  await orderQueries(sequenceQueries.value);
  toaster.add({
    severity: "success",
    summary: "Query order saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadProject();
}

function selectedClass(id: number) {
  return (id === modelStore.querySelectedId)
      ? "border-2 border-indigo-500"
      : "border-2 border-gray-200"
}

// -- toggle add form

function queryAdded() {
  search.value = '';
  document
      .getElementById('query-start')
      ?.scrollIntoView({behavior: "smooth", block: "nearest"});
  toggle('event');
}

const op = ref();
const toggle = (event: any) => {
  op.value.toggle(event);
};

</script>

<style scoped>

#listExample {
  background-color: #F6F6F6;
}

</style>