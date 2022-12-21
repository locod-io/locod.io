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
  <div id="detailDocumentor" style="width:1100px;">
    <div v-if="modelStore.documentorLoading" style="min-height:350px;">
      <loading-spinner/>
    </div>
    <div v-else>
      <div class="flex-row flex">
        <div class="basis-1/2">
          <div class="flex">
            <div class="text-right mr-4 mt-2">
              Status
            </div>
            <div class="basis-2/5 p-inputtext-sm" style="padding:0 !important;">
              <Dropdown class="w-full"
                        v-model="selectedStatus"
                        :options="availableStatus"
                        option-label="name">
                <template #value="slotProps">
                  <div class="rounded-full px-3 py-1 text-white font-bold text-xs" v-if="slotProps.value"
                       :style="'background-color:#'+slotProps.value.color">
                    {{ slotProps.value.name }}
                  </div>
                  <span v-else>
                {{ slotProps.placeholder }}
              </span>
                </template>
                <template #option="slotProps">
                  <div class="rounded-full px-3 py-1 text-white text-xs font-bold"
                       :style="'background-color:#'+slotProps.option.color">
                    {{ slotProps.option.name }}
                  </div>
                </template>
              </Dropdown>
            </div>

            <div v-if="modelStore.documentor && modelStore.documentor.status.isFinal" class="text-sm">
              <div class="mt-2 ml-2">
                by
                <email-label :email="modelStore.documentor.finalBy"/>
                -
                <Timeago :datetime="modelStore.documentor.finalAt" long/>
              </div>
            </div>

          </div>
          <!-- -- editor -->
          <div class="mt-4">
            <div v-if="modelStore.documentor && modelStore.documentor.status.isFinal">
              <div v-html="modelStore.documentor.description" class="descriptionWrapper"></div>
              <hr>
            </div>
            <div v-else>
              <simple-editor v-model="description"/>
            </div>
          </div>
        </div>
        <div class="basis-1/2">
          <div v-if="modelStore.documentor">
            <drop-zone-documentor-image :documentor="modelStore.documentor"/>
          </div>
        </div>
      </div>
      <!-- -- buttons -->
      <div class="flex flex-row mt-4">
        <div class="basis-1/4">
          <Button v-if="!isSaving"
                  @click="saveDocumentor(false)"
                  class="p-button-sm p-button-success w-full"
                  icon="pi pi-save"
                  label="SAVE"/>
          <Button v-else
                  label="SAVE"
                  disabled
                  icon="pi pi-spinner pi-spin"
                  class="p-button-sm p-button-success w-full"/>
        </div>
        <div class="basis-1/4 ml-4">
          <Button v-if="!isSaving"
                  @click="saveDocumentor(true)"
                  class="p-button-sm p-button-success w-full"
                  icon="pi pi-save"
                  label="SAVE AND CLOSE"/>
          <Button v-else
                  label="SAVE AND CLOSE"
                  disabled
                  icon="pi pi-spinner pi-spin"
                  class="p-button-sm p-button-success w-full"/>
        </div>
        <div class="basis-1/4">
          &nbsp;
        </div>
        <div class="basis-1/4">
          <Button @click="closeDialog"
                  class="p-button-sm p-button-outlined w-full"
                  icon="pi pi-times"
                  label="CANCEL"/>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {useModelStore} from "@/stores/model";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import SimpleEditor from "@/components/editor/simpleEditor.vue";
import {computed, onActivated, onDeactivated, onMounted, onUnmounted, ref, watch} from "vue";
import type {ModelStatus} from "@/api/query/interface/model";
import type {ChangeDocumentorCommand} from "@/api/command/interface/modelConfiguration";
import {changeDocumentor} from "@/api/command/model/changeDocumentor";
import {useToast} from "primevue/usetoast";
import {Timeago} from 'vue2-timeago';
import EmailLabel from "@/components/common/emailLabel.vue";
import DropZoneDocumentorImage from "@/components/documentor/dropZoneDocumentorImage.vue";

const modelStore = useModelStore();
const selectedStatus = ref<ModelStatus>({id: 0, name: '', color: 'CCC'});
const description = ref<string>('');
const toaster = useToast();
const isSaving = ref<boolean>(false);

function closeDialog() {
  modelStore.showDocumentor = false;
}

async function saveDocumentor(close: boolean) {
  isSaving.value = true;
  let result = await changeDocumentor(command.value);
  await modelStore.reLoadProject();
  if (modelStore.documentor) {
    switch (modelStore.documentor.type) {
      case 'domain-model':
        await modelStore.reLoadDomainModel();
        break;
      case 'enum':
        await modelStore.reLoadEnum();
        break;
      case 'query':
        await modelStore.reLoadQuery();
        break;
      case 'command':
        await modelStore.reLoadCommand();
        break;
    }
  }
  isSaving.value = false;
  if (close) {
    toaster.add({
      severity: "success",
      summary: "Status & documentation saved",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    closeDialog();
  }
}

// -- shortcut to save ------------------------------------------------------------

onMounted((): void => {
  document.addEventListener("keydown", shortcutHandler);
});

onUnmounted(() => {
  document.removeEventListener("keydown", shortcutHandler);
});

onActivated((): void => {
  document.addEventListener("keydown", shortcutHandler);
});

onDeactivated(() => {
  document.removeEventListener("keydown", shortcutHandler);
});

function shortcutHandler(event) {
  if (!((event.keyCode === 83 && event.ctrlKey) || (event.keyCode === 68 && event.ctrlKey))) {
    return;
  }
  event.preventDefault();
  if ((event.keyCode === 83 && event.ctrlKey)) {
    saveDocumentor(false);
  }
}

// -- computed -------------------------------------------------------------------

const command = computed<ChangeDocumentorCommand>(() => {
  let command: ChangeDocumentorCommand = {};
  command.id = modelStore.documentor?.id ?? 0;
  command.description = description.value;
  command.statusId = selectedStatus.value.id;
  return command;
});

const documentor = computed(() => {
  return modelStore.documentor;
});

watch(documentor, (value) => {
  if (modelStore.documentor) {
    selectedStatus.value = modelStore.documentor.status;
    description.value = modelStore.documentor.description;
  }
});

const availableStatus = computed(() => {
  let status = [];
  if (modelStore.documentor) {
    status.push(modelStore.documentor.status);
  }
  if (modelStore.project) {
    for (const statusItem of modelStore.project.status) {
      for (const flowOutStatus of modelStore.documentor.status.flowOut) {
        if (statusItem.id === flowOutStatus) {
          status.push(statusItem);
        }
      }
    }
  }
  return status;
});

</script>