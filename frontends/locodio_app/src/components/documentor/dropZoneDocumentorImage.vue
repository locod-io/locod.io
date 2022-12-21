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
  <div id="dropZoneProjectLogo" class="mt-2 ml-4">
    <div>Image</div>

    <div v-show="documentor.image" class="mt-2 mb-2">
      <Image id="documentorImageRef"
             :src="apiUrl+'/model/documentor/'+documentor.id+'/image?t=' + timestamp"
             :alt="documentor.image" preview/>
    </div>
    <div v-if="documentor.image" class="mb-2">
      <Button @click="removeImage"
              icon="pi pi-trash"
              label="remove image"
              class="p-button-sm p-button-outlined"/>
    </div>
    <div v-if="!documentor.status.isFinal">
      <div class="dropzone" ref="dropzoneRef" id="dropZoneDocumentorImage">
        <div class="ml-2 mt-2 text-xs text-gray-400">
          Image (jpg, png, gif) files only, max size 3MB.
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import Dropzone from "dropzone";
import type {Documentor} from "@/api/query/interface/model";
import {removeDocumentorImage} from "@/api/command/model/removeDocumentorImage";
import {useModelStore} from "@/stores/model";

const apiUrl = import.meta.env.VITE_API_URL;
const props = defineProps<{ documentor: Documentor }>();
const dropzoneRef = ref(null);
const timestamp = new Date().getTime();
const modelStore = useModelStore();

async function removeImage() {
  let result = await removeDocumentorImage(props.documentor.id);
  if (result) {
    props.documentor.image = '';
    void modelStore.reLoadProject();
  }
}

onMounted((): void => {
  if (dropzoneRef.value !== null) {
    let dropzone = new Dropzone(dropzoneRef.value, {
      url: apiUrl + '/model/documentor/' + props.documentor.id + '/upload',
      maxFiles: 1,
      acceptedFiles: '.png,.bmp,.jpg,.jpeg',
      paramName: 'documentorImage',
      maxFilesize: 3,
      thumbnailWidth: 70,
      thumbnailHeight: 70
    });
    dropzone.on("complete", file => {
      dropzone.removeFile(file);
      props.documentor.image = 'some-image';
      let timestamp = new Date().getTime();
      let documentorImage = document.getElementById("documentorImageRef");
      documentorImage.src = apiUrl + '/model/documentor/' + props.documentor.id + '/image?t=' + timestamp;
    });
  }
});

</script>

<style scoped>
#dropZoneDocumentorImage {
  height: 40px !important;
  padding: 2px !important;
}
</style>