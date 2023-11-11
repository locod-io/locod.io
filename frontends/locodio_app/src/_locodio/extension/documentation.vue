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
  <div>
    <div class="flex gap-2 border-b-[1px] border-gray-300 dark:border-gray-600 h-12">
      <div v-if="documentor.status.isFinal === false" class="flex-none py-2 pl-2">
        <Button
            v-if="!isSaving"
            @click="change"
            icon="pi pi-save"
            label="SAVE"
            class="p-button-success p-button-sm w-32"/>
        <Button
            v-else
            disabled
            icon="pi pi-spin pi-spinner"
            label="SAVE"
            class="p-button-success p-button-sm w-32"/>
      </div>
      <div class="flex-none py-2 pl-2">
        <div class="flex">
          <div>
            <Button
                @click="loadDocumentor"
                v-if="!modelStore.documentorLoading"
                icon="pi pi-refresh"
                class="p-button-sm"/>
            <Button
                v-else
                disabled
                icon="pi pi-spin pi-spinner"
                class="p-button-sm"/>
          </div>
        </div>
      </div>
    </div>

    <!-- -- editor -->
    <div class="text-sm">
      <div v-if="documentor && documentor.status.isFinal" class="bg-white dark:bg-gray-900 border-b-[1px] border-gray-300 dark:border-gray-600">
        <div v-html="documentor.description" class="descriptionWrapper p-4"></div>
      </div>
      <div v-else>
        <simple-editor v-model="description"/>
      </div>
    </div>
    <div>
      <drop-zone-documentor-image :documentor="documentor"/>
    </div>

<!--    <div class="text-sm p-4">-->
<!--      {{documentor}}-->
<!--    </div>-->


  </div>
</template>

<script setup lang="ts">
import {computed, onMounted, ref, watch} from "vue";
import {useModelStore} from "@/stores/model";
import type {Documentor} from "@/api/query/interface/model";
import type {ChangeDocumentorCommand} from "@/api/command/interface/modelConfiguration";
import SimpleEditor from "@/_common/editor/simpleEditor.vue";
import type {ModelStatus} from "@/api/query/interface/model";
import {changeDocumentor} from "@/api/command/model/changeDocumentor";
import {useToast} from "primevue/usetoast";
import DropZoneDocumentorImage from "@/components/documentor/dropZoneDocumentorImage.vue";

const props = defineProps<{
  type: 'module' | 'domain-model' | 'enum' | 'query' | 'command',
  id: number
}>();

const documentorId = computed((): number => {
  if (props.type == 'domain-model') return modelStore.domainModel?.documentor.id ?? 0;
  if (props.type == 'enum') return modelStore.enum?.documentor.id ?? 0;
  if (props.type == 'query') return modelStore.query?.documentor.id  ?? 0;
  if (props.type == 'command') return modelStore.command?.documentor.id ?? 0;
  return modelStore.module?.documentor.id ?? 0;
});

const documentor = computed((): Documentor | undefined => {
  if (props.type == 'domain-model') return modelStore.domainModelDocumentor;
  if (props.type == 'enum') return modelStore.enumDocumentor;
  if (props.type == 'query') return modelStore.queryDocumentor;
  if (props.type == 'command') return modelStore.commandDocumentor;
  return modelStore.moduleDocumentor;
});

const modelStore = useModelStore();
const toaster = useToast();

onMounted((): void => {
  void loadDocumentor();
});

const isSaving = ref<boolean>(false);
const description = ref<string>('');
const selectedStatus = ref<ModelStatus>({id: 0, name: '', color: 'CCC'});

async function loadDocumentor() {
  await modelStore.loadDocumentor(documentorId.value, props.type, props.id, false, false);
  selectedStatus.value = documentor.value.status;
  description.value = documentor.value.description;
}

watch(documentor, (value) => {
    selectedStatus.value = value.status;
    description.value = value.description;
});

const command = computed<ChangeDocumentorCommand>(() => {
  let command: ChangeDocumentorCommand = {};
  command.id = modelStore.documentor?.id ?? 0;
  command.description = description.value;
  command.statusId = selectedStatus.value.id;
  return command;
});

async function change() {
  isSaving.value = true;
  let result = await changeDocumentor(command.value);
  await modelStore.reLoadProject();
  if (modelStore.documentor) {
    switch (modelStore.documentor.type) {
      case 'domain-model':
        await modelStore.loadDocumentor(documentor.value.id,documentor.value.type,modelStore.domainModel.id,false,true);
        await modelStore.reLoadDomainModel();
        break;
      case 'enum':
        await modelStore.loadDocumentor(documentor.value.id,documentor.value.type,modelStore.enum.id,false,true);
        await modelStore.reLoadEnum();
        break;
      case 'query':
        await modelStore.loadDocumentor(documentor.value.id,documentor.value.type,modelStore.query.id,false,true);
        await modelStore.reLoadQuery();
        break;
      case 'command':
        await modelStore.loadDocumentor(documentor.value.id,documentor.value.type,modelStore.command.id,false,true);
        await modelStore.reLoadCommand();
        break;
    }
  }
  isSaving.value = false;
  toaster.add({
    severity: "success",
    summary: "Documentation saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
}

</script>

<style scoped>

</style>