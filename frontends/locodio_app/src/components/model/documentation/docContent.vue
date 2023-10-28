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
    <div class="flex p-2 border-b-[1px] border-gray-300 dark:border-gray-600 h-12">
      <div>
        <Button v-if="!schemaStore.isDocumentationLoading"
                icon="pi pi-refresh" @click="loadDocumentation"
                class="p-button-sm p-button-icon"/>
        <Button v-else icon="pi pi-spinner pi-spin"
                class="p-button-sm"
                disabled/>
      </div>
      <div class="ml-2">
        <Button @click="downloadDocumentationPdf"
                icon="pi pi-file-pdf" title="Download as PDF"
                class="p-button-sm p-button-outlined p-button-secondary p-button-icon-only"/>
      </div>
    </div>

    <!-- documentation content -->
    <DetailWrapper :estate-height="86">
      <div id="documentationWrapper" class="text-gray-700 bg-white dark:bg-gray-900 dark:text-gray-50">

        <div v-for="docItem in schemaStore.documentation.items"
             class="border-b-[1px] border-gray-300 dark:border-gray-600 p-4"
             :id="'doc-'+docItem.labelLevel">

          <div>
            <!-- id and status render -->
            <div v-if="docItem.item" class="text-sm float-right mt-2">
              <status-badge-small :id="docItem.artefactId" :status="docItem.item.documentor.status"/>
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
            <div v-if="docItem.typeCode === 'AT'">
              <doc-content-attributes :doc-item="docItem"/>
            </div>
            <div v-if="docItem.typeCode === 'AS'">
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
  let url = apiUrl + '/model/project/' + modelStore.projectId + '/documentation/download';
  window.open(url, '_blank');
}

</script>