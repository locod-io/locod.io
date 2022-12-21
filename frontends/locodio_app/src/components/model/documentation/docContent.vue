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
  <div id="docContent">

    <!-- toolbar -->
    <div class="p-1 flex">
      <div>
        <Button v-if="!schemaStore.isDocumentationLoading"
                icon="pi pi-refresh" @click="loadDocumentation"
                class="p-button-sm"/>
        <Button v-else icon="pi pi-spinner pi-spin"
                class="p-button-sm"
                disabled/>
      </div>
      <div class="ml-2">
          <Button @click="downloadDocumentationPdf"
                icon="pi pi-download"
                class="p-button-sm p-button-outlined"
                label="download pdf"/>
      </div>
    </div>

    <!-- documentation content -->
    <DetailWrapper :estate-height="193">
      <div id="documentationWrapper" style="max-width:750px;" class="p-4 text-gray-700">

        <div v-for="docItem in schemaStore.documentation.items" class="py-1" :id="'doc-'+docItem.labelLevel">

          <div class="border-t-[1px] border-gray-400">
            <!-- id and status render -->
            <div v-if="docItem.item" class="text-sm float-right mt-2">
              <status-badge-small :id="docItem.typeCode+'-'+docItem.id" :status="docItem.item.documentor.status"/>
            </div>
            <!-- title -->
            <div class="text-lg font-bold" v-if="docItem.level === 1">
              {{ docItem.labelLevel }} {{ docItem.label }}
            </div>
            <div class="text-lg font-semibold" v-if="docItem.level === 2">
              {{ docItem.labelLevel }} {{ docItem.label }}
            </div>
            <div class="font-semibold" v-if="docItem.level === 3">
              {{ docItem.labelLevel }} {{ docItem.label }}
            </div>
            <div class="font-semibold text-sm" v-if="docItem.level === 4">
              {{ docItem.labelLevel }} {{ docItem.label }}
            </div>
          </div>

          <!-- content of an item -->
          <div v-if="docItem.item">
            <doc-content-item :doc-item="docItem"></doc-content-item>
            <div v-if="docItem.typeCode === 'E'">
              <doc-content-enum-options :doc-item="docItem"/>
            </div>
          </div>
          <div v-else>
            <div v-if="docItem.typeCode === 'ATTR'">
              <doc-content-attributes :doc-item="docItem"/>
            </div>
            <div v-if="docItem.typeCode === 'ASS'">
              <doc-content-associations :doc-item="docItem"/>
            </div>
          </div>

        </div>
      </div>
    </DetailWrapper>

  </div>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import {useModelStore} from "@/stores/model";
import DetailWrapper from "@/components/wrapper/detailWrapper.vue";
import StatusBadgeSmall from "@/components/common/statusBadgeSmall.vue";
import {useSchemaStore} from "@/stores/schema";
import DocContentItem from "@/components/model/documentation/docContentItem.vue";
import DocContentAttributes from "@/components/model/documentation/docContentAttributes.vue";
import DocContentAssociations from "@/components/model/documentation/docContentAssociations.vue";
import DocContentEnumOptions from "@/components/model/documentation/docContentEnumOptions.vue";

const apiUrl = import.meta.env.VITE_API_URL as string;
const modelStore = useModelStore();
const schemaStore = useSchemaStore();

onMounted((): void => {
  loadDocumentation();
});

function loadDocumentation() {
  void schemaStore.loadDocumentation(modelStore.projectId);
  void modelStore.reLoadProject();
}

function downloadDocumentationPdf() {
  let url = apiUrl+'/model/project/'+modelStore.projectId+'/documentation/download';
  window.open(url, '_blank');
}

</script>