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
  <div class="detailWrapper">
    <div class="detail">
      <div>

        <!-- toolbar --------------------------------------------------------------------------------------------- -->
        <div class="flex flex-row p-2">
          <div class="basis-1/4" v-if="!modelStore.isEnumFinal">
            <Button
                v-if="!isSaving"
                @click="change"
                icon="pi pi-save"
                label="SAVE"
                class="p-button-success p-button-sm w-full"/>
            <Button
                v-else
                disabled
                icon="pi pi-spin pi-spinner"
                label="SAVE"
                class="p-button-success p-button-sm w-full"/>
          </div>
          <div class="basis-1/12 pl-2">
            <div class="flex">
              <div>
                <Button
                    @click="reload"
                    v-if="!modelStore.enumReloading"
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
          <!--  documentor -->
          <div class="basis-7/12">
            <div v-if="modelStore.enum" class="mt-2">
              <status-badge class="mt-1 mr-1"
                            :is-documentation="false"
                            @open="openDocumentor(modelStore.enum.documentor.id,modelStore.enum.id)"
                            :id="modelStore.enum.id"
                            type="enum"
                            :documentor="modelStore.enum.documentor"/>
            </div>
          </div>
          <div class="basis-1/4 text-right" v-if="!modelStore.isEnumFinal">
            <Button v-if="!isSaving"
                    icon="pi pi-trash"
                    class="p-button-sm"
                    @click="deleteAction($event)"/>
            <Button v-else
                    icon="pi pi-spin pi-spinner"
                    class="p-button-sm"/>
            <ConfirmPopup/>
          </div>
        </div>

        <!-- form ------------------------------------------------------------------------------------------------ -->
        <DetailWrapper :estate-height="273">
          <div class="p-2 p-inputtext-sm">
            <div class="flex flex-row" v-on:keyup.enter="change">
              <div class="basis-2/4">
                <!-- name -->
                <div><label class="text-sm font-bold">Name *</label></div>
                <div>
                      <span class="p-input-icon-right w-full">
                        <InputText class="w-full p-inputtext-sm"
                                   :readonly="modelStore.isEnumFinal"
                                   placeholder="Name"
                                   v-model="command.name"/>
                        <i v-if="!v$.name.$invalid" class="pi pi-check text-green-600"/>
                        <i v-if="v$.name.$invalid" class="pi pi-times text-red-600"/>
                      </span>
                </div>
              </div>
              <div class="basis-2/4 ml-2">
                <!-- domain model -->
                <div><label class="text-sm">Model *</label></div>
                <div>
                  <Dropdown optionLabel="name"
                            v-model="command.domainModelId"
                            :disabled="modelStore.isEnumFinal"
                            option-value="id"
                            :options="modelStore.project.domainModels"
                            placeholder="Select a domain model"
                            class="w-full p-dropdown-sm"/>
                </div>
              </div>
            </div>

            <div class="flex flex-row mt-2" v-on:keyup.enter="change">
              <div class="basis-11/12">
                <!-- name -->
                <div><label class="text-sm">Namespace *</label></div>
                <div>
                      <span class="p-input-icon-right w-full">
                        <InputText class="w-full p-inputtext-sm"
                                   :readonly="modelStore.isEnumFinal"
                                   placeholder="namespace"
                                   v-model="command.namespace"/>
                        <i v-if="!v$.namespace.$invalid" class="pi pi-check text-green-600"/>
                        <i v-if="v$.namespace.$invalid" class="pi pi-times text-red-600"/>
                      </span>
                </div>
              </div>
              <div class="basis-1/12">
                <div class="mt-7 ml-2">
                  <copy-button @click="copyNamespace" v-if="!modelStore.isEnumFinal"/>
                </div>
              </div>
            </div>

            <!-- list options ---------------------------------------------------------------------------------- -->
            <Fieldset legend="Options" class="mt-4">
              <div class="text-sm">
                <table>
                  <thead>
                  <tr class="border-b-[1px]">
                    <th width="5%">&nbsp;</th>
                    <th width="42%">Name</th>
                    <th width="42%">Value</th>
                    <th width="15%">&nbsp;</th>
                  </tr>
                  </thead>
                  <Draggable
                      v-model="modelStore.enum.options"
                      tag="tbody"
                      item-key="name"
                      handle=".handle"
                      @end="saveFieldOrder"
                      ghost-class="ghost">
                    <template #item="{ element }">
                      <edit-option :item="element"/>
                    </template>
                  </Draggable>
                  <add-option v-if="!modelStore.isEnumFinal"/>
                </table>
              </div>
            </Fieldset>
          </div>
        </DetailWrapper>

        <!-- render this template -------------------------------------------------------------------------- -->
        <div class="p-inputtext-sm">
          <generate-block type="enum" :subject-id="modelStore.enumSelectedId"/>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {computed, onMounted, ref} from "vue";
import DetailWrapper from "@/components/wrapper/detailWrapper.vue";
import {useConfirm} from "primevue/useconfirm";
import Draggable from "vuedraggable";
import CopyButton from "@/components/common/copyButton.vue";
import AddOption from "@/components/model/addOption.vue";
import EditOption from "@/components/model/editOption.vue";
import {useModelStore} from "@/stores/model";
import type {ChangeEnumCommand, OrderEnumOptionCommand} from "@/api/command/interface/enumCommands";
import {minValue, required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {changeEnum} from "@/api/command/model/changeEnum";
import {useToast} from "primevue/usetoast";
import {orderEnumOption} from "@/api/command/model/orderEnumOption";
import GenerateBlock from "@/components/model/generateBlock.vue";
import {deleteEnum} from "@/api/command/model/deleteEnum";
import StatusBadge from "@/components/common/statusBadge.vue";

const modelStore = useModelStore();
const toaster = useToast();
const isSaving = ref<boolean>(false);
const dragging = ref<boolean>(false);
const command = ref<ChangeEnumCommand>({
  id: modelStore.enumSelectedId,
  domainModelId: modelStore.enum?.domainModel.id ?? 0,
  name: modelStore.enum?.name ?? "",
  namespace: modelStore.enum?.namespace ?? ""
});

// -- mounted

onMounted((): void => {
  v$.value.$touch();
});

// -- validation

const rules = {
  name: {required},
  namespace: {required},
  domainModelId: {minValueValue: minValue(1)},
};
const v$ = useVuelidate(rules, command);

// -- copy namespace from model

function copyNamespace() {
  let _namespace = '';
  if(modelStore.enum) {
    _namespace = modelStore.enum.domainModel.namespace;
  }
  command.value.namespace = _namespace;
}

// -- change

async function change() {
  v$.value.$touch();
  if (!v$.value.$invalid && !modelStore.isEnumFinal) {
    isSaving.value = true;
    await changeEnum(command.value);
    toaster.add({
      severity: "success",
      summary: "Enum saved",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadProject();
    await modelStore.reLoadEnum();
    isSaving.value = false;
  }
}

async function reload() {
  await modelStore.reLoadEnum();
  command.value.domainModelId = modelStore.enum?.domainModel.id ?? 0;
  command.value.name = modelStore.enum?.name ?? "";
  command.value.namespace = modelStore.enum?.namespace ?? "";
}

// -- save order of the options

const sequence = computed((): OrderEnumOptionCommand => {
  if (modelStore.enum) {
    let sequence = [];
    for (let i = 0; i < modelStore.enum.options.length; i++) {
      sequence.push(modelStore.enum.options[i].id);
    }
    return {sequence: sequence};
  } else {
    return {sequence: []};
  }
});

async function saveFieldOrder() {
  await orderEnumOption(sequence.value);
  toaster.add({
    severity: "success",
    summary: "Option order saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadEnum();
}

// -- delete confirmation

const confirm = useConfirm();

function deleteAction(event: MouseEvent) {
  const target = event.currentTarget;
  if (target instanceof HTMLElement) {
    confirm.require({
      target: target,
      message: "Are you sure ?",
      icon: "pi pi-exclamation-triangle",
      acceptLabel: "Yes",
      rejectLabel: "No",
      acceptIcon: "pi pi-check",
      rejectIcon: "pi pi-times",
      accept: () => {
        void deleteDetail();
      },
      reject: () => {
        // callback to execute when user rejects the action
      },
    });
  }
}

async function deleteDetail() {
  isSaving.value = true;
  await deleteEnum({id: modelStore.enumSelectedId});
  toaster.add({
    severity: "success",
    summary: "Enum deleted.",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadProject();
  modelStore.enumSelectedId = 0;
  modelStore.enum = undefined;
  isSaving.value = false;
}

// -- documentor

function openDocumentor(id: number, subjectId: number) {
  modelStore.loadDocumentor(id, 'enum', subjectId);
}

</script>