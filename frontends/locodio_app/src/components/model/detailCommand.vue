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

        <!-- toolbar ------------------------------------------------------------ -->
        <div class="flex flex-row p-2">
          <div class="basis-1/4" v-if="!modelStore.isCommandFinal">
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
                    v-if="!modelStore.commandReloading"
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
            <div v-if="modelStore.command" class="mt-2">
              <status-badge class="mt-1 mr-1"
                            :is-documentation="false"
                            @open="openDocumentor(modelStore.command.documentor.id,modelStore.command.id)"
                            :id="modelStore.command.id"
                            type="command"
                            :documentor="modelStore.command.documentor"/>
            </div>
          </div>
          <div class="basis-1/4 text-right" v-if="!modelStore.isCommandFinal">
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
                                   placeholder="Name"
                                   :readonly="modelStore.isCommandFinal"
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
                            option-value="id"
                            :disabled="modelStore.isCommandFinal"
                            :options="modelStore.project.domainModels"
                            placeholder="Select a domain model"
                            class="w-full p-dropdown-sm"/>
                </div>
              </div>
            </div>

            <div class="flex flex-row mt-2" v-on:keyup.enter="change">
              <div class="basis-11/12">
                <!-- namespace -->
                <div><label class="text-sm">Namespace *</label></div>
                <div>
                      <span class="p-input-icon-right w-full">
                        <InputText class="w-full p-inputtext-sm"
                                   placeholder="namespace"
                                   :readonly="modelStore.isCommandFinal"
                                   v-model="command.namespace"/>
                        <i v-if="!v$.namespace.$invalid" class="pi pi-check text-green-600"/>
                        <i v-if="v$.namespace.$invalid" class="pi pi-times text-red-600"/>
                      </span>
                </div>
              </div>
              <div class="basis-1/12">
                <div class="mt-7 ml-2" v-if="!modelStore.isCommandFinal">
                  <copy-button @click="takeNamespaceFromProject"/>
                </div>
              </div>
            </div>

            <!-- mappings ------------------------------------------------------- -->
            <Fieldset legend="Mapping" class="mt-4">
              <table class="w-full">
                <thead>
                <tr class="border-b-[1px]">
                  <th width="10%">Mapping</th>
                  <th width="30%">Name</th>
                  <th width="20%">Type</th>
                  <th width="20%">Mapped by</th>
                  <th width="20%">Inversed by</th>
                </tr>
                </thead>
                <tbody>
                <!-- attributes -->
                <tr v-for="attribute in modelStore.command.domainModel.attributes" :key="attribute.id"
                    class="border-b-[1px]">
                  <td>
                    <mapping-selection-box
                        :readonly="modelStore.isCommandFinal"
                        @select="addAttribute(attribute)"
                        @unselect="removeAttribute(attribute)"
                        :selection="isInMapping(attribute.name)"/>
                  </td>
                  <td class="text-sm">
                    <div class="pt-1 pb-1">{{ attribute.name }}</div>
                  </td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <!-- associations -->
                <tr v-for="association in modelStore.command.domainModel.associations" :key="association.id"
                    class="border-b-[1px]">
                  <td>
                    <mapping-selection-box
                        :readonly="modelStore.isCommandFinal"
                        @select="addAssociation(association)"
                        @unselect="removeAssociation(association)"
                        :selection="isAssociationInMapping(association)"/>
                  </td>
                  <td class="text-sm">{{ association.targetDomainModel.name }}</td>
                  <td class="text-xs">{{ association.type }}</td>
                  <td class="text-xs">{{ association.mappedBy }}</td>
                  <td class="text-xs">{{ association.inversedBy }}</td>
                </tr>
                </tbody>
              </table>
            </Fieldset>

          </div>
        </DetailWrapper>

        <!-- render this template -------------------------------------------------------------------------- -->
        <div class="p-inputtext-sm">
          <generate-block type="command" :subject-id="modelStore.commandSelectedId"/>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import DetailWrapper from "@/components/wrapper/detailWrapper.vue";
import {useConfirm} from "primevue/useconfirm";
import {useModelStore} from "@/stores/model";
import {minValue, required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {useToast} from "primevue/usetoast";
import GenerateBlock from "@/components/model/generateBlock.vue";
import type {ChangeCommandCommand} from "@/api/command/interface/commandCommands";
import {changeCommand} from "@/api/command/model/changeCommand";
import MappingSelectionBox from "@/components/model/mappingSelectionBox.vue";
import type {Association, Attribute} from "@/api/query/interface/model";
import {deleteCommand} from "@/api/command/model/deleteCommand";
import CopyButton from "@/components/common/copyButton.vue";
import StatusBadge from "@/components/common/statusBadge.vue";

const modelStore = useModelStore();
const toaster = useToast();
const isSaving = ref<boolean>(false);
const dragging = ref<boolean>(false);
const command = ref<ChangeCommandCommand>({
  id: modelStore.commandSelectedId,
  domainModelId: modelStore.command?.domainModel.id ?? 0,
  name: modelStore.command?.name ?? "",
  namespace: modelStore.command?.namespace ?? "",
  mapping: modelStore.command?.mapping ?? []
});

// -- mounted

onMounted((): void => {
  v$.value.$touch();
});

// -- mapping functions

function isInMapping(name: string): boolean {
  for (let i = command.value.mapping.length - 1; i >= 0; i--) if (command.value.mapping[i].name === name) return true
  return false
}

function isAssociationInMapping(association: Association): boolean {
  let _mappings = determineMappingName(association);
  if (_mappings.length != 0) {
    for (let i = 0; i < _mappings.length; i++) {
      let _mapping = _mappings[i]
      for (let i = command.value.mapping.length - 1; i >= 0; i--) {
        if (command.value.mapping[i].name === _mapping.name) return true
      }
    }
  }
  return false
}

function addAttribute(attribute: Attribute) {
  addToMapping(attribute.name, attribute.type)
}

function removeAttribute(attribute: Attribute) {
  removeFromMapping(attribute.name)
}

function addToMapping(name: string, type: string): void {
  let _mapping = {name: name, type: type};
  command.value.mapping.push(_mapping);
}

function removeFromMapping(name: string): void {
  for (let i = command.value.mapping.length - 1; i >= 0; i--) {
    if (command.value.mapping[i].name === name) command.value.mapping.splice(i, 1);
  }
}

function addAssociation(association: Association) {
  let _mappings = determineMappingName(association);
  if (_mappings.length != 0) {
    for (let i = 0; i < _mappings.length; i++) {
      let _mapping = _mappings[i];
      addToMapping(_mapping.name, _mapping.type);
    }
  }
}

function removeAssociation(association: Association) {
  let _mappings = determineMappingName(association);
  if (_mappings.length != 0) {
    for (let i = 0; i < _mappings.length; i++) {
      let _mapping = _mappings[i];
      removeFromMapping(_mapping.name);
    }
  }
}

function determineMappingName(association: Association) {
  let _mappings = []
  let _mappingOne = new Object();
  let _mappingTwo = new Object();
  switch (association.type) {
    case 'Many-To-One_Unidirectional':
      _mappingOne.name = association.mappedBy + 'Id';
      _mappingOne.type = 'integer';
      _mappings.push(_mappingOne);
      break;
    case 'One-To-One_Unidirectional':
      _mappingOne.name = association.mappedBy + 'Id';
      _mappingOne.type = 'integer';
      _mappings.push(_mappingOne);
      break;
    case 'One-To-One_Bidirectional':
      _mappingOne.name = association.mappedBy + 'Id';
      _mappingOne.type = 'integer';
      _mappings.push(_mappingOne);
      break;
    case 'One-To-One_Self-referencing':
      _mappingOne.name = association.mappedBy + 'Id';
      _mappingOne.type = 'integer';
      _mappings.push(_mappingOne);
      break;
    case 'One-To-Many_Self-referencing':
      _mappingOne.name = association.mappedBy + 'Id';
      _mappingOne.type = 'integer';
      _mappings.push(_mappingOne);
      break;
  }
  return _mappings;
}

// -- validation

const rules = {
  name: {required},
  namespace: {required},
  domainModelId: {minValueValue: minValue(1)},
};
const v$ = useVuelidate(rules, command);

// -- change

async function change() {
  v$.value.$touch();
  if (!v$.value.$invalid && !modelStore.isCommandFinal) {
    isSaving.value = true;
    await changeCommand(command.value);
    toaster.add({
      severity: "success",
      summary: "Command saved",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadProject();
    await modelStore.reLoadCommand();
    isSaving.value = false;
  }
}

async function reload() {
  await modelStore.reLoadCommand();
  command.value.domainModelId = modelStore.command?.domainModel.id ?? 0;
  command.value.name = modelStore.command?.name ?? "";
  command.value.namespace = modelStore.command?.namespace ?? "";
  command.value.mapping = modelStore.command?.mapping ?? [];
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
  await deleteCommand({id: modelStore.commandSelectedId});
  toaster.add({
    severity: "success",
    summary: "Command deleted.",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadProject();
  modelStore.commandSelectedId = 0;
  modelStore.command = undefined;
  isSaving.value = false;
}

function takeNamespaceFromProject() {
  if (modelStore.command
      && modelStore.command.project.modelSettings
      && modelStore.command.domainModel.module
  ) {
    command.value.namespace = modelStore.command.project.modelSettings.applicationLayer
        + '\\Command\\' + modelStore.command.domainModel.module.name + '\\' + command.value.name;
  }
}

// -- documentor

function openDocumentor(id: number, subjectId: number) {
  modelStore.loadDocumentor(id, 'command', subjectId);
}

</script>