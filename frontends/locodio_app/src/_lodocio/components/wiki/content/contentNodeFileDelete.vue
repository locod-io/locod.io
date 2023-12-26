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
  <div v-if="!isSaving">
    <delete-button @deleted="deleteImage"/>
  </div>
  <div v-else>
    <i class="pi pi-spin pi-spinner"/>
  </div>
</template>

<script setup lang="ts">
import {ref} from "vue";
import {useAppStore} from "@/stores/app";
import {useWikiStore} from "@/_lodocio/stores/wiki";
import DeleteButton from "@/components/common/deleteButton.vue";
import {deleteWikiFile} from "@/_lodocio/api/command/wiki/deleteWikiFile";
import type {WikiFile} from "@/_lodocio/api/interface/wiki";
import {useToast} from "primevue/usetoast";

const props = defineProps<{ file: WikiFile }>();
const appStore = useAppStore();
const wikiStore = useWikiStore();
const isSaving = ref<boolean>(false);
const toaster = useToast();

async function deleteImage() {
  console.log('-- delete image');
  isSaving.value = true;
  await deleteWikiFile({id: props.file.id})
  toaster.add({
    severity: "success",
    summary: "File " + props.file.name + " deleted",
    detail: "",
    life: appStore.toastLifeTime
  });
  await wikiStore.reloadWiki();
  isSaving.value = false;
}

</script>

<style scoped>
#dropZoneWikiImage {
  height: 40px !important;
  padding: 2px !important;
}
</style>