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
            <related-document type="tracker"
                              :related-project-document="trackerStore.tracker.relatedProjectDocument"
                              :id="trackerStore.tracker.id"
                              :markdown="markdown"/>
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
import type {TrackerNode, TrackerNodeGroup} from "@/_lodocio/api/interface/tracker";
import {useTrackerStore} from "@/_lodocio/stores/tracker";
import {ref, onMounted} from "vue";
import {useAppStore} from "@/stores/app";
import SshPre from 'simple-syntax-highlighter';
import {convertGroupToMarkDown, convertNodeToMarkDown} from "@/_lodocio/function/nodeConverters";
import RelatedDocument from "@/_lodocio/components/tracker/document/relatedDocument.vue";

const trackerStore = useTrackerStore();
const isSaving = ref<boolean>(false);
const markdown = ref<string>('');
const appStore = useAppStore();

onMounted((): void => {
  markdown.value = "";
  if (trackerStore.tracker) {
    console.log('-- convert tracker to markdown');
    markdown.value = trackerStore.tracker.name + "\n";
    for (let i = 0; i < trackerStore.tracker.name.length + 2; i++) {
      markdown.value = markdown.value + "=";
    }
    markdown.value = markdown.value + "\n\n";
    for (const node of trackerStore.tracker.nodes) {
      _convertNodeToMarkDown(node);
    }
    for (const group of trackerStore.tracker.groups) {
      _convertGroupToMarkDown(group);
    }
  }
});

function _convertNodeToMarkDown(node: TrackerNode): void {
  markdown.value = markdown.value + "" + convertNodeToMarkDown(node);
}

function _convertGroupToMarkDown(group: TrackerNodeGroup): void {
  markdown.value = markdown.value + "" + convertGroupToMarkDown(group);
  for (const node of group.nodes) {
    _convertNodeToMarkDown(node);
  }
  for (const _group of group.groups) {
    _convertGroupToMarkDown(_group);
  }
}

</script>

<style scoped>

</style>