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
  <div id="listTemplate">

    <!-- search & refresh --------------------------------------------------------- -->
    <div class="flex border-b-[1px] border-gray-300 dark:border-gray-600 h-12">
      <div class="flex-none p-2">
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
      <div class="flex-grow pr-2.5 pt-2">
        <div class="p-input-icon-right w-full">
          <InputText
              type="text"
              class="w-full p-inputtext-sm"
              v-model="search"/>
          <i class="pi pi-search"/>
        </div>
      </div>
    </div>

    <!-- template list ------------------------------------------------------------ -->
    <list-wrapper :estate-height="125">

      <Draggable
          v-model="list"
          tag="div"
          item-key="id"
          handle=".handle"
          @end="saveTemplateOrder"
          ghost-class="ghost">
        <template #item="{ element }">

          <div class="w-full bg-white hover:bg-indigo-100 h-12 dark:bg-gray-900 dark:hover:bg-indigo-900"
               :class="selectedClass(element.id)"
               @dblclick="selectDetail(element.id)">

            <div class="flex gap-2 h-12 p-3">

              <div class="flex-none" v-if="search.length === 0">
                <div class="text-gray-300 hover:text-green-600 cursor-move mr-2 dark:text-gray-600">
                  <i class="pi pi-bars handle" style="font-size:.85rem;"></i>
                </div>
              </div>

              <div class="flex-none">
                <i class="pi pi-database" v-if="(element.type === 'domain_model')"></i>
                <i class="pi pi-align-justify" v-if="(element.type === 'enum')"></i>
                <i class="pi pi-download" v-if="(element.type === 'command')"></i>
                <i class="pi pi-upload" v-if="(element.type === 'query')"></i>
              </div>

              <div class="flex-grow line-clamp-1 text-sm h-5 font-semibold">
                {{ element.name }}
              </div>

              <div class="flex-none w-8 mr-2" :style="correctionStyle(element.id)">
                <span class="bg-gray-400 rounded-full px-2 text-white font-bold text-xs py-1 dark:bg-gray-600">
                  {{ element.language }}
                </span>
              </div>

              <div class="flex-none" v-if="element.id != modelStore.templateSelectedId">
                <edit-button @click="selectDetail(element.id)"></edit-button>
              </div>
            </div>

          </div>
        </template>
      </Draggable>

    </list-wrapper>

    <div class="border-t-[1px] border-gray-300 dark:border-gray-600 h-12 p-2 flex gap-2">
      <!-- add -->
      <div class="flex-none">
        <Button
            icon="pi pi-plus"
            class="p-button-sm p-button-icon"
            @click="toggle"
            aria-haspopup="true"
            aria-controls="overlay_panel"
        />
      </div>
      <!-- import -->
      <div class="flex-none">
        <Button
            icon="pi pi-download"
            class="p-button-sm p-button-secondary p-button-outlined"
            label="Import master template(s)"
            @click="showMasterTemplates = true"/>
      </div>
      <div class="flex-grow">&nbsp;</div>
      <div class="flex-none">
        <delete-template/>
      </div>

    </div>
  </div>

  <OverlayPanel ref="op" :showCloseIcon="true" :dismissable="true">
    <add-template v-on:added="templateAdded"></add-template>
  </OverlayPanel>

  <Sidebar position="left" v-model:visible="showMasterTemplates" class="w-[50rem]">
    <select-master-templates @imported="showMasterTemplates=false"/>
  </Sidebar>

</template>

<script setup lang="ts">
import ListWrapper from "@/components/wrapper/listWrapper.vue";
import {computed, onMounted, ref, watch} from "vue";
import AddTemplate from "@/components/model/addTemplate.vue";
import EditButton from "@/components/common/editButton.vue";
import {useModelStore} from "@/stores/model";
import type {Template} from "@/api/query/interface/model";
import {useToast} from "primevue/usetoast";
import Draggable from "vuedraggable";
import type {OrderTemplateCommand} from "@/api/command/interface/templateCommands";
import {orderTemplates} from "@/api/command/model/orderTemplate";
import {useAppStore} from "@/stores/app";
import SelectMasterTemplates from "@/components/model/selectMasterTemplates.vue";
import DeleteTemplate from "@/components/model/deleteTemplate.vue";

const modelStore = useModelStore();
const toaster = useToast();
const search = ref<string>('');
const showMasterTemplates = ref<boolean>(false);

const list = ref<Array<Template>>([]);

// -- refresh list

const filteredTemplates = computed((): Array<Template> => {
  if (modelStore.project) {
    if (search.value === '') {
      return modelStore.project.templates;
    } else {
      const filterValue = search.value.toLowerCase();
      const filter = event => event.name.toLowerCase().includes(filterValue)
          || event.language.toLowerCase().includes(filterValue)
          || event.type.toLowerCase().includes(filterValue);
      return modelStore.project.templates.filter(filter);
    }
  } else {
    return [];
  }
});

watch(filteredTemplates, (): void => {
  list.value = JSON.parse(JSON.stringify(filteredTemplates.value))
});

onMounted((): void => {
  list.value = JSON.parse(JSON.stringify(filteredTemplates.value))
});

function refreshList() {
  void modelStore.reLoadProject();
}

function selectDetail(id: number) {
  void modelStore.loadTemplate(id);
}

function selectedClass(id: number) {
  return (id === modelStore.templateSelectedId)
      ? "border-2 border-indigo-500"
      : "border-b-[1px] border-gray-300 dark:border-gray-600"
}

function correctionStyle(id: number) {
  return (id === modelStore.templateSelectedId)
      ? "margin-top:-0.2rem;"
      : ""
}

const sequenceTemplates = computed((): OrderTemplateCommand => {
  let sequence = [];
  for (let i = 0; i < list.value.length; i++) {
    sequence.push(list.value[i].id);
  }
  return {sequence: sequence};
});

async function saveTemplateOrder() {
  await orderTemplates(sequenceTemplates.value);
  toaster.add({
    severity: "success",
    summary: "Template order saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadProject();
}

// -- toggle add form

function templateAdded() {
  search.value = '';
  document
      .getElementById('template-start')
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