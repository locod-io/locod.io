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
    <div class="flex p-2">
      <div>
        <Button
            v-if="!modelStore.isMasterTemplatesLoading"
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

    <!-- template list ------------------------------------------------------------ -->
    <list-wrapper :estate-height="220">

      <div id="template-start"></div>
      <Draggable
          v-model="list"
          tag="div"
          item-key="id"
          handle=".handle"
          @end="saveTemplateOrder"
          ghost-class="ghost">
        <template #item="{ element }">

          <div class="m-2 p-2 border-2 rounded-xl bg-white hover:bg-indigo-100"
               :class="selectedClass(element.id)"
               @dblclick="selectDetail(element.id)">
            <div class="flex flex-row-reverse">
              <edit-button @click="selectDetail(element.id)"></edit-button>
              <div>
                <div class="mt-1.5 text-gray-300 hover:text-green-600 cursor-move mr-2"
                     v-if="search.length === 0">
                  <i class="pi pi-bars handle"></i>
                </div>
              </div>
              <div class="w-full">
                <div>
                  <i class="pi pi-database" v-if="(element.type === 'domain_model')"></i>
                  <i class="pi pi-align-justify" v-if="(element.type === 'enum')"></i>
                  <i class="pi pi-download" v-if="(element.type === 'command')"></i>
                  <i class="pi pi-upload" v-if="(element.type === 'query')"></i>
                  {{ element.name }}
                </div>

                <div class="text-sm mt-1 mb-1"
                     v-if="element.description.trim().length != 0"
                     v-html="element.description.substring(0,150).trim()+'...'"></div>
                <div v-else>&nbsp;</div>

                <div class="mt-1 border-t-[1px] pt-1 border-gray-400">
                  <Badge :value="element.language" class="p-badge-secondary"/>
                  <span v-if="element.isPublic" class="ml-2 text-gray-400" title="shared template">
                    <font-awesome-icon icon="fa-solid fa-people-group" />
                  </span>
                  &nbsp;
                  <span v-for="tag in element.tags"
                        class="text-xs rounded-full bg-blue-400 py-0.5 px-1.5 ml-1 text-white">
                    {{tag}}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </template>
      </Draggable>

    </list-wrapper>

    <!-- add button -------------------------------------------------------------- -->
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
        if(template.name.toLowerCase().includes(filterValue)
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
      : "border-2 border-gray-200"
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

<style scoped>

#listExample {
  background-color: #F6F6F6;
}

</style>