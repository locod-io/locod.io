<template>
  <div id="dropZoneProjectLogo" class="mt-2">
    <div class="flex flex-row mt-4">
      <div class="basis-1/4 text-right">
        <div class="mt-1 text-sm">Project logo</div>
      </div>
      <div class="basis-3/4 ml-4">

        <!-- resulting logo -->
        <div v-if="showLogo">
          <div class="w-1/3 mx-auto">
            <img id="project-logo" :src="apiUrl+'/model/project/'+project.id+'/logo'" :alt="project.name">
          </div>
        </div>
        <!-- uploader -->
        <div class="dropzone" ref="dropzoneRef" id="projectLogoDropZone">
          <div class="ml-2 mt-2 text-xs text-gray-400">
            Image (jpg, png, gif) files only, max size 2MB.
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type {UserProject} from "@/api/query/interface/user";
import {onMounted, ref} from "vue";
import Dropzone from "dropzone";

const apiUrl = import.meta.env.VITE_API_URL;
const props = defineProps<{ project: UserProject }>();
const dropzoneRef = ref(null);
const showLogo = ref<boolean>(false);

onMounted((): void => {
  if (props.project.logo !== '') {
    showLogo.value = true;
  }
  if (dropzoneRef.value !== null) {
    let dropzone = new Dropzone(dropzoneRef.value, {
      url: apiUrl + '/model/project/' + props.project.id + '/upload-logo',
      maxFiles: 1,
      acceptedFiles: '.png,.bmp,.jpg,.jpeg',
      paramName: 'logoReference',
      maxFilesize: 2,
      thumbnailWidth: 70,
      thumbnailHeight: 70
    });
    dropzone.on("complete", file => {
      dropzone.removeFile(file);
      showLogo.value = true;
      const logo = document.getElementById("project-logo");
      if (logo) {
        logo.src = apiUrl + '/model/project/' + props.project.id + '/logo?' + new Date().getTime();
      }
    });
  }
});

</script>

<style scoped>
#projectLogoDropZone {
  height: 120px !important;
  padding: 2px !important;
}
</style>