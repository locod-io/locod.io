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
  <div style="width:700px;" class="p-2">
    <div class="border-t-[1px] border-gray-300 dark:border-gray-600">
      <TabView>
        <TabPanel header="Related Document">
          <div v-if="trackerStore.tracker">
            <related-document
                type="node"
                :id="nodeRef.id"
                :markdown="markdown"
                :related-project-document="nodeRef.relatedProjectDocument"/>
          </div>
        </TabPanel>
        <TabPanel header="Markdown">
          <div class="text-sm">
            <SshPre
                language="markdown"
                label="md"
                :reactive="true"
                :copy-button="true"
                v-model="markdown">
              <template #copy-button>
                <div>
                  <Button label="Copy"
                          icon="pi pi-copy"
                          class="p-button-outlined p-button-secondary p-button-sm"/>
                </div>
              </template>
              {{ markdown }}
            </SshPre>
          </div>
        </TabPanel>
      </TabView>
    </div>
  </div>
</template>

<script setup lang="ts">
import type {TrackerNode} from "@/_lodocio/api/interface/tracker";
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import {ref, toRef, onMounted} from "vue";
import {useAppStore} from "@/stores/app";
import SshPre from 'simple-syntax-highlighter';
import {convertNodeToMarkDown} from "@/_lodocio/function/nodeConverters";
import RelatedDocument from "@/_lodocio/components/tracker/document/relatedDocument.vue";

const props = defineProps<{ node: TrackerNode }>();
const trackerStore = useTrackerStore();
const isSaving = ref<boolean>(false);
const nodeRef = toRef(props, 'node');
const markdown = ref<string>('');
const appStore = useAppStore();

onMounted((): void => {
  markdown.value = convertNodeToMarkDown(nodeRef.value);
});

</script>

<style scoped>

</style>