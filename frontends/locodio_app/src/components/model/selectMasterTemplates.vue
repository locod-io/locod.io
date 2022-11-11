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
  <div id="selectMasterTemplates">

    <div class="flex">
      <div class="w-full">
        <div class="p-input-icon-right w-full">
          <InputText
              type="text"
              class="w-full p-inputtext-sm"
              v-model="search"/>
          <i class="pi pi-search"/>
        </div>
      </div>
    </div>

    <div class="mt-2">
      <div class="flex border-b-[1px] my-2 pb-2" v-for="template in filteredTemplates">
        <div>
          <Checkbox name="masterTemplateId" :value="template.id" v-model="command.masterTemplateIds"/>
        </div>
        <div class="ml-2">
          <i class="pi pi-database" v-if="(template.type === 'domain_model')"></i>
          <i class="pi pi-align-justify" v-if="(template.type === 'enum')"></i>
          <i class="pi pi-download" v-if="(template.type === 'command')"></i>
          <i class="pi pi-upload" v-if="(template.type === 'query')"></i>
        </div>
        <div class="ml-2">
          <Badge :value="template.language" class="p-badge-secondary"/>
        </div>
        <div class="text-sm ml-2 mt-0.5">{{ template.name }}</div>
        <div>
          <span v-for="tag in template.tags"
                class="text-xs rounded-full bg-blue-400 py-0.5 px-1.5 ml-1 text-white">
                {{tag}}
          </span>
        </div>
      </div>
    </div>

    <div class="mt-2">
      <Button
          @click="doImport"
          v-if="!isImporting"
          icon="pi pi-save"
          label="IMPORT"
          :disabled="(command.masterTemplateIds.length === 0)"
          class="p-button-success p-button-sm w-full"/>
      <Button
          v-else
          disabled
          icon="pi pi-spin pi-spinner"
          label="IMPORTING"
          class="p-button-success p-button-sm w-full"/>
    </div>

  </div>
</template>

<script setup lang="ts">
import {useModelStore} from "@/stores/model";
import {useToast} from "primevue/usetoast";
import {computed, ref} from "vue";
import type {ImportTemplatesFromMasterTemplatesCommand} from "@/api/command/interface/templateCommands";
import {importTemplatesFromMasterTemplates} from "@/api/command/model/importTemplatesFromMasterTemplates";
import type {Template} from "@/api/query/interface/model";
import type {UserMasterTemplate} from "@/api/query/interface/user";

const modelStore = useModelStore();
const toaster = useToast();
const search = ref<string>('');
const isImporting = ref<boolean>(false);
const emit = defineEmits(["imported"]);

const command = ref<ImportTemplatesFromMasterTemplatesCommand>({
  projectId: modelStore.projectId,
  masterTemplateIds: [],
});

const filteredTemplates = computed((): Array<UserMasterTemplate> => {
  if (modelStore.masterTemplates) {
    if (search.value === '') {
      return modelStore.masterTemplates;
    } else {
      const filterValue = search.value.toLowerCase();
      const filter = event => event.name.toLowerCase().includes(filterValue)
          || event.language.toLowerCase().includes(filterValue)
          || event.type.toLowerCase().includes(filterValue)
          || event.tags.includes(filterValue)
          || command.value.masterTemplateIds.includes(event.id);
      return modelStore.masterTemplates.filter(filter);
    }
  } else {
    return [];
  }
});

async function doImport() {
  isImporting.value = true;
  await importTemplatesFromMasterTemplates(command.value);
  toaster.add({
    severity: "success",
    summary: "Template(s) imported.",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadProject();
  isImporting.value = false;
  emit("imported");
}

</script>