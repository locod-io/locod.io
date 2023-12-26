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
            <related-document
                type="group"
                :id="group.id"
                :markdown="markdown"
                :related-project-document="group.relatedProjectDocument"/>
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
import type {WikiNodeGroup, WikiNode} from "@/_lodocio/api/interface/wiki";
import {useWikiStore} from "@/_lodocio/stores/wiki";
import {ref, onMounted} from "vue";
import {useAppStore} from "@/stores/app";
import SshPre from 'simple-syntax-highlighter';
import {convertGroupToMarkDown, convertNodeToMarkDown} from "@/_lodocio/function/nodeConverters";
import RelatedDocument from "@/_lodocio/components/wiki/document/relatedDocument.vue";

const props = defineProps<{ group: WikiNodeGroup }>();
const wikiStore = useWikiStore();
const isSaving = ref<boolean>(false);
const markdown = ref<string>('');
const appStore = useAppStore();

onMounted((): void => {
  markdown.value = "";
  if (wikiStore.wiki) {
    console.log('-- convert group to markdown');
    let _titleLevel = '';
    for (let i = 0; i < props.group.level + 1; i++) {
      _titleLevel += '#';
    }
    let _title = _titleLevel + " " + props.group.number + ". " + props.group.name + "\n\n";
    markdown.value = _title.trimStart();
    if (props.group.nodes.length > 0) {
      for (const node of props.group.nodes) {
        _convertNodeToMarkDown(node);
      }
    }
    for (const group of props.group.groups) {
      _convertGroupToMarkDown(group);
    }
  }
});

function _convertNodeToMarkDown(_node: WikiNode): void {
  markdown.value = markdown.value + "" + convertNodeToMarkDown(_node);
}

function _convertGroupToMarkDown(_group: WikiNodeGroup): void {
  markdown.value = markdown.value + "" + convertGroupToMarkDown(_group);
  if (_group.nodes) {
    for (const node of _group.nodes) {
      _convertNodeToMarkDown(node);
    }
  }
  if (_group.groups) {
    for (const __group of _group.groups) {
      _convertGroupToMarkDown(__group);
    }
  }
}

</script>

<style scoped>

</style>