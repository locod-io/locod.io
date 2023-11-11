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
  <div id="dialogCodeGeneration">

    <div style="max-width: 800px;"
         class="mx-auto"
         v-if="isLoading">
      <div style="height:400px">
        <loading-spinner/>
      </div>
    </div>
    <div v-else style="max-width: 800px;" class="mx-auto">

      <div v-if="isGenerated">
        <SshPre
            :language="template.language.toLowerCase()"
            :label="template.language.toUpperCase()"
            :reactive="true"
            :copy-button="true"
            v-model="code">
          <template #copy-button>
            <div>
              <Button label="Copy" icon="pi pi-copy"
                      class="p-button-outlined p-button-secondary p-button-sm"></Button>
            </div>
          </template>
          {{ code }}
        </SshPre>
      </div>
      <div v-else>
        <div class="font-mono dark:text-yellow-100 text-yellow-900">
          <i class="pi pi-exclamation-triangle"></i>
          {{errorMessage}}
        </div>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import SshPre from 'simple-syntax-highlighter';
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import {generateTemplateBySubjectId} from "@/api/query/model/getTemplate";
import type {GeneratedTemplate, Template} from "@/api/query/interface/model";


const props = defineProps<{
  template: Template,
  subjectId: number
}>();

const isLoading = ref<boolean>(true);
const code = ref<string>('');
const isGenerated = ref<boolean>(true);
const errorMessage = ref<string>('');

onMounted((): void => {
  void generatedCode();
});

async function generatedCode() {
  const generatedTemplate: GeneratedTemplate = await generateTemplateBySubjectId(props.template.id, props.subjectId);
  code.value = generatedTemplate.code;
  isGenerated.value = generatedTemplate.isGenerated;
  errorMessage.value = generatedTemplate.errorMessage;
  isLoading.value = false;
}

</script>

<style scoped>

.ssh-pre {
  font-size: 14px !important;
}

</style>