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
  <div id="listExample">

    <!-- search & refresh ------------------------------------------------------------------------------------------ -->
    <div class="flex p-2">
      <div>
        <Button
            v-if="!modelStore.isProjectLoading"
            icon="pi pi-refresh"
            class="p-button-sm"
            @click="refreshList"/>
        <Button
            v-else
            class="p-button-sm"
            disabled
            icon="pi pi-spin pi-spinner"/>
      </div>
      <div class="w-full mr-2">
        <div class="p-input-icon-right w-full ml-2">
          <InputText
              type="text"
              class="w-full p-inputtext-sm"
              v-model="search"/>
          <i class="pi pi-search"/>
        </div>
      </div>
    </div>

    <!-- template list --------------------------------------------------------------------------------------------- -->
    <list-wrapper :estate-height="249">

      <div id="enum-start"></div>
      <Draggable
          v-model="list"
          tag="div"
          item-key="id"
          handle=".handle"
          @end="saveEnumOrder"
          ghost-class="ghost">
        <template #item="{ element }">

          <div class="m-2 p-2 border-2 rounded-xl bg-white hover:bg-indigo-100"
               :class="selectedClass(element.id)"
               @dblclick="selectDetail(element.id)">
            <div class="flex flex-row-reverse">
              <edit-button @click="selectDetail(element.id)"></edit-button>
              <div>
                <div class="mt-1.5 text-gray-300 hover:text-green-600 cursor-move mr-2" v-if="search.length === 0">
                  <i class="pi pi-bars handle"></i>
                </div>
              </div>
              <div class="w-full">
                <div class="font-semibold">
                  {{ element.name }}
                </div>
                <div class="text-xs">{{ element.namespace }}</div>
                <div class="mt-2">
                  <Badge :value="element.domainModel.name" class="p-badge-secondary"/>
                </div>
              </div>
            </div>
          </div>

        </template>
      </Draggable>

    </list-wrapper>

    <!-- add button ------------------------------------------------------------------------------------------------ -->
    <div class="p-2">
      <div class="text-right">
        <Button
            label="ADD"
            icon="pi pi-plus"
            class="p-button-sm"
            @click="toggle"
            aria-haspopup="true"
            aria-controls="overlay_panel"
        />
      </div>
    </div>
  </div>

  <OverlayPanel ref="op" :showCloseIcon="true" :dismissable="true">
    <add-enum v-on:added="enumAdded"/>
  </OverlayPanel>

</template>

<script setup lang="ts">
import ListWrapper from "@/components/wrapper/listWrapper.vue";
import {computed, onMounted, ref, watch} from "vue";
import EditButton from "@/components/common/editButton.vue";
import AddEnum from "@/components/model/addEnum.vue";
import {useModelStore} from "@/stores/model";
import type {Enum} from "@/api/query/interface/model";
import {useToast} from "primevue/usetoast";
import type {OrderEnumCommand} from "@/api/command/interface/enumCommands";
import Draggable from "vuedraggable";
import {orderEnums} from "@/api/command/model/orderEnum";

const modelStore = useModelStore();
const toaster = useToast();
const search = ref<string>('');
const list = ref<Array<Enum>>([]);

// -- refresh list

const filteredList = computed((): Array<Enum> => {
  if (modelStore.project) {
    if (search.value === '') {
      return modelStore.project.enums;
    } else {
      const filterValue = search.value.toLowerCase();
      const filter = event => event.name.toLowerCase().includes(filterValue)
          || event.namespace.toLowerCase().includes(filterValue)
          || event.domainModel.name.toLowerCase().includes(filterValue);
      return modelStore.project.enums.filter(filter);
    }
  } else {
    return [];
  }
});

watch(filteredList, (): void => {
  list.value = JSON.parse(JSON.stringify(filteredList.value))
});

onMounted((): void => {
  list.value = JSON.parse(JSON.stringify(filteredList.value))
});

function refreshList() {
  void modelStore.reLoadProject();
}

function selectDetail(id: number) {
  void modelStore.loadEnum(id);
}

// -- save order of the domain models ----------------------------------------------------------------------------------

const sequenceEnums = computed((): OrderEnumCommand => {
  let sequence = [];
  for (let i = 0; i < list.value.length; i++) {
    sequence.push(list.value[i].id);
  }
  return {sequence: sequence};
});

async function saveEnumOrder() {
  await orderEnums(sequenceEnums.value);
  toaster.add({
    severity: "success",
    summary: "Enum order saved",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadProject();
}

function selectedClass(id: number) {
  return (id === modelStore.enumSelectedId)
      ? "border-2 border-indigo-500"
      : "border-2 border-gray-200"
}

// -- toggle add form

function enumAdded() {
  search.value = '';
  document
      .getElementById('enum-start')
      ?.scrollIntoView({behavior: "smooth", block: "nearest"});
  toggle('event');
}

const op = ref();
const toggle = (event: any) => {
  op.value.toggle(event);
};

</script>

<style scoped>

#listExample {
  background-color: #F6F6F6;
}

</style>