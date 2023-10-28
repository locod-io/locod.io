<template>
  <div id="statusBadge" class="flex gap-2">

    <div v-if="modelStore.documentor && modelStore.documentor.status.isFinal" class="text-xs flex-none">
      <div class="ml-2 mt-1.5">
        {{ documentor.status.name }}
        by
        <email-label :email="modelStore.documentor.finalBy"/>
        -
        <Timeago :datetime="modelStore.documentor.finalAt" long/>
      </div>
    </div>

    <div class="bg-gray-300 rounded-full flex flex-none w-46 dark:bg-gray-600">

      <!-- subject id -->
      <div class="w-14 text-center font-bold py-0.5 mt-1 cursor-pointer text-xs"
           @click="open">
        {{ props.artefactId }}
      </div>

      <!-- status -->
      <div :style="'background-color:#'+documentor.status.color"
           @click="toggle"
           aria-haspopup="true"
           aria-controls="status_menu"
           class="rounded-full w-32 text-white font-bold px-2 py-0.5 flex flex-row-reverse cursor-pointer">

        <!-- select other status -->
        <!-- style="margin-top:-3px;margin-right:-3px;" -->
        <div
            v-if="!isSaving"
            class="cursor-pointer">
          <font-awesome-icon icon="fa-solid fa-circle-chevron-down"/>
        </div>
        <div v-else style="font-size:.9em;">
          <font-awesome-icon icon="fa-solid fa-circle-notch" class="pi-spin"/>
        </div>

        <!-- menu -->
        <Menu id="status_menu" ref="menu" :model="availableStatus" :popup="true">
          <template #item="{item}">
            <div class="p-2 cursor-pointer" @click="changeStatus(item.id)">
              <div class="text-white rounded-full py-0.5 px-3 w-48 text-sm text-center"
                   :style="'background-color:#'+item.color+''">
                {{ item.name }}
              </div>
            </div>
          </template>
        </Menu>

        <!-- status label -->
        <div class="cursor-pointer text-center mx-auto text-xs mt-1">
          {{ documentor.status.name }}
        </div>

      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import type {Documentor} from "@/api/query/interface/model";
import {computed, onMounted, ref} from "vue";
import {useModelStore} from "@/stores/model";
import {changeDocumentorStatus} from "@/api/command/model/changeDocumentorStatus";
import {useToast} from "primevue/usetoast";
import {Timeago} from 'vue2-timeago';
import EmailLabel from "@/components/common/emailLabel.vue";
import {useSchemaStore} from "@/stores/schema";

// -- props & variables
const props = defineProps<{
  documentor: Documentor,
  type: string,
  id: number,
  artefactId: string,
  isDocumentation: boolean
}>();
const menu = ref();
const isSaving = ref<boolean>(false);
const modelStore = useModelStore();
const schemaStore = useSchemaStore();
const toaster = useToast();
const emit = defineEmits(["open"]);

onMounted((): void => {
  void loadDocumentor();
});

function open() {
  emit("open");
}

const toggle = (event: any) => {
  menu.value.toggle(event);
};

async function loadDocumentor() {
  await modelStore.loadDocumentor(props.documentor.id, props.type, props.id, false, true);
}

async function changeStatus(statusId: number) {
  isSaving.value = true;
  let command = {id: modelStore.documentor?.id ?? 0, statusId: statusId};
  let result = await changeDocumentorStatus(command);
  await loadDocumentor();
  await modelStore.reLoadProject();
  if (props.isDocumentation) {
    await schemaStore.reloadDocumentation(modelStore.projectId);
  } else {
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
  }
  isSaving.value = false;
  toaster.add({severity: "success", summary: "Status changed", detail: "", life: modelStore.toastLifeTime});
}

const availableStatus = computed(() => {
  let status = [];
  if (modelStore.project && modelStore.documentor) {
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