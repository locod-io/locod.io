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
        <div class="flex gap-2 border-b-[1px] border-gray-300 dark:border-gray-600 h-12">
          <div class="flex-none my-2 pl-2" v-if="!modelStore.isQueryFinal">
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
                    v-if="!modelStore.queryReloading"
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
            <div v-if="modelStore.query">
              <status-badge class="mt-0.5"
                            :is-documentation="false"
                            :artefact-id="modelStore.query.artefactId"
                            @open="openDocumentor(modelStore.query.documentor.id,modelStore.query.id)"
                            :id="modelStore.query.id"
                            type="query"
                            :documentor="modelStore.query.documentor"/>
            </div>
          </div>
          <div class="flex-none">
            <extension-button
                v-if="modelStore.query"
                :id="modelStore.query.id"
                type="query"/>
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
                                   placeholder="Name"
                                   :readonly="modelStore.isQueryFinal"
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
                            :disabled="modelStore.isQueryFinal"
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
                                   placeholder="namespace"
                                   :readonly="modelStore.isQueryFinal"
                                   v-model="command.namespace"/>
                        <i v-if="!v$.namespace.$invalid" class="pi pi-check text-green-600"/>
                        <i v-if="v$.namespace.$invalid" class="pi pi-times text-red-600"/>
                      </span>
                </div>
              </div>
              <div class="basis-1/12">
                <div class="mt-7 ml-2">
                  <copy-button @click="takeNamespaceFromProject" v-if="!modelStore.isQueryFinal"/>
                </div>
              </div>
            </div>

          </div>

          <!-- mappings ------------------------------------- -->
          <div class="flex border-b-[1px] border-gray-300 dark:border-gray-600 h-12 p-4 font-bold text-sm">
            Mappings
          </div>
          <table class="w-full" cellpadding="2">
            <thead>
            <tr class="border-b-[1px] border-gray-300 dark:border-gray-600 h-8">
              <th width="10%" class="pl-2">&nbsp;</th>
              <th width="30%">Name</th>
              <th width="20%">Type</th>
              <th width="20%">Mapped by</th>
              <th width="20%">Inversed by</th>
            </tr>
            </thead>
            <tbody>
            <!-- attributes -->
            <tr v-for="attribute in modelStore.query.domainModel.attributes" :key="attribute.id"
                class="border-b-[1px] border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
              <td class="pl-4">
                <mapping-selection-box
                    :readonly="modelStore.isQueryFinal"
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
            <tr v-for="association in modelStore.query.domainModel.associations" :key="association.id"
                class="border-b-[1px] border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
              <td class="pl-4">
                <mapping-selection-box
                    :readonly="modelStore.isQueryFinal"
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

        </DetailWrapper>

        <!-- render this template -------------------------------------------------------------------------- -->
        <div>
          <generate-block type="query" :subject-id="modelStore.querySelectedId"/>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import DetailWrapper from "@/components/wrapper/detailWrapper.vue";
import {useModelStore} from "@/stores/model";
import {minValue, required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {useToast} from "primevue/usetoast";
import GenerateBlock from "@/components/model/generateBlock.vue";
import type {ChangeQueryCommand} from "@/api/command/interface/queryCommands";
import {changeQuery} from "@/api/command/model/changeQuery";
import MappingSelectionBox from "@/components/model/mappingSelectionBox.vue";
import type {Association, Attribute} from "@/api/query/interface/model";
import CopyButton from "@/components/common/copyButton.vue";
import StatusBadge from "@/components/common/statusBadge.vue";
import ExtensionButton from "@/_locodio/extensionButton.vue";

const modelStore = useModelStore();
const toaster = useToast();
const isSaving = ref<boolean>(false);
const dragging = ref<boolean>(false);
const command = ref<ChangeQueryCommand>({
  id: modelStore.querySelectedId,
  domainModelId: modelStore.query?.domainModel.id ?? 0,
  name: modelStore.query?.name ?? "",
  namespace: modelStore.query?.namespace ?? "",
  mapping: modelStore.query?.mapping ?? []
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
  let _mappings = [];
  let _mappingOne = new Object();
  let _mappingTwo = new Object();
  switch (association.type) {
    case 'Many-To-One_Unidirectional':
      _mappingOne.name = association.mappedBy
      _mappingOne.type = association.targetDomainModel.name + 'ReadModel'
      _mappings.push(_mappingOne)
      break
    case 'One-To-One_Unidirectional':
      _mappingOne.name = association.mappedBy
      _mappingOne.type = association.targetDomainModel.name + 'ReadModel'
      _mappings.push(_mappingOne)
      break
    case 'One-To-One_Bidirectional':
      _mappingOne.name = association.mappedBy
      _mappingOne.type = association.targetDomainModel.name + 'ReadModel'
      _mappings.push(_mappingOne)
      break
    case 'One-To-One_Self-referencing':
      _mappingOne.name = association.mappedBy
      _mappingOne.type = association.targetDomainModel.name + 'ReadModel'
      _mappings.push(_mappingOne)
      break
    case 'One-To-Many_Bidirectional':
      _mappingOne.name = association.targetDomainModel.name.toLowerCase() + 's'
      _mappingOne.type = association.targetDomainModel.name + 'ReadModel[]'
      _mappings.push(_mappingOne)
      break
    case 'One-To-Many_Self-referencing':
      _mappingOne.name = association.mappedBy
      _mappingOne.type = association.targetDomainModel.name + 'ReadModel'
      _mappings.push(_mappingOne)
      _mappingTwo.name = association.inversedBy
      _mappingTwo.type = association.targetDomainModel.name + 'ReadModel[]'
      _mappings.push(_mappingTwo)
      break
    case 'Many-To-Many_Unidirectional':
      _mappingOne.name = association.mappedBy
      _mappingOne.type = association.targetDomainModel.name + 'ReadModel[]'
      _mappings.push(_mappingOne)
      break
    case 'Many-To-Many_Bidirectional':
      _mappingOne.name = association.targetDomainModel.name.toLowerCase() + 's'
      _mappingOne.type = association.targetDomainModel.name + 'ReadModel[]'
      _mappings.push(_mappingOne)
      break
    case 'Many-To-Many_Self-referencing':
      _mappingOne.name = association.mappedBy
      _mappingOne.type = association.targetDomainModel.name + 'ReadModel[]'
      _mappings.push(_mappingOne)
      _mappingTwo.name = association.inversedBy
      _mappingTwo.type = association.targetDomainModel.name + 'ReadModel[]'
      _mappings.push(_mappingTwo)
      break
  }
  return _mappings
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
  if (!v$.value.$invalid && !modelStore.isQueryFinal) {
    isSaving.value = true;
    await changeQuery(command.value);
    toaster.add({
      severity: "success",
      summary: "Query saved",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadProject();
    await modelStore.reLoadQuery();
    isSaving.value = false;
  }
}

async function reload() {
  await modelStore.reLoadQuery();
  command.value.domainModelId = modelStore.query?.domainModel.id ?? 0;
  command.value.name = modelStore.query?.name ?? "";
  command.value.namespace = modelStore.query?.namespace ?? "";
  command.value.mapping = modelStore.query?.mapping ?? [];
}

function takeNamespaceFromProject() {
  if (modelStore.query
      && modelStore.query.project.modelSettings
      && modelStore.query.domainModel.module
  ) {
    command.value.namespace = modelStore.query.project.modelSettings.applicationLayer
        + '\\Query\\' + modelStore.query.domainModel.module.name + '\\' + command.value.name;
  }
}

// -- documentor

function openDocumentor(id: number, subjectId: number) {
  modelStore.loadDocumentor(id, 'query', subjectId);
}

</script>