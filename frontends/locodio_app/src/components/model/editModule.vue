<template>
  <tr id="Component_Edit_Module"
      class="border-b-[1px]"
      v-on:keyup.enter="save" v-on:keyup.esc="viewForm">

    <!-- view mode ------------------------------------------------- -->
    <td v-if="isView">
      <div class="flex mt-1 mb-1 mr-2">
        <div class="mt-1 text-gray-200 hover:text-green-600 cursor-move mr-2">
          <i class="pi pi-bars handle"></i>
        </div>
        <div v-if="!item.documentor.status.isFinal">
          <edit-button @click="editForm"/>
        </div>
      </div>
    </td>
    <td v-if="isView"><strong>{{ item.name }}</strong></td>
    <td v-if="isView">{{ item.namespace }}</td>
    <td v-if="isView">
      <!-- documentor: identifier + status -->
      <status-badge-small
          :id="'M-'+item.id"
          @click="openDocumentor(item.documentor.id,item.id)"
          :status="item.documentor.status" class="cursor-pointer"/>
    </td>
    <td v-if="isView" align="right">
      <div v-if="item.usages == 0 && !item.documentor.status.isFinal" class="ml-1">
        <delete-button @deleted="deleteItem"></delete-button>
      </div>
    </td>

    <!-- edit mode ------------------------------------------------- -->

    <td v-if="!isView" colspan="2">
      <span class="p-input-icon-right w-full">
        <InputText class="w-full p-inputtext-sm"
                   placeholder="name"
                   v-model="commandEdit.name"/>
        <i v-if="!vModuleEdit$.name.$invalid" class="pi pi-check text-green-600"/>
        <i v-if="vModuleEdit$.name.$invalid" class="pi pi-times text-red-600"/>
      </span>
    </td>
    <td v-if="!isView">
      <div class="pl-2">
        <span class="p-input-icon-right w-full">
          <InputText class="w-full p-inputtext-sm"
                     placeholder="namespace"
                     v-model="commandEdit.namespace"/>
          <i v-if="!vModuleEdit$.namespace.$invalid" class="pi pi-check text-green-600"/>
          <i v-if="vModuleEdit$.namespace.$invalid" class="pi pi-times text-red-600"/>
        </span>
      </div>
    </td>
    <td v-if="!isView" colspan="2">
      <div class="flex mt-2 mb-2">
        <div class="mr-2 ml-2">
          <save-button @click="save"></save-button>
        </div>
        <div class="mt-0.5">
          <close-button @click="viewForm"></close-button>
        </div>
      </div>
    </td>

  </tr>
</template>

<script setup lang="ts">
import type {Module} from "@/api/query/interface/model";
import {onMounted, ref} from "vue";
import {useModelStore} from "@/stores/model";
import {useToast} from "primevue/usetoast";
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import type {ChangeModuleCommand} from "@/api/command/interface/modelConfiguration";
import EditButton from "@/components/common/editButton.vue";
import {changeModule} from "@/api/command/model/changeModule";
import SaveButton from "@/components/common/saveButton.vue";
import CloseButton from "@/components/common/closeButton.vue";
import StatusBadgeSmall from "@/components/common/statusBadgeSmall.vue";
import DeleteButton from "@/components/common/deleteButton.vue";
import {deleteModule} from "@/api/command/model/deleteModule";

// -- props
const props = defineProps<{
  item: Module,
}>();

const isView = ref<boolean>(true);
const isSaving = ref<boolean>(false);
const modelStore = useModelStore();
const toaster = useToast();
const commandEdit = ref<ChangeModuleCommand>({
  id: props.item.id,
  name: props.item.name,
  namespace: props.item.namespace,
});

// -- mounted

onMounted((): void => {
  vModuleEdit$.value.$touch();
});

// -- documentor

function openDocumentor(id: number, subjectId: number) {
  modelStore.loadDocumentor(id, 'module', subjectId);
}

// -- validation

const rules = {
  name: {required},
  namespace: {required},
};
const vModuleEdit$ = useVuelidate(rules, commandEdit);

// -- functions

function editForm() {
  isView.value = false;
  commandEdit.value.id = props.item.id;
  commandEdit.value.name = props.item.name;
  commandEdit.value.namespace = props.item.namespace;
}

function viewForm() {
  isView.value = true;
}

async function save() {
  vModuleEdit$.value.$touch();
  if (!vModuleEdit$.value.$invalid) {
    isSaving.value = true;
    await changeModule(commandEdit.value);
    toaster.add({
      severity: "success",
      summary: "Module changed",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadProject();
    isView.value = true;
    isSaving.value = false;
  }
}

async function deleteItem() {
  isSaving.value = true;
  await deleteModule({id: props.item.id});
  toaster.add({
    severity: "success",
    summary: "Module deleted.",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadProject();
  isSaving.value = false;
}

</script>
