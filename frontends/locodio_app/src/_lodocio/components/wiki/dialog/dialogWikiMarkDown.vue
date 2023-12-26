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
          <div v-if="wikiStore.wiki">
            <related-document type="wiki"
                              :related-project-document="wikiStore.wiki.relatedProjectDocument"
                              :id="wikiStore.wiki.id"
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
import type {WikiNode, WikiNodeGroup} from "@/_lodocio/api/interface/wiki";
import {useWikiStore} from "@/_lodocio/stores/wiki";
import {ref, onMounted} from "vue";
import {useAppStore} from "@/stores/app";
import SshPre from 'simple-syntax-highlighter';
import {convertGroupToMarkDown, convertNodeToMarkDown} from "@/_lodocio/function/nodeConverters";
import RelatedDocument from "@/_lodocio/components/wiki/document/relatedDocument.vue";

const wikiStore = useWikiStore();
const isSaving = ref<boolean>(false);
const markdown = ref<string>('');
const appStore = useAppStore();

onMounted((): void => {
  markdown.value = "";
  if (wikiStore.wiki) {
    console.log('-- convert wiki to markdown');
    markdown.value = wikiStore.wiki.name + "\n";
    for (let i = 0; i < wikiStore.wiki.name.length + 2; i++) {
      markdown.value = markdown.value + "=";
    }
    markdown.value = markdown.value + "\n\n";
    for (const node of wikiStore.wiki.nodes) {
      _convertNodeToMarkDown(node);
    }
    for (const group of wikiStore.wiki.groups) {
      _convertGroupToMarkDown(group);
    }
  }
});

function _convertNodeToMarkDown(node: WikiNode): void {
  markdown.value = markdown.value + "" + convertNodeToMarkDown(node);
}

function _convertGroupToMarkDown(group: WikiNodeGroup): void {
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