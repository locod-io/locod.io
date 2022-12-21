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
  <div id="docContentItem">

    <!-- edit button -->
    <div class="float-right pt-2 w-10 ml-4">
      <div v-if="!props.docItem.isEdit">
        <edit-button @click="openEditMode"/>
      </div>
      <div v-else class="mt-10">
        <close-button @click="closeEditMode"/>
      </div>
    </div>

    <!-- module & namespace and repo -->
    <div class="text-xs py-0.5 ml-8" v-if="docItem.typeCode === 'DM'">
      Module: {{ docItem.item.module.name }}
    </div>
    <div class="text-xs py-0.5 ml-8">
      Namespace: {{ docItem.item.namespace }}
    </div>
    <div class="text-xs py-0.5 ml-8" v-if="docItem.typeCode === 'DM'">
      Repository: {{ docItem.item.repository }}
    </div>

    <!-- documentor -->
    <div v-if="!props.docItem.isEdit">
      <div v-if="docItem.item.documentor.image" class="mt-2 mb-2 ml-8">
        <Image class="documentorImage"
               :src="apiUrl+'/model/documentor/'+docItem.item.documentor.id+'/image?t=' + timestamp"
               :alt="docItem.item.documentor.image" preview />
      </div>
      <div v-if="docItem.item.documentor.description === ''">
        <div class="mt-1" v-if="!(docItem.typeCode == 'Q' || docItem.typeCode == 'C')">&nbsp;</div>
      </div>
      <div v-else>
        <div class="mt-2 text-sm ml-8 descriptionWrapper" v-html="docItem.item.documentor.description"></div>
      </div>
    </div>
    <div v-else>
      <div class="mt-2 border-t-[1px] border-gray-300">
        <status-badge class="mt-2"
                      :is-documentation="true"
                      :id="docItem.item.id"
                      :documentor="docItem.item.documentor"
                      :type="docItem.type"/>
      </div>
      <div class="mt-2">
        <div v-if="docItem.item.documentor.status.isFinal">
          <div v-if="docItem.item.documentor.image" class="mt-2 mb-2 ml-8">
            <Image class="documentorImage"
                   :src="apiUrl+'/model/documentor/'+docItem.item.documentor.id+'/image?t=' + timestamp"
                   :alt="docItem.item.documentor.image" preview />
          </div>
          <div v-if="docItem.item.documentor.description === ''">
            <div class="mt-1">&nbsp;</div>
          </div>
          <div v-else>
            <div class="mt-2 ml-8 text-sm descriptionWrapper" v-html="docItem.item.documentor.description"></div>
          </div>
        </div>
        <div v-else>
          <!-- documentor image uploader -->
          <doc-item-image-uploader :documentor="docItem.item.documentor"/>
          <!-- editor -->
          <simple-editor v-model="description"/>
        </div>
      </div>
      <div class="mt-2 mb-2" v-if="!docItem.item.documentor.status.isFinal">
        <Button v-if="!isSaving"
                label="SAVE"
                icon="pi pi-save"
                @click="saveDocumentation"
                class="p-button-sm p-button-success w-64"/>
        <Button v-else
                label="SAVE"
                icon="pi pi-spinner pi-spin"
                :disabled="true"
                class="p-button-sm p-button-success w-64"/>
      </div>
    </div>

    <!-- -- render command mapping ------------------------------------------------------- -->
    <div class="ml-8" v-if="docItem.typeCode === 'C'">
      <table class="text-xs">
        <thead>
        <tr>
          <th>Name</th>
          <th class="pl-3">Type</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="mapping in docItem.item.mapping" class="border-t-[1px] border-gray-300">
          <td class="py-0.5 font-semibold">{{mapping.name}}</td>
          <td class="pl-3">{{mapping.type}}</td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- -- render query mapping --------------------------------------------------------- -->
    <div class="ml-8" v-if="docItem.typeCode === 'Q'">
      <table class="text-xs">
        <thead>
        <tr>
          <th>Name</th>
          <th class="pl-3">Type</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="mapping in docItem.item.mapping" class="border-t-[1px] border-gray-300">
          <td class="py-0.5 font-semibold">{{mapping.name}}</td>
          <td class="pl-3">{{mapping.type}}</td>
        </tr>
        </tbody>
      </table>
    </div>

  </div>
</template>

<script setup lang="ts">
import DocItemImageUploader from "@/components/model/documentation/docItemImageUploader.vue";

const apiUrl = import.meta.env.VITE_API_URL;
import type {DocumentationItem} from "@/api/query/interface/model";
import {computed, ref} from "vue";
import EditButton from "@/components/common/editButton.vue";
import StatusBadge from "@/components/common/statusBadge.vue";
import CloseButton from "@/components/common/closeButton.vue";
import SimpleEditor from "@/components/editor/simpleEditor.vue";
import {useSchemaStore} from "@/stores/schema";
import {useModelStore} from "@/stores/model";
import type {ChangeDocumentorCommand} from "@/api/command/interface/modelConfiguration";
import {changeDocumentor} from "@/api/command/model/changeDocumentor";
import {useToast} from "primevue/usetoast";

// -- props
const props = defineProps<{
  docItem: DocumentationItem
}>();

const emit = defineEmits(["edit"]);

const schemaStore = useSchemaStore();
const modelStore = useModelStore();
const toaster = useToast();
const timestamp = new Date().getTime();

const isSaving = ref<boolean>(false);
const description = ref<string>(props.docItem.item.documentor.description);

function openEditMode() {
  props.docItem.isEdit = true;
  description.value = props.docItem.item.documentor.description;
  schemaStore.closeEditors(props.docItem.labelLevel);
}

function closeEditMode() {
  props.docItem.isEdit = false;
  schemaStore.closeEditors(props.docItem.labelLevel);
  schemaStore.loadDocumentation(modelStore.projectId);
}

const command = computed<ChangeDocumentorCommand>(() => {
  let command: ChangeDocumentorCommand = {};
  command.id = modelStore.documentor?.id ?? 0;
  command.description = description.value;
  command.statusId = modelStore.documentor?.status.id ?? 0;
  return command;
});

async function saveDocumentation() {
  isSaving.value = true;
  let result = await changeDocumentor(command.value);
  props.docItem.isEdit = false;
  schemaStore.closeEditors(props.docItem.labelLevel);
  await schemaStore.loadDocumentation(modelStore.projectId);
  isSaving.value = false;
  toaster.add({
    severity: "success",
    summary: "Documentation saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
}

</script>

<style>

.documentorImage img {
  max-height:150px;
  max-width:750px;
  height:auto;
  width:auto;
}

</style>