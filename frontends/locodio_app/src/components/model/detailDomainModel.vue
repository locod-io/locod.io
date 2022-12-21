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
          <div class="basis-1/4" v-if="!modelStore.isDomainModelFinal">
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
                    v-if="!modelStore.domainModelReloading"
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
            <div v-if="modelStore.domainModel" class="mt-2">
              <status-badge class="mt-1 mr-1"
                            @open="openDocumentor(modelStore.domainModel.documentor.id,modelStore.domainModel.id)"
                            :id="modelStore.domainModel.id"
                            type="domain-model"
                            :is-documentation="false"
                            :documentor="modelStore.domainModel.documentor"/>
            </div>
          </div>
          <div class="basis-1/4 text-right" v-if="!modelStore.isDomainModelFinal">
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

        <!-- form -------------------------------------------------------------------------------------------------- -->
        <DetailWrapper :estate-height="273">
          <div class="p-inputtext-sm">

            <div class="flex flex-row" v-on:keyup.enter="change">
              <div class="basis-2/12 text-right">
                <!-- name -->
                <div class="mt-1"><label class="text-sm font-bold">Name *</label></div>
              </div>
              <div class="basis-4/12 ml-2">
                <div>
                      <span class="p-input-icon-right w-full">
                        <InputText class="w-full p-inputtext-sm"
                                   :readonly="modelStore.isDomainModelFinal"
                                   v-model="command.name"
                                   placeholder="Enter a name"></InputText>
                        <i v-if="!vChangeDomainModel$.name.$invalid" class="pi pi-check text-green-600"/>
                        <i v-if="vChangeDomainModel$.name.$invalid" class="pi pi-times text-red-600"/>
                      </span>
                </div>
              </div>

              <div class="basis-1/12 text-right">
                <!-- module -->
                <div class="mt-1"><label class="text-sm font-bold">Module *</label></div>
              </div>
              <div class="basis-4/12">
                <div class="flex ml-2">
                  <Dropdown optionLabel="name"
                            v-model="command.moduleId"
                            option-value="id"
                            :disabled="modelStore.isDomainModelFinal"
                            :options="modelStore.project.modules"
                            placeholder="Select a module"
                            class="w-full p-dropdown-sm"/>
                  <div class="mx-2 mt-1.5" v-if="!modelStore.isDomainModelFinal">
                    <i v-if="!vChangeDomainModel$.moduleId.$invalid" class="pi pi-check-circle text-green-600"/>
                    <i v-if="vChangeDomainModel$.moduleId.$invalid" class="pi pi-times-circle text-red-600"/>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex flex-row mt-2" v-on:keyup.enter="change">
              <div class="basis-2/12 text-right">
                <!-- name -->
                <div class="mt-1"><label class="text-sm">Namespace *</label></div>
              </div>
              <div class="basis-9/12 ml-2">
                <div>
                      <span class="p-input-icon-right w-full">
                        <InputText class="w-full p-inputtext-sm"
                                   :readonly="modelStore.isDomainModelFinal"
                                   v-model="command.namespace"
                                   placeholder="Enter a namespace"></InputText>
                        <i v-if="!vChangeDomainModel$.namespace.$invalid" class="pi pi-check text-green-600"/>
                        <i v-if="vChangeDomainModel$.namespace.$invalid" class="pi pi-times text-red-600"/>
                      </span>
                </div>
              </div>
              <div class="basis-1/12">
                <div class="mt-1 ml-2" v-if="!modelStore.isDomainModelFinal">
                  <copy-button @click="takeNamespaceFromProject"/>
                </div>
              </div>
            </div>

            <div class="flex flex-row mt-2" v-on:keyup.enter="change">
              <div class="basis-2/12 text-right">
                <!-- name -->
                <div class="mt-1"><label class="text-sm">Repository *</label></div>
              </div>
              <div class="basis-9/12 ml-2">
                <div>
                      <span class="p-input-icon-right w-full">
                        <InputText class="w-full p-inputtext-sm"
                                   v-model="command.repository"
                                   :readonly="modelStore.isDomainModelFinal"
                                   placeholder="Enter a repository"></InputText>
                        <i v-if="!vChangeDomainModel$.repository.$invalid" class="pi pi-check text-green-600"/>
                        <i v-if="vChangeDomainModel$.repository.$invalid" class="pi pi-times text-red-600"/>
                      </span>
                </div>
              </div>
              <div class="basis-1/12">
                <div class="mt-1 ml-2" v-if="!modelStore.isDomainModelFinal">
                  <copy-button @click="takeRepositoryFromProject"/>
                </div>
              </div>
            </div>

            <!-- list fields ----------------------------------------------------------------------------------- -->
            <Fieldset legend="Attributes" class="mt-2">
              <div class="text-sm">
                <table>
                  <thead>
                  <tr class="border-b-[1px]">
                    <th width="5%">&nbsp;</th>
                    <th width="22%">Name</th>
                    <th width="20%">Type</th>
                    <th width="10%">Length</th>
                    <th width="5%">Identifier</th>
                    <th width="5%">Required</th>
                    <th width="5%">Unique</th>
                    <th width="5%">Make?</th>
                    <th width="5%">Change?</th>
                    <th width="8%">&nbsp;</th>
                  </tr>
                  </thead>
                  <Draggable
                      v-model="modelStore.domainModel.attributes"
                      tag="tbody"
                      item-key="name"
                      handle=".handle"
                      @end="saveFieldOrder"
                      ghost-class="ghost">
                    <template #item="{ element }">
                      <edit-attribute :item="element"/>
                    </template>
                  </Draggable>
                  <add-attribute v-if="!modelStore.isDomainModelFinal"/>
                </table>
              </div>
            </Fieldset>

            <!-- list relations -------------------------------------------------------------------------------- -->
            <Fieldset legend="Associations" class="mt-2">
              <div class="text-sm">
                <table>
                  <thead>
                  <tr class="border-b-[1px]">
                    <th width="7%">&nbsp;</th>
                    <th width="30%" colspan="2">Type <span class="text-xs font-normal">(Required/Make/Change)</span>
                    </th>
                    <th width="10%">Target</th>
                    <th width="20%">Mapped/Inversed By</th>
                    <th width="10%">Fetch</th>
                    <th width="10%">OrderBy</th>
                    <th width="10%">Direction</th>
                    <th width="10%">&nbsp;</th>
                  </tr>
                  </thead>
                  <Draggable
                      v-model="modelStore.domainModel.associations"
                      tag="tbody"
                      item-key="type"
                      handle=".handle"
                      @end="saveRelationOrder"
                      ghost-class="ghost">
                    <template #item="{ element }">
                      <edit-association :association="element"/>
                    </template>
                  </Draggable>
                  <add-association v-if="!modelStore.isDomainModelFinal"/>
                </table>
              </div>
            </Fieldset>

          </div>
        </DetailWrapper>

        <!-- render this template -------------------------------------------------------------------------- -->
        <div class="p-inputtext-sm">
          <generate-block type="domain_model" :subject-id="modelStore.domainModelSelectedId"/>
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
import {useModelStore} from "@/stores/model";
import {useToast} from "primevue/usetoast";
import {minValue, required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import type {
  ChangeDomainModelCommand,
  OrderAssociationCommand,
  OrderAttributeCommand
} from "@/api/command/interface/domainModelCommands";
import GenerateBlock from "@/components/model/generateBlock.vue";
import {changeDomainModel} from "@/api/command/model/changeDomainModel";
import {orderAttributes} from "@/api/command/model/orderAttributes";
import {orderAssociations} from "@/api/command/model/orderAssociations";
import {deleteDomainModel} from "@/api/command/model/deleteDomainModel";
import CopyButton from "@/components/common/copyButton.vue";
import EditAssociation from "@/components/model/editAssociation.vue";
import AddAssociation from "@/components/model/addAssociation.vue";
import EditAttribute from "@/components/model/editAttribute.vue";
import AddAttribute from "@/components/model/addAttribute.vue";
import StatusBadge from "@/components/common/statusBadge.vue";

const modelStore = useModelStore();
const toaster = useToast();
const isSaving = ref<boolean>(false);
const dragging = ref<boolean>(false);
const command = ref<ChangeDomainModelCommand>({
  id: modelStore.domainModelSelectedId,
  moduleId: modelStore.domainModel?.module?.id ?? 0,
  name: modelStore.domainModel?.name ?? "",
  namespace: modelStore.domainModel?.namespace ?? "",
  repository: modelStore.domainModel?.repository ?? ""
});

// -- mounted

onMounted((): void => {
  vChangeDomainModel$.value.$touch();
});

// -- validation

const rules = {
  name: {required},
  namespace: {required},
  repository: {required},
  moduleId: {minValueValue: minValue(1)},
};
const vChangeDomainModel$ = useVuelidate(rules, command);

// -- change

async function change() {
  vChangeDomainModel$.value.$touch();
  if (!vChangeDomainModel$.value.$invalid && !modelStore.isDomainModelFinal) {
    isSaving.value = true;
    await changeDomainModel(command.value);
    toaster.add({
      severity: "success",
      summary: "Model saved",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadProject();
    await modelStore.reLoadDomainModel();
    isSaving.value = false;
  }
}

async function reload() {
  await modelStore.reLoadDomainModel();
  command.value.name = modelStore.domainModel?.name ?? "";
  command.value.namespace = modelStore.domainModel?.namespace ?? "";
  command.value.repository = modelStore.domainModel?.repository ?? "";
}

// -- save order of the fields

const sequenceFields = computed((): OrderAttributeCommand => {
  if (modelStore.domainModel) {
    let sequence = [];
    for (let i = 0; i < modelStore.domainModel.attributes.length; i++) {
      sequence.push(modelStore.domainModel.attributes[i].id);
    }
    return {sequence: sequence};
  } else {
    return {sequence: []};
  }
});

async function saveFieldOrder() {
  await orderAttributes(sequenceFields.value);
  toaster.add({
    severity: "success",
    summary: "Attributes order saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadDomainModel();
}

// -- save order of the relations

const sequenceRelations = computed((): OrderAssociationCommand => {
  if (modelStore.domainModel) {
    let sequence = [];
    for (let i = 0; i < modelStore.domainModel.associations.length; i++) {
      sequence.push(modelStore.domainModel.associations[i].id);
    }
    return {sequence: sequence};
  } else {
    return {sequence: []};
  }
});

async function saveRelationOrder() {
  await orderAssociations(sequenceRelations.value);
  toaster.add({
    severity: "success",
    summary: "Relation order saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadDomainModel();
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
  await deleteDomainModel({id: modelStore.domainModelSelectedId});
  toaster.add({
    severity: "success",
    summary: "Model deleted.",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadProject();
  modelStore.domainModelSelectedId = 0;
  modelStore.domainModel = undefined;
  isSaving.value = false;
}

function takeNamespaceFromProject() {
  if (modelStore.domainModel?.project.modelSettings && modelStore.domainModel.module) {
    command.value.namespace = modelStore.domainModel.project.modelSettings.domainLayer
        + '\\Model\\' + modelStore.domainModel.module.namespace;
  }
}

function takeRepositoryFromProject() {
  if (modelStore.domainModel?.project.modelSettings && modelStore.domainModel.module) {
    command.value.repository = modelStore.domainModel.project.modelSettings.infrastructureLayer
        + '\\Database\\' + modelStore.domainModel.module.namespace + '\\' + command.value.name + 'Repository';
  }
}

// -- documentor

function openDocumentor(id: number, subjectId: number) {
  modelStore.loadDocumentor(id, 'domain-model', subjectId);
}


</script>