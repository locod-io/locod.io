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
  <div class="detailWrapper">
    <div class="detail">
      <div>
        <form v-on:submit.prevent="change">

          <!-- toolbar --------------------------------------------------------------------------------------------- -->
          <div class="flex p-2 gap-2 border-b-[1px] border-gray-300 dark:border-gray-600 h-12">
            <div class="flex-none">
              <Button
                  v-if="!isSaving"
                  type="submit"
                  icon="pi pi-save"
                  label="SAVE"
                  class="p-button-success p-button-sm w-32"/>
              <Button
                  v-else
                  disabled
                  icon="pi pi-spin pi-spinner"
                  label="SAVE"
                  class="p-button-success p-button-sm w-32"/>
            </div>
            <div class="flex-none">
              <Button
                  @click="reload"
                  v-if="!modelStore.masterTemplateReloading"
                  icon="pi pi-refresh"
                  class="p-button-sm"/>
              <Button
                  v-else
                  disabled
                  icon="pi pi-spin pi-spinner"
                  class="p-button-sm"/>
            </div>
            <div class="flex-none">
              <div class="ml-4 mt-2">
                <a :href="'/api/model/master-template/'+modelStore.masterTemplateSelectedId+'/download'"
                   class="text-gray-300 hover:text-gray-500 dark:text-gray-500"
                   title="download master template">
                  <font-awesome-icon icon="fa-solid fa-cloud-arrow-down"/>
                </a>
              </div>
            </div>
            <div class="flex-grow">&nbsp;</div>
            <div class="flex-none">
              <Button @click="showDescription = true"
                      class="p-button-sm p-button-secondary p-button-outlined p-button-icon"
                      icon="pi pi-chevron-right"
                      label="Description & tags"/>
            </div>
          </div>

          <!-- form -------------------------------------------------------------------------------------------------- -->
          <div class="p-2 gap-0 border-b-[1px] border-gray-300 dark:border-gray-600 h-36">

            <div class="p-2" v-if="modelStore.masterTemplate">
              <div class="p-inputtext-sm">

                <div class="flex flex-row">
                  <div class="basis-4/6">
                    <!-- name -->
                    <div><label class="text-sm">Name *</label></div>
                    <div>
                      <span class="p-input-icon-right w-full">
                        <InputText
                            v-model="command.name"
                            class="w-full p-inputtext-sm"
                            placeholder="enter a name"/>
                        <i v-if="!v$.name.$invalid" class="pi pi-check text-green-600"/>
                        <i v-if="v$.name.$invalid" class="pi pi-times text-red-600"/>
                        </span>
                    </div>
                  </div>
                  <div class="basis-1/6 ml-2">
                    <!-- language -->
                    <div><label class="text-sm">Language *</label></div>
                    <div>
                      <span class="p-input-icon-right w-full">
                      <InputText
                          v-model="command.language"
                          class="w-full p-inputtext-sm"
                          placeholder="enter a language"/>
                          <i v-if="!v$.language.$invalid" class="pi pi-check text-green-600"/>
                          <i v-if="v$.language.$invalid" class="pi pi-times text-red-600"/>
                        </span>
                    </div>
                  </div>

                  <!-- share this template -->
                  <div class="basis-1/6 ml-4 text-center">
                    <div><label class="text-sm">Share with others?</label></div>
                    <div class="mt-1">
                      <div class="ml-6 flex">
                        <div class="mr-2">
                          <font-awesome-icon icon="fa-solid fa-people-group"/>
                        </div>
                        <InputSwitch v-model="command.isPublic"></InputSwitch>
                      </div>
                    </div>
                  </div>

                </div>

                <!-- type -->
                <div class="flex mt-5">
                  <div class="mr-2 ml-1">
                    <RadioButton name="template-type" value="domain_model" input-id="domain-model"
                                 v-model="command.type"/>
                  </div>
                  <div class="mr-2 mt-1 text-xs">
                    <label for="domain-model">
                      <i class="pi pi-database"></i> Model
                    </label>
                  </div>
                  <div class="mr-2">
                    <RadioButton name="template-type" value="enum" input-id="enum"
                                 v-model="command.type"/>
                  </div>
                  <div class="mr-2 mt-1 text-xs">
                    <label for="enum">
                      <i class="pi pi-align-justify"></i> Enum
                    </label>
                  </div>
                  <div class="mr-2">
                    <RadioButton name="template-type" value="query" input-id="query"
                                 v-model="command.type"/>
                  </div>
                  <div class="mr-2 mt-1 text-xs">
                    <label for="query">
                      <i class="pi pi-upload"></i> Query
                    </label>
                  </div>
                  <div class="mr-2">
                    <RadioButton name="template-type" value="command" input-id="command"
                                 v-model="command.type"/>
                  </div>
                  <div class="mr-2 mt-1 text-xs">
                    <label for="command">
                      <i class="pi pi-download"></i> Command
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <DetailWrapper :estate-height="205">
            <!-- editor -->
            <div>
              <v-ace-editor
                  v-model:value="command.template"
                  ref="templateEditor"
                  lang="twig"
                  class="vue-ace-editor"
                  :theme="getTheme"
                  :style="'height:' + wrapperHeight+';font-family: \'Fira Code\', monospace;'"
              />
            </div>
          </DetailWrapper>
        </form>

      </div>
    </div>
  </div>

  <Sidebar v-model:visible="showDescription" position="right">
    <div class="w-full">
      <div class="mt-2 mb-2 text-sm">Tags</div>
      <div>
        <Chips v-model="command.tags" separator=","/>
      </div>
      <div class="mt-2 mb-2 text-sm">Description</div>
      <simple-editor v-model="command.description"/>
    </div>
  </Sidebar>

</template>

<script setup lang="ts">
import {computed, onActivated, onBeforeMount, onDeactivated, onMounted, onUnmounted, ref} from "vue";
import DetailWrapper from "@/components/wrapper/detailWrapper.vue";
import {useConfirm} from "primevue/useconfirm";
import {VAceEditor} from 'vue3-ace-editor';
import 'ace-builds/src-noconflict/mode-twig';
import 'ace-builds/src-noconflict/theme-chrome';
import {useModelStore} from "@/stores/model";
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {useToast} from "primevue/usetoast";
import type {Template} from "@/api/query/interface/model";
import type {ChangeMasterTemplateCommand} from "@/api/command/interface/masterTemplateCommands";
import {changeMasterTemplate} from "@/api/command/model/changeMasterTemplate";
import SimpleEditor from "@/_common/editor/simpleEditor.vue";
import '@/ace-config';
import {useAppStore} from "@/stores/app";

// -- props, store and emits

const modelStore = useModelStore();
const toaster = useToast();
const confirm = useConfirm();
const isSaving = ref<boolean>(false);
const showDescription = ref<boolean>(false);
const appStore = useAppStore();

const command = ref<ChangeMasterTemplateCommand>({
  id: modelStore.masterTemplateSelectedId,
  type: modelStore.masterTemplate?.type ?? "domain_model",
  name: modelStore.masterTemplate?.name ?? "",
  language: modelStore.masterTemplate?.language ?? "",
  template: modelStore.masterTemplate?.template ?? "",
  isPublic: modelStore.masterTemplate?.isPublic ?? false,
  description: modelStore.masterTemplate?.description ?? "",
  tags: modelStore.masterTemplate?.tags ?? []
});

const getTheme = computed(() => {
  if (appStore.theme === 'light') {
    return 'chrome';
  } else {
    return 'nord_dark';
  }
});

// -- mounting and activation

onMounted((): void => {
  v$.value.$touch();
  document.addEventListener("keydown", shortcutMasterTemplateHandler);
});

onUnmounted(() => {
  document.removeEventListener("keydown", shortcutMasterTemplateHandler);
});

onActivated((): void => {
  v$.value.$touch();
  document.addEventListener("keydown", shortcutMasterTemplateHandler);
});

onDeactivated(() => {
  document.removeEventListener("keydown", shortcutMasterTemplateHandler);
});

function shortcutMasterTemplateHandler(event) {
  if (!((event.keyCode === 83 && event.ctrlKey))) {
    return;
  }
  event.preventDefault();
  if ((event.keyCode === 83 && event.ctrlKey)) {
    change();
  }
}

// -- validation

const rules = {
  type: {required},
  name: {required},
  language: {required},
  template: {required},
};
const v$ = useVuelidate(rules, command);

// -- change

async function change() {
  if (!v$.value.$invalid) {
    isSaving.value = true;
    await changeMasterTemplate(command.value);
    toaster.add({
      severity: "success",
      summary: "Master template saved.",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.loadMasterTemplates();
    isSaving.value = false;
  }
}

async function reload() {
  await modelStore.reLoadMasterTemplate();
  command.value.type = modelStore.masterTemplate?.type ?? "domain_model";
  command.value.name = modelStore.masterTemplate?.name ?? "";
  command.value.language = modelStore.masterTemplate?.language ?? "";
  command.value.template = modelStore.masterTemplate?.template ?? "";
  command.value.isPublic = modelStore.masterTemplate?.isPublic ?? false;
  command.value.description = modelStore.masterTemplate?.description ?? "";
}

// -- editor height pusher

const wrapperHeight = ref("400px");
const estateHeight = 205;

function editorResizeHandler() {
  wrapperHeight.value = `${window.innerHeight - estateHeight}px`;
}

onBeforeMount(() => {
  wrapperHeight.value = `${window.innerHeight - estateHeight}px`;
  window.addEventListener("resize", editorResizeHandler);
});

onUnmounted(() => {
  window.removeEventListener("resize", editorResizeHandler);
});

</script>