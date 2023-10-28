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
        <div class="flex gap-2 gap-0 border-b-[1px] border-gray-300 dark:border-gray-600 h-12">
          <div class="flex-none my-2 pl-2" v-if="!modelStore.isEnumFinal">
            <Button
                v-if="!isSaving"
                @click="change"
                icon="pi pi-save"
                label="SAVE"
                class="p-button-success p-button-sm w-36"/>
            <Button
                v-else
                disabled
                icon="pi pi-spin pi-spinner"
                label="SAVE"
                class="p-button-success p-button-sm w-36"/>
          </div>
          <div class="flex-none my-2 pl-2">
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
          <div class="flex-grow">&nbsp;</div>
          <!--  documentor -->
          <div class="flex-none my-2">
            <div v-if="modelStore.enum" class="mt-0.5">
              <status-badge :is-documentation="false"
                            :artefact-id="modelStore.enum.artefactId"
                            @open="openDocumentor(modelStore.enum.documentor.id,modelStore.enum.id)"
                            :id="modelStore.enum.id"
                            type="enum"
                            :documentor="modelStore.enum.documentor"/>
            </div>
          </div>
          <div class="flex-none">
            <extension-button
                v-if="modelStore.enum"
                :id="modelStore.enum.id"
                type="enum"/>
          </div>
        </div>

        <!-- form ------------------------------------------------------------------------------------------------ -->
        <DetailWrapper :estate-height="125">

          <div class="py-2 px-4 border-b-[1px] border-gray-300 dark:border-gray-600 h-36">
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
            <div class="flex flex-row mt-1" v-on:keyup.enter="change">
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

          </div>

          <!-- list options ---------------------------------------------------------------------------------- -->
          <div class="flex border-b-[1px] border-gray-300 dark:border-gray-600 h-12 p-3.5 font-bold text-sm">
            Options
          </div>
          <div class="text-sm border-b-[1px] border-gray-300 dark:border-gray-600">
            <table class="w-full">
              <thead>
              <tr class="border-b-[1px] border-gray-300 dark:border-gray-600 h-8">
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
        </DetailWrapper>

        <!-- render this template -------------------------------------------------------------------------- -->
        <div><generate-block type="enum" :subject-id="modelStore.enumSelectedId"/></div>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {computed, onMounted, ref} from "vue";
import DetailWrapper from "@/components/wrapper/detailWrapper.vue";
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
import StatusBadge from "@/components/common/statusBadge.vue";
import ExtensionButton from "@/_locodio/extensionButton.vue";

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
  if (modelStore.enum) {
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

// -- documentor

function openDocumentor(id: number, subjectId: number) {
  modelStore.loadDocumentor(id, 'enum', subjectId);
}

</script>