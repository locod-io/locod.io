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
  <div id="docItemImageUploader" class="mb-2">

    <!-- render the image smaller -->
    <div v-show="documentor.image" class="mt-2">
      <Image class="documentorImageSmall"
             id="documentorImageRef"
             :src="apiUrl+'/model/documentor/'+documentor.id+'/image?t=' + timestamp"
             :alt="documentor.image" preview/>
    </div>
    <div v-show="!showUploadZone" class="flex">
      <div>
        <Button @click="openUploader"
                icon="pi pi-upload"
                label="upload image"
                class="p-button-sm p-button-outlined"/>
      </div>
      <div class="ml-2" v-if="documentor.image">
        <Button @click="removeImage"
                icon="pi pi-trash"
                label="remove image"
                class="p-button-sm p-button-outlined"/>
      </div>
    </div>
    <div v-show="showUploadZone">
      <div class="float-right mt-2 mr-2">
        <close-button @click="closeUploader"></close-button>
      </div>
      <div class="dropzone" ref="dropzoneRef" id="dropZoneDocumentorImage">
        <div class="ml-2 mt-2 text-xs text-gray-400">
          Image (jpg, png, gif) files only, max size 3MB.
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import type {Documentor} from "@/api/query/interface/model";
import {onMounted, ref} from "vue";
import Dropzone from "dropzone";
import {useModelStore} from "@/stores/model";
import {useSchemaStore} from "@/stores/schema";
import CloseButton from "@/components/common/closeButton.vue";
import {removeDocumentorImage} from "@/api/command/model/removeDocumentorImage";

const apiUrl = import.meta.env.VITE_API_URL;

const props = defineProps<{
  documentor: Documentor
}>();

const modelStore = useModelStore();
const schemaStore = useSchemaStore();
const dropzoneRef = ref(null);
const showUploadZone = ref<boolean>(false);
const timestamp = new Date().getTime();

async function removeImage() {
  let result = await removeDocumentorImage(props.documentor.id);
  if(result){
    void modelStore.reLoadProject();
    void schemaStore.reloadDocumentation(modelStore.projectId);
  }
}

onMounted((): void => {
  createUploader();
});

function createUploader() {
   if (dropzoneRef.value !== null) {
    let dropzone = new Dropzone(dropzoneRef.value, {
      url: apiUrl + '/model/documentor/' + props.documentor.id + '/upload',
      maxFiles: 1,
      acceptedFiles: '.png,.bmp,.jpg,.jpeg',
      paramName: 'documentorImage',
      maxFilesize: 3,
      thumbnailWidth: 70,
      thumbnailHeight: 70
    })
    dropzone.on("complete", file => {
      dropzone.removeFile(file);
      showUploadZone.value = false;
      let timestamp = new Date().getTime();
      let documentorImage = document.getElementById("documentorImageRef");
      documentorImage.src = apiUrl + '/model/documentor/' + props.documentor.id + '/image?t=' + timestamp;
      void modelStore.reLoadProject();
      void schemaStore.reloadDocumentation(modelStore.projectId);
    });
   }
}

function openUploader() {
  showUploadZone.value = true;
}

function closeUploader() {
  showUploadZone.value = false;
}

</script>

<style>

#dropZoneDocumentorImage {
  display: block;
  min-height: 120px !important;
  height: 120px !important;
  padding: 2px !important;
}

.documentorImageSmall img {
  max-height: 120px;
  max-width: 700px;
  height: auto;
  width: auto;
}

</style>