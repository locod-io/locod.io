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
  <div :id="'content-node-'+nodeRef.uuid"
       :class="selectedClass">

    <div class="flex gap-2 font-bold p-2">
      <div v-if="nodeRef.level !== 0" class="flex-none">
        <span v-for="i in nodeRef.level">
          &nbsp;&nbsp;&nbsp;&nbsp;
        </span>
      </div>
      <div class="flex-none mt-2">
        <div v-if="!nodeRef.isOpen" @click="openNode" class="cursor-pointer">
          <font-awesome-icon :icon="['far', 'circle-right']"/>
        </div>
        <div v-if="nodeRef.isOpen" @click="closeNode" class="cursor-pointer">
          <font-awesome-icon :icon="['far', 'circle-down']"/>
        </div>
      </div>
      <div class="mt-2">{{ nodeRef.number }}.</div>

      <!-- // render and edit the name -->
      <div v-if="!editNameMode" class="flex-grow line-clamp-1 h-6 mt-2" @dblclick="editName">
        {{ nodeRef.name }}
      </div>
      <div v-else class="flex gap-2 flex-grow mt-1" v-on:keyup.enter="saveName" v-on:keyup.esc="closeEditName">
        <div class="flex-grow">
          <InputText
              :id="'node-name-'+nodeRef.uuid"
              class="p-inputtext-sm w-full"
              v-model="changeNameCommand.name"/>
        </div>
        <div class="flex-none">
          <Button class="p-button-sm p-button-success p-button-icon"
                  icon="pi pi-save"
                  @click="saveName"
                  v-if="!isSaving"/>
          <Button v-else
                  class="p-button-sm p-button-success p-button-icon"
                  icon="pi pi-spin pi-spinner"
                  disabled/>
        </div>
        <div class="flex-none">
          <close-button @click="closeEditName"></close-button>
        </div>
        <delete-node :node="nodeRef" class="flex-none mt-1"/>
      </div>

      <!-- // render the status and artefactId -->
      <div class="flex-none p-1">
        <wiki-status-badge :node="nodeRef" v-on:clicked="closeAllEditors"/>
      </div>

    </div>

    <!-- // the description of this node -->
    <div class="flex gap-2">
      <div v-if="nodeRef.level !== 0" class="flex-none">
        <span v-for="i in nodeRef.level">
          &nbsp;&nbsp;&nbsp;&nbsp;
        </span>
      </div>

      <div v-if="nodeRef.isOpen" class="flex-grow">

        <div v-if="!editDescriptionMode"
             @dblclick="editDescription"
             class="py-2 pl-8 pr-4 text-sm">

          <div class="descriptionWrapper">
            <div v-if="nodeRef.status.isFinal"
                 style="margin-top:-10px;"
                 class="line-clamp-2 h-12 pl-4 text-xs flex-none font-normal float-right font-sans text-right">
              by
              <email-label :email="nodeRef.finalBy"/>
              <br>
              <Timeago :datetime="nodeRef.finalAt" long/>
            </div>

            <!-- -- render the content ----------------------------------------------------------------------------- -->
            <div v-if="nodeRef.description.length !== 0">
              <div v-html="filteredDescription" :id="'descriptionContent-'+nodeRef.uuid"/>
              <div v-if="showUploadFigmaImagesButton">
                <Button v-if="!isSaving"
                        class="p-button-sm p-button-outlined"
                        icon="pi pi-upload"
                        label="Upload Figma Images"
                        @click="uploadFigmaImages"/>
                <Button v-else
                        class="p-button-sm p-button-outlined"
                        icon="pi pi-spinner pi-spin"
                        disabled
                        label="Upload Figma Images"/>
              </div>
            </div>
            <!-- -- render the content ----------------------------------------------------------------------------- -->

            <div v-else>
              <edit-button @click="editDescription"/>
            </div>
          </div>

        </div>
        <div v-if="editDescriptionMode"
             v-on:keyup.esc="closeEditDescription"
             class="text-sm border-t-[1px] border-gray-300 dark:border-gray-600">
          <div>
            <simple-editor v-model="changeDescriptionCommand.description"/>
          </div>
          <div class="flex gap-2 p-2 h-12">
            <div>
              <Button class="p-button-sm p-button-success p-button-icon"
                      icon="pi pi-save"
                      @click="saveDescription"
                      v-if="!isSaving"/>
              <Button v-else
                      class="p-button-sm p-button-success p-button-icon"
                      icon="pi pi-spin pi-spinner"
                      disabled/>
            </div>
            <div>
              <close-button @click="closeEditDescription"></close-button>
            </div>

          </div>
        </div>

        <!-- -- render the related images & upload -->
        <content-node-files :node="nodeRef"/>

        <!-- -- render the related issues -->
        <div class="pb-2" v-if="nodeRef.relatedIssues">
          <div v-for="issue in nodeRef.relatedIssues"
               class="mb-1"
               :key="issue.id">
            <div class="flex gap-2 mb-0.5">
              <div class="flex-none ml-10 w-20">
                <span
                    class="w-full rounded-full px-2 text-white font-bold text-xs py-1 bg-gray-300 dark:bg-gray-700 text-xs">
                  {{ issue.identifier }}
                </span>
              </div>
              <div class="flex-grow line-clamp-1 text-sm">
                {{ issue.title }}
              </div>
            </div>
          </div>
        </div>

      </div>
      <div v-else class="descriptionWrapper pl-12 py-2 text-sm flex-grow">
        ...
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import {useWikiStore} from "@/_lodocio/stores/wiki";
import type {WikiNode} from "@/_lodocio/api/interface/wiki";
import {computed, ref, toRef, watch} from "vue";
import {Timeago} from 'vue2-timeago';
import type {ChangeNodeDescriptionCommand} from "@/_lodocio/api/command/wiki/changeNodeDescription";
import {changeNodeDescription} from "@/_lodocio/api/command/wiki/changeNodeDescription";
import EditButton from "@/components/common/editButton.vue";
import SimpleEditor from "@/_common/editor/simpleEditor.vue";
import CloseButton from "@/components/common/closeButton.vue";
import {useAppStore} from "@/stores/app";
import {useToast} from "primevue/usetoast";
import type {ChangeNodeNameCommand} from "@/_lodocio/api/command/wiki/changeNodeName";
import {changeNodeName} from "@/_lodocio/api/command/wiki/changeNodeName";
import WikiStatusBadge from "@/_lodocio/components/wiki/content/wikiStatusBadge.vue";
import EmailLabel from "@/components/common/emailLabel.vue";
import DeleteNode from "@/_lodocio/components/wiki/content/deleteNode.vue";
import ContentNodeFiles from "@/_lodocio/components/wiki/content/contentNodeFiles.vue";
import type {UploadFigmaExportImageCommand} from "@/_lodocio/api/command/wiki/uploadFigmaExportImage";
import {uploadFigmaExportImage} from "@/_lodocio/api/command/wiki/uploadFigmaExportImage";

const props = defineProps<{ node: WikiNode }>();
const nodeRef = toRef(props, 'node');
const wikiStore = useWikiStore();
const appStore = useAppStore();
const toaster = useToast();
const editDescriptionMode = ref<boolean>(false);
const editNameMode = ref<boolean>(false);
const isSaving = ref<boolean>(false);
const showUploadFigmaImagesButton = ref<boolean>(false);
const figmaDocumentKey = ref<string>('');

const selectedClass = computed(() => {
  return (nodeRef.value.id === wikiStore.wikiNodeId)
      ? "border-2 border-indigo-500"
      : "border-b-[1px] border-gray-300 dark:border-gray-600"
});

function openNode() {
  nodeRef.value.isOpen = true;
  wikiStore.renumberTree();
}

function closeNode() {
  nodeRef.value.isOpen = false;
  wikiStore.renumberTree();
}

function closeAllEditors() {
  closeEditName()
  closeEditDescription()
}

// -- change the name

watch(nodeRef, (newValue): void => {
  changeNameCommand.value = {
    id: newValue.id,
    name: newValue.name
  }
  changeDescriptionCommand.value = {
    id: newValue.id,
    description: newValue.description
  }
});

const filteredDescription = computed(() => {
  setTimeout(findFigmaAttachments, 500);
  return nodeRef.value.description.replace(/colwidth="(\d+)"/g, 'style="width:$1px"');
});

watch(filteredDescription, (newValue): void => {
  setTimeout(findFigmaAttachments, 500);
});

function findFigmaAttachments() {
  console.log('-- find linear attachements');
  const contentElement = document.getElementById('descriptionContent-' + nodeRef.value.uuid);
  if (contentElement) {
    showUploadFigmaImagesButton.value = false;
    const iframeElements = contentElement.querySelectorAll('iframe');
    if (iframeElements) {
      iframeElements.forEach(function (iframeElement) {
        if (iframeElement.src.includes('https://www.figma.com/')) {
          showUploadFigmaImagesButton.value = true;
        }
      });
    }
    const linkElements = contentElement.querySelectorAll('a');
    if (linkElements) {
      linkElements.forEach(function (linkElement) {
        if (linkElement.href.includes('https://www.figma.com/')) {
          const match = linkElement.href.match(/\/file\/([a-zA-Z0-9_-]+)\//);
          if (match && match[1]) {
            figmaDocumentKey.value = match[1];
          }
          showUploadFigmaImagesButton.value = true;
          let iframeElement = document.createElement('iframe');
          iframeElement.height = '250';
          iframeElement.width = '100%';
          iframeElement.id = 'figma-iframe-' + nodeRef.value.uuid;
          iframeElement.src = 'https://www.figma.com/embed?embed_host=locodio&url=' + linkElement.href;
          iframeElement.allowFullscreen = true;
          linkElement.parentNode.replaceChild(iframeElement, linkElement);
        }
      });
    }
  }
}

async function uploadFigmaImages() {
  if (figmaDocumentKey.value !== '') {
    console.log('-- upload figma images');
    const command: UploadFigmaExportImageCommand = {
      nodeId: nodeRef.value.id,
      figmaDocumentKey: figmaDocumentKey.value
    }
    isSaving.value = true;
    await uploadFigmaExportImage(command);
    toaster.add({
      severity: "success",
      summary: "Figma files are uploaded",
      detail: "",
      life: appStore.toastLifeTime,
    });
    await wikiStore.reloadWiki();
    isSaving.value = false;
    if (nodeRef.value.id === wikiStore.wikiNodeId) {
      await wikiStore.reloadWikiNode();
    }
  }
}

const changeNameCommand = ref<ChangeNodeNameCommand>({
  id: nodeRef.value.id,
  name: nodeRef.value.name
});

function editName() {
  if (!nodeRef.value.status.isFinal) {
    editNameMode.value = true;
    setTimeout(function () {
      document.getElementById("node-name-" + nodeRef.value.uuid).focus();
    }, 200);
  }
}

function closeEditName() {
  editNameMode.value = false;
}

async function saveName() {
  isSaving.value = true;
  await changeNodeName(changeNameCommand.value);
  toaster.add({
    severity: "success",
    summary: "Name changed",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await wikiStore.reloadWiki();
  isSaving.value = false;
  if (nodeRef.value.id === wikiStore.wikiNodeId) {
    await wikiStore.reloadWikiNode();
  }
  closeEditName();
}

// -- change the description

const changeDescriptionCommand = ref<ChangeNodeDescriptionCommand>({
  id: nodeRef.value.id,
  description: nodeRef.value.description
});

function editDescription() {
  if (!nodeRef.value.status.isFinal) {
    editDescriptionMode.value = true;
  }
}

function closeEditDescription() {
  editDescriptionMode.value = false;
}

async function saveDescription() {
  isSaving.value = true;
  await changeNodeDescription(changeDescriptionCommand.value);
  toaster.add({
    severity: "success",
    summary: "Description changed",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await wikiStore.reloadWiki();
  isSaving.value = false;
  if (nodeRef.value.id === wikiStore.wikiNodeId) {
    await wikiStore.reloadWikiNode();
  }
  closeEditDescription();
}

</script>

<style scoped>

</style>