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

    <!-- search & refresh --------------------------------------------------------- -->
    <div class="flex border-b-[1px] border-gray-300 dark:border-gray-600 h-12">
      <div class="flex-none p-2">
        <Button
            v-if="!modelStore.isMasterTemplatesLoading"
            icon="pi pi-refresh"
            class="p-button-sm p-button-icon"
            @click="refreshList"/>
        <Button
            v-else
            class="p-button-sm p-button-icon"
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

      <div id="template-start"></div>
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

              <div class="flex-none w-18" :style="correctionStyle(element.id)">
                <span class="bg-gray-400 rounded-full px-2 text-white font-bold text-xs py-1 dark:bg-gray-600">
                  {{ element.language }}
                </span>
                <span v-if="element.isPublic" class="ml-2 text-gray-400" title="shared template">
                    <font-awesome-icon icon="fa-solid fa-people-group"/>
                </span>
              </div>

              <div class="flex-none" v-if="element.id != modelStore.masterTemplateSelectedId">
                <edit-button @click="selectDetail(element.id)"></edit-button>
              </div>

            </div>
          </div>
        </template>
      </Draggable>

    </list-wrapper>

    <!-- bottom list tools ----------------------------------------------------------------------------------------- -->
    <div class="flex p-2 gap-2 border-t-[1px] border-gray-300 dark:border-gray-600 h-12">
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
        <delete-master-template/>
      </div>
    </div>
  </div>

  <OverlayPanel ref="op" :showCloseIcon="true" :dismissable="true">
    <add-master-template v-on:added="templateAdded"/>
  </OverlayPanel>

</template>

<script setup lang="ts">
import ListWrapper from "@/components/wrapper/listWrapper.vue";
import {computed, onMounted, ref, watch} from "vue";
import EditButton from "@/components/common/editButton.vue";
import {useModelStore} from "@/stores/model";
import type {Template} from "@/api/query/interface/model";
import {useToast} from "primevue/usetoast";
import Draggable from "vuedraggable";
import type {OrderMasterTemplateCommand} from "@/api/command/interface/masterTemplateCommands";
import {orderMasterTemplates} from "@/api/command/model/orderMasterTemplate";
import AddMasterTemplate from "@/components/model/addMasterTemplate.vue";
import type {UserMasterTemplate} from "@/api/query/interface/user";
import DeleteMasterTemplate from "@/components/model/deleteMasterTemplate.vue";

const modelStore = useModelStore();
const toaster = useToast();
const search = ref<string>('');
const list = ref<Array<UserMasterTemplate>>([]);

// -- refresh list

const filteredTemplates = computed((): Array<UserMasterTemplate> => {
  if (modelStore.masterTemplates) {
    if (search.value === '') {
      return modelStore.masterTemplates;
    } else {
      const filterValue = search.value.toLowerCase();
      let _result = [];
      for (const template of modelStore.masterTemplates) {
        if (template.name.toLowerCase().includes(filterValue)
            || template.language.toLowerCase().includes(filterValue)
            || template.description.toLowerCase().includes(filterValue)
            || template.tags.includes(filterValue)
            || template.type.toLowerCase().includes(filterValue)
        ) {
          _result.push(template)
        }
      }
      return _result;
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
  void modelStore.loadMasterTemplates();
}

function selectDetail(id: number) {
  void modelStore.loadMasterTemplate(id);
}

function selectedClass(id: number) {
  return (id === modelStore.masterTemplateSelectedId)
      ? "border-2 border-indigo-500"
      : "border-b-[1px] border-gray-300 dark:border-gray-600"
}

function correctionStyle(id: number) {
  return (id === modelStore.masterTemplateSelectedId)
      ? "margin-top:-0.2rem;"
      : ""
}

const sequenceTemplates = computed((): OrderMasterTemplateCommand => {
  let sequence = [];
  for (let i = 0; i < list.value.length; i++) {
    sequence.push(list.value[i].id);
  }
  return {sequence: sequence};
});

async function saveTemplateOrder() {
  await orderMasterTemplates(sequenceTemplates.value);
  toaster.add({
    severity: "success",
    summary: "Template order saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.loadMasterTemplates();
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