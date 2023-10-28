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
              <div class="flex">
                <div>
                  <Button
                      @click="reload"
                      v-if="!modelStore.templateReloading"
                      icon="pi pi-refresh"
                      class="p-button-sm"/>
                  <Button
                      v-else
                      disabled
                      icon="pi pi-spin pi-spinner"
                      class="p-button-sm"/>
                </div>
              </div>
            </div>

            <div class="flex-none">
              <div class="ml-4 mt-1">
                <a :href="'/api/model/template/'+modelStore.templateSelectedId+'/download'"
                   class="text-gray-300 hover:text-gray-500 dark:text-gray-500"
                   title="download template">
                  <font-awesome-icon icon="fa-solid fa-cloud-arrow-down"/>
                </a>
              </div>
            </div>

            <div class="flex-grow">&nbsp;</div>

            <!-- export -->
            <div class="flex-none">

              <div v-if="modelStore.template.masterTemplate">
                <div class="text-gray-400 text-sm flex gap-4">

                  <!-- link to master template -->
                  <div class="flex-grow line-clamp-1 mt-1 h-6">
                    <font-awesome-icon icon="fa-solid fa-link"/>
                    {{ modelStore.template.masterTemplate.name }}
                  </div>

                  <!-- master template is newer -->
                  <div
                      v-if="modelStore.template.masterTemplate.lastUpdatedAtNumber > modelStore.template.linkedAtNumber"
                      class="flex-none">
                    <Button @click="changeTemplateContentFn"
                            label="Update template (newer master)"
                            icon="pi pi-upload"
                            class="p-button-sm p-button-secondary p-button-outlined"/>

                  </div>

                  <!-- this template is newer -->
                  <div v-else class="flex-none">
                    <Button @click="changeMasterTemplateContentFn"
                            label="Update master template" icon="pi pi-upload"
                            class="p-button-sm p-button-secondary p-button-outlined"/>
                  </div>

                </div>
              </div>
              <div v-else>
                <Button @click="exportTemplate"
                        label="Export as master template" icon="pi pi-upload"
                        class="p-button-sm p-button-secondary p-button-outlined"/>
              </div>
            </div>
          </div>

          <!-- form -------------------------------------------------------------------------------------------------- -->

          <div v-if="modelStore.template">

            <div class="p-4 gap-0 border-b-[1px] border-gray-300 dark:border-gray-600 h-36">

              <div class="flex flex-row">
                <div class="basis-3/4">
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
                <div class="basis-1/4 ml-2">
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
              </div>

              <!-- type -->
              <div class="flex mt-5">
                <div class="mr-2">
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

            <DetailWrapper :estate-height="247">
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
          </div>

          <!-- render this template -->
          <div
              class="flex flex-row bg-indigo-200 p-2 gap-0 border-t-[1px] border-gray-300 dark:border-gray-600 h-12 dark:bg-indigo-900"
              v-on:keyup.g="generateCode">
            <div class="basis-1/4 mt-1.5 text-right text-sm">Try this template with:</div>
            <div class="basis-1/2 ml-2">
              <Dropdown optionLabel="name"
                        v-model="selectedSubject"
                        :options="models"
                        placeholder="Select a model"
                        class="w-full p-dropdown-sm"/>
            </div>
            <div class="basis-1/4 ml-2">
              <Button label="GENERATE"
                      icon="pi pi-code"
                      class="w-full p-button-sm"
                      :disabled="!(selectedSubject)"
                      @click="generateCode"/>
            </div>
          </div>

        </form>

      </div>
    </div>
  </div>

  <!-- dialog code generation // full screen overlay  -->
  <Sidebar v-model:visible="showCodeGenerator"
           v-on:keyup.esc="showCodeGenerator=false"
           position="full"
           :dismissable="true">
    <dialog-code-generation
        v-on:close-code="showCodeGenerator = false"
        :template="modelStore.template"
        :subject-id="selectedSubject.id"/>
  </Sidebar>

</template>

<script setup lang="ts">
import {computed, onActivated, onBeforeMount, onDeactivated, onMounted, onUnmounted, ref} from "vue";
import DetailWrapper from "@/components/wrapper/detailWrapper.vue";
import {useConfirm} from "primevue/useconfirm";
import {VAceEditor} from 'vue3-ace-editor';
import 'ace-builds/src-noconflict/mode-twig';
import 'ace-builds/src-noconflict/theme-chrome';
import DialogCodeGeneration from "@/components/model/dialogCodeGeneration.vue";
import {useModelStore} from "@/stores/model";
import type {
  ChangeMasterTemplateContentCommand,
  ChangeTemplateCommand,
  ChangeTemplateContentCommand,
  ExportTemplateToMasterTemplateCommand
} from "@/api/command/interface/templateCommands";
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {changeTemplate} from "@/api/command/model/changeTemplate";
import {useToast} from "primevue/usetoast";
import type {Template} from "@/api/query/interface/model";
import {exportTemplateToMasterTemplate} from "@/api/command/model/exportTemplateToMasterTemplate";
import {deleteTemplate} from "@/api/command/model/deleteTemplate";
import {changeMasterTemplateContent} from "@/api/command/model/changeMasterTemplateContent";
import {changeTemplateContent} from "@/api/command/model/changeTemplateContent";
import '@/ace-config';
import {useAppStore} from "@/stores/app";

// -- props, store and emits

const modelStore = useModelStore();
const appStore = useAppStore();
const toaster = useToast();
const confirm = useConfirm();
const isSaving = ref<boolean>(false);
const command = ref<ChangeTemplateCommand>({
  id: modelStore.templateSelectedId,
  type: modelStore.template?.type ?? "domain_model",
  name: modelStore.template?.name ?? "",
  language: modelStore.template?.language ?? "",
  template: modelStore.template?.template ?? ""
});

const getTheme = computed(() => {
  if (appStore.theme === 'light') {
    return 'chrome';
  } else {
    return 'nord_dark';
  }
});

// -- mounted

onMounted((): void => {
  v$.value.$touch();
  document.addEventListener("keydown", shortcutHandler);
});

onUnmounted(() => {
  document.removeEventListener("keydown", shortcutHandler);
});

onActivated((): void => {
  v$.value.$touch();
  document.addEventListener("keydown", shortcutHandler);
});

onDeactivated(() => {
  document.removeEventListener("keydown", shortcutHandler);
});

function shortcutHandler(event) {
  if (!((event.keyCode === 83 && event.ctrlKey) || (event.keyCode === 68 && event.ctrlKey))) {
    return;
  }
  event.preventDefault();
  if ((event.keyCode === 83 && event.ctrlKey)) {
    change();
  }
  if ((event.keyCode === 68 && event.ctrlKey)) {
    if (selectedSubject.value) generateCode();
  }
}

const selectedSubject = ref<any>();
const models = computed(() => {
  if (modelStore.project && modelStore.template) {
    switch (modelStore.template.type) {
      case 'domain_model':
        return modelStore.project.domainModels;
      case 'enum':
        return modelStore.project.enums;
      case 'query':
        return modelStore.project.queries;
      case 'command':
        return modelStore.project.commands;
    }
  } else {
    return [];
  }
});

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
    await changeTemplate(command.value);
    toaster.add({
      severity: "success",
      summary: "Template saved",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadProject();
    isSaving.value = false;
  }
}

async function reload() {
  await modelStore.reLoadTemplate();
  command.value.type = modelStore.template?.type ?? "domain_model";
  command.value.name = modelStore.template?.name ?? "";
  command.value.language = modelStore.template?.language ?? "";
  command.value.template = modelStore.template?.template ?? "";
}

// -- export

async function exportTemplate() {
  isSaving.value = true;
  const commandExport: ExportTemplateToMasterTemplateCommand = {
    templateId: modelStore.templateSelectedId,
    userId: appStore.user?.id ?? 0
  }
  await exportTemplateToMasterTemplate(commandExport);
  toaster.add({
    severity: "success",
    summary: "Template exported to master templates.",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.loadMasterTemplates();
  await reload();
  isSaving.value = false;
}

async function changeTemplateContentFn() {
  isSaving.value = true;
  const commandChange: ChangeTemplateContentCommand = {
    templateId: modelStore.templateSelectedId,
    masterTemplateId: modelStore.template?.masterTemplate?.id ?? 0
  }
  await changeTemplateContent(commandChange);
  toaster.add({
    severity: "success",
    summary: "Template updated.",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await reload();
  isSaving.value = false;
}

async function changeMasterTemplateContentFn() {
  isSaving.value = true;
  const commandChange: ChangeMasterTemplateContentCommand = {
    templateId: modelStore.templateSelectedId,
    masterTemplateId: modelStore.template?.masterTemplate?.id ?? 0
  }
  await changeMasterTemplateContent(commandChange);
  toaster.add({
    severity: "success",
    summary: "Master template updated.",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.loadMasterTemplates();
  await reload();
  isSaving.value = false;
}

// -- editor height pusher

const wrapperHeight = ref("400px");
const estateHeight = 248;

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

onActivated((): void => {
  v$.value.$touch();
  document.addEventListener("keydown", editorResizeHandler);
});

onDeactivated(() => {
  document.removeEventListener("keydown", editorResizeHandler);
});

// -- generated window

const showCodeGenerator = ref<boolean>(false);

function generateCode() {
  showCodeGenerator.value = true;
}

</script>