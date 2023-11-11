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

  <div v-if="nodeRef.files.length != 0" class="ml-8 mb-3 mr-4">
    <div>

      <Draggable
          v-model="nodeRef.files"
          tag="div"
          item-key="id"
          handle=".handle"
          @end="saveTrackerFileOrder"
          class="flex gap-2"
          ghost-class="ghost">

        <template #item="{ element }">
          <div class="w-32">
            <Image :src="apiUrl+'/doc/tracker/node/file/'+element.id+'/image?'+timestamp"
                   preview :alt="element.name"
                   class="w-32"/>
            <div class="flex gap-2" v-if="!nodeRef.status.isFinal">
              <div class="flex-none mt-1 text-gray-300 hover:text-green-600 cursor-move mr-2 dark:text-gray-600">
                <i class="pi pi-bars handle"></i>
              </div>
              <div class="flex-grow">&nbsp;</div>
              <div class="flex-none">
                <content-node-file-delete :file="element"/>
              </div>
            </div>
          </div>
        </template>

      </Draggable>

    </div>
  </div>
  <div v-if="!nodeRef.status.isFinal">
    <div class="float-right mr-4">
      <image-button @click="showUploadZoneFn" v-if="!showUploadZone"/>
      <close-button @click="hideUploadZoneFn" v-if="showUploadZone" class="mr-2 mt-2"/>
    </div>
    <div v-show="showUploadZone" class="border-[1px] border-gray ml-8 mb-3 mr-4">
      <div class="dropzone" ref="dropzoneRef" id="dropZoneTrackerImage">
        <div class="ml-2 mt-2 text-xs text-gray-400">
          Image (jpg, png, gif) files only, max size 10MB.
        </div>
      </div>
    </div>
  </div>

</template>

<script setup lang="ts">
import type {TrackerNode} from "@/_lodocio/api/interface/tracker";
import {toRef, onMounted, ref, computed} from "vue";
import Dropzone from "dropzone";
import {useAppStore} from "@/stores/app";
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import ContentNodeFileDelete from "@/_lodocio/components/tracker/content/contentNodeFileDelete.vue";
import ImageButton from "@/_common/buttons/imageButton.vue";
import CloseButton from "@/components/common/closeButton.vue";
import type {OrderTrackerFileCommand} from "@/_lodocio/api/command/tracker/orderTrackerFile";
import {orderTrackerFile} from "@/_lodocio/api/command/tracker/orderTrackerFile";
import {useToast} from "primevue/usetoast";
import Draggable from "vuedraggable";

const apiUrl = import.meta.env.VITE_API_URL;
const dropzoneRef = ref(null);
const timestamp = new Date().getTime();
const props = defineProps<{ node: TrackerNode }>();
const nodeRef = toRef(props, 'node');
const appStore = useAppStore();
const trackerStore = useTrackerStore();
const showUploadZone = ref<boolean>(false);
const toaster = useToast();

// -- save order of the files

const sequenceTrackers = computed((): OrderTrackerFileCommand => {
  let sequence = [];
  for (let i = 0; i < nodeRef.value.files.length; i++) {
    sequence.push(nodeRef.value.files[i].id)
  }
  return {sequence: sequence};
});

async function saveTrackerFileOrder() {
  await orderTrackerFile(sequenceTrackers.value);
  toaster.add({
    severity: "success",
    summary: "Image order saved",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await trackerStore.reloadTracker();
}

// -- upload file to a node

onMounted((): void => {
  if (dropzoneRef.value !== null) {
    let dropzone = new Dropzone(dropzoneRef.value, {
      url: apiUrl + '/doc/tracker/node/' + props.node.id + '/upload-file',
      maxFiles: 10,
      acceptedFiles: '.png,.bmp,.jpg,.jpeg',
      paramName: 'trackerNodeImage',
      maxFilesize: 10,
      thumbnailWidth: 70,
      thumbnailHeight: 70
    });
    dropzone.on("complete", file => {
      dropzone.removeFile(file);
      trackerStore.reloadTracker();
    });
  }
});

function showUploadZoneFn() {
  showUploadZone.value = true;
}

function hideUploadZoneFn() {
  showUploadZone.value = false;
}

</script>

<style scoped>
#dropZoneTrackerImage {
  height: 40px !important;
  padding: 2px !important;
}
</style>