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
  <div class="mt-2 flex flex-row bg-indigo-100 p-2 rounded-lg">
    <div class="basis-1/5 mt-2 text-right text-sm">
      Generate some code
    </div>
    <div class="basis-3/5 ml-2">
      <Dropdown optionLabel="name"
                :options="templates"
                v-model="selectedTemplate"
                placeholder="Select a template"
                class="w-full p-dropdown-sm"/>
    </div>
    <div class="basis-1/5 ml-2">
      <Button label="GENERATE"
              :disabled="!(selectedTemplate)"
              icon="pi pi-box"
              class="w-full p-button-sm"
              @click="generateCode"/>
    </div>
  </div>

  <!-- dialog code generation // full screen overlay  -->
  <Sidebar v-model:visible="showCodeGenerator"
           position="full"
           v-on:keyup.esc="showCodeGenerator=false">
    <dialog-code-generation :template="selectedTemplate" :subject-id="subjectId"/>
  </Sidebar>

</template>

<script setup lang="ts">
import {computed, ref} from "vue";
import DialogCodeGeneration from "@/components/model/dialogCodeGeneration.vue";
import type {Template} from "@/api/query/interface/model";
import {useModelStore} from "@/stores/model";

const props = defineProps<{
  type: string,
  subjectId: number
}>();
const modelStore = useModelStore();
const showCodeGenerator = ref<boolean>(false);
const selectedTemplate = ref<Template>();

const templates = computed((): Array<Template> => {
  if (modelStore.project) {
    let templates = [];
    for (let i = 0; i < modelStore.project.templates.length; i++) {
      if (modelStore.project.templates[i].type == props.type) {
        templates.push(modelStore.project.templates[i]);
      }
    }
    return templates;
  } else {
    return [];
  }
});

function generateCode() {
  showCodeGenerator.value = true;
}

</script>