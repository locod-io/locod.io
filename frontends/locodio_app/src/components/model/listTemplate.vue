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

    <!-- template list ------------------------------------------------------------ -->
    <list-wrapper :estate-height="249">

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
                <div>
                  <Badge :value="element.language" class="p-badge-secondary"/>
                </div>
              </div>
            </div>
          </div>
        </template>
      </Draggable>

    </list-wrapper>

    <div class="p-2">
      <div class="flex flex-row">
        <!-- import -->
        <div class="basis-3/4">
          <div @click="showMasterTemplates = true"
               class="bg-blue-300 w-[13rem] rounded-lg text-white flex cursor-pointer pb-0.5 mt-1.5">
            <div class="mr-1 ml-2">
              <font-awesome-icon icon="fa-solid fa-file-import"/>
            </div>
            <div class="text-sm pt-0.5">Import master template(s)</div>
          </div>
        </div>
        <!-- add -->
        <div class="text-right basis-1/4">
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
  </div>
  <OverlayPanel ref="op" :showCloseIcon="true" :dismissable="true">
    <add-template v-on:added="templateAdded"></add-template>
  </OverlayPanel>

  <Sidebar position="left" v-model:visible="showMasterTemplates" class="w-[30rem]">
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
      : "border-2 border-gray-200"
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

#listExample {
  background-color: #F6F6F6;
}

</style>