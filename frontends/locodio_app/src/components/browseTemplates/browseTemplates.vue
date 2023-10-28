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
  <div v-if="!isLoading" class="overflow-auto">

    <DataView :value="filteredTemplates" :layout="layout" paginatorPosition="bottom" :paginator="true" :rows="12">
      <template #header>
        <div class="p-input-icon-right w-1/4">
          <InputText
              type="text"
              class="w-full p-inputtext-sm"
              v-model="search"/>
          <i class="pi pi-search"/>
        </div>
      </template>

      <template #list="slotProps">
        <div></div>
      </template>

      <template #grid="slotProps">
        <div class="basis-1/3 p-2">
          <div @click="selectTemplate(slotProps.data)"
               class="border-gray-200 border-2 rounded-xl p-2 bg-gray-100 cursor-pointer">
            <div>
              <i class="pi pi-database" v-if="(slotProps.data.type === 'domain_model')"></i>
              <i class="pi pi-align-justify" v-if="(slotProps.data.type === 'enum')"></i>
              <i class="pi pi-download" v-if="(slotProps.data.type === 'command')"></i>
              <i class="pi pi-upload" v-if="(slotProps.data.type === 'query')"></i>
              {{ slotProps.data.name }}
            </div>
            <div class="text-right text-xs text-gray-400">
              <Timeago :datetime="slotProps.data.lastUpdatedAt" long/>
            </div>
            <div class="text-sm mt-1 mb-1"
                 v-if="slotProps.data.description.trim().length != 0"
                 v-html="slotProps.data.description"></div>
            <div v-else>&nbsp;</div>
            <div class="mt-2 border-t-[1px] pt-1 border-gray-200 flex">
              <div class="rounded-full px-2 py-1 text-xs text-white mr-1 font-bold"
                   :style="'background-color:'+slotProps.data.from.color">
                <i class="pi pi-user"></i>&nbsp;&nbsp;{{ slotProps.data.from.initials }}
              </div>
              <div>
                <Badge :value="slotProps.data.language" class="p-badge-secondary"/>&nbsp;
              </div>
              <div>
              <span v-for="tag in slotProps.data.tags"
                    class="text-xs rounded-full bg-blue-400 py-0.5 px-1.5 ml-1 text-white">
                {{ tag }}
              </span>
              </div>
            </div>
          </div>
        </div>

      </template>
    </DataView>
  </div>
  <div v-else>
    <loading-spinner/>
  </div>

  <!-- -- detail template -----------------------------------------------------------------------------------------  -->
  <Dialog v-model:visible="showDetail"
          :header="detailTemplate.name"
          :modal="true">
    <template #header>
      <div class="flex w-full">
        <div>
          <div @click="forkTemplateAction"
               class="bg-blue-400 w-[14rem] rounded-lg text-white flex cursor-pointer pb-0.5 hover:bg-blue-500">
            <div class="mr-1 ml-2">
              <font-awesome-icon icon="fa-solid fa-code-fork"/>
            </div>
            <div class="text-sm pt-0.5">Fork this template in my library</div>
          </div>
        </div>
        <div class="ml-2">
          <Badge :value="detailTemplate.language" class="p-badge-secondary"/>&nbsp;
        </div>
        <div class="ml-2">{{ detailTemplate.name }}</div>
      </div>
    </template>

    <div style="display:block;width:900px;">
      <div v-if="detailTemplate.template" class="text-sm">
        <SshPre
            language="php"
            label="twig"
            :reactive="true"
            :copy-button="true"
            v-model="detailTemplate.template">
          <template #copy-button>
            <div>
              <Button label="Copy" icon="pi pi-copy"
                      class="p-button-outlined p-button-secondary p-button-sm"></Button>
            </div>
          </template>
          {{ detailTemplate.template }}
        </SshPre>
      </div>
    </div>
  </Dialog>

</template>

<script setup lang="ts">
import {computed, onMounted, ref} from "vue";
import type {MasterTemplate} from "@/api/query/interface/templates";
import {browseTemplates} from "@/api/query/user/browseTemplates";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import {Timeago} from 'vue2-timeago';
import SshPre from 'simple-syntax-highlighter';
import {useAppStore} from "@/stores/app";
import type {ForkTemplateCommand} from "@/api/command/interface/userCommands";
import {forkTemplate} from "@/api/command/model/forkTemplate";
import {useToast} from "primevue/usetoast";

// -- props & stores

const isLoading = ref<boolean>(true);
const templates = ref<Array<MasterTemplate>>([]);
const layout = ref<string>('grid');
const showDetail = ref<boolean>(false);
const detailTemplate = ref<MasterTemplate>({name: ''});
const search = ref<string>('');
const isForking = ref<boolean>(false);
const appStore = useAppStore();
const toaster = useToast();

onMounted((): void => {
  getPublicTemplates();
});

function selectTemplate(template: MasterTemplate) {
  showDetail.value = true;
  detailTemplate.value = template;
}

async function getPublicTemplates() {
  isLoading.value = true;
  templates.value = await browseTemplates();
  isLoading.value = false;
}

const filteredTemplates = computed((): Array<MasterTemplate> => {
  if (templates) {
    if (search.value === '') {
      return templates.value;
    } else {
      const filterValue = search.value.toLowerCase();
      let _result = [];
      for (const template of templates.value) {
        if (template.name.toLowerCase().includes(filterValue)
            || template.language.toLowerCase().includes(filterValue)
            || template.description.toLowerCase().includes(filterValue)
            || template.tags.includes(filterValue)
            || template.type.toLowerCase().includes(filterValue)
        ) {
          _result.push(template)
        }
      }
      return _result;
    }
  } else {
    return [];
  }
});

async function forkTemplateAction() {
  if (appStore.user && detailTemplate) {
    isForking.value = true;
    let command: ForkTemplateCommand = {
      userId: appStore.user.id,
      templateId: detailTemplate.value.id
    };
    const result = await forkTemplate(command);
    toaster.add({
      severity: "success",
      summary: "Template forked, template now available in your library (my templates).",
      detail: "",
      life: appStore.toastLifeTime,
    });
    isForking.value = false;
    showDetail.value = false;
  }
}

</script>