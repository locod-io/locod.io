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
  <div class="mr-2">
    <delete-button @deleted="deleteNode"/>
  </div>
</template>

<script setup lang="ts">
import DeleteButton from "@/components/common/deleteButton.vue";
import type {WikiNode} from "@/_lodocio/api/interface/wiki";
import {useWikiStore} from "@/_lodocio/stores/wiki";
import {useToast} from "primevue/usetoast";
import {useAppStore} from "@/stores/app";
import {ref} from "vue";
import {deleteWikiNode} from "@/_lodocio/api/command/wiki/deleteWikiNode";

const props = defineProps<{ node: WikiNode }>();
const wikiStore = useWikiStore();
const toaster = useToast();
const appStore = useAppStore();
const isSaving = ref<boolean>(false);

async function deleteNode() {
  isSaving.value = true;
  await wikiStore.removeNodeFromStructure(props.node.uuid);
  await deleteWikiNode({id: props.node.id});
  toaster.add({
    severity: "success",
    summary: wikiStore.wiki?.code + "-" + props.node.artefactId + " deleted.",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await wikiStore.reloadWiki();
  isSaving.value = false;
}

</script>

<style scoped>

</style>