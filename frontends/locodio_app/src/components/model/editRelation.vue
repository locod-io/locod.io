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
  <tr id="Component_Edit_Relation"
      class="border-b-[1px]"
      v-on:keyup.enter="save"
      v-on:keyup.esc="viewForm">

    <!-- view mode ------------------------------------------------------------------------------------------------- -->
    <td v-if="isView">
      <div class="flex mt-1 mb-1 mr-2">
        <div class="mt-1 text-gray-200 hover:text-green-600 cursor-move mr-2">
          <i class="pi pi-bars handle"></i>
        </div>
        <edit-button @click="editForm"/>
      </div>
    </td>
    <td v-if="isView"><strong>{{ relation.type }}</strong></td>
    <td v-if="isView">
      <div class="flex">
        <div><i class="pi pi-check" v-if="(relation.required)"></i></div>
        <div class="ml-2"><i class="pi pi-check" v-if="(relation.make)"></i></div>
        <div class="ml-2"><i class="pi pi-check" v-if="(relation.change)"></i></div>
      </div>
    </td>
    <td v-if="isView">{{ relation.targetDomainModel.name }}</td>
    <td v-if="isView">
      <!-- rendering of different combinations -->
      <span v-if="relation.type === 'One-To-Many_Bidirectional'">{{ relation.mappedBy }}</span>
      <span v-if="(relation.type === 'One-To-One_Self-referencing'
                            || relation.type === 'One-To-One_Unidirectional'
                            || relation.type === 'Many-To-Many_Unidirectional'
                            || relation.type === 'Many-To-One_Unidirectional')">
        ${{ relation.mappedBy }}
      </span>
      <span v-if="relation.type === 'Many-To-One_Unidirectional'"> / {{ relation.inversedBy }}</span>
      <span v-if="(relation.type === 'One-To-One_Bidirectional'
                           || relation.type === 'Many-To-Many_Self-referencing'
                           || relation.type === 'One-To-Many_Self-referencing'
                           || relation.type === 'Many-To-Many_Bidirectional')">
        <span v-if="relation.mappedBy !== ''">{{ relation.mappedBy }}</span>
        <span v-if="relation.inversedBy !== ''"> / {{ relation.inversedBy }}</span>
      </span>
    </td>
    <td v-if="isView">{{ relation.fetch }}</td>
    <td v-if="isView">
      <div v-if="relation.type === 'One-To-Many_Bidirectional'">{{ relation.orderBy }}</div>
    </td>
    <td v-if="isView">
      <div v-if="relation.type === 'One-To-Many_Bidirectional'">{{ relation.orderDirection }}</div>
    </td>
    <td v-if="isView" align="right">
      <delete-button @deleted="deleteItem"></delete-button>
    </td>

    <!-- edit mode ------------------------------------------------------------------------------------------------- -->
    <td v-if="!isView">&nbsp;</td>
    <td v-if="!isView">
      <div class="flex ml-8">
        <div class="mr-2"><InputSwitch v-model="commandEdit.required"/></div>
        <div class="mr-2"><InputSwitch v-model="commandEdit.make"/></div>
        <div><InputSwitch v-model="commandEdit.change"/></div>
      </div>
    </td>
    <td v-if="!isView" colspan="3">
      <!-- rendering of different combinations -->
      <div class="flex">
        <div v-if="commandEdit.type == 'One-To-Many_Bidirectional'" class="mr-2">
          <InputText placeholder="mappedBy"
                     class="w-full p-inputtext-sm"
                     v-model="commandEdit.mappedBy"/>
        </div>
        <div class="mr-2"
            v-if="(commandEdit.type == 'One-To-One_Self-referencing'|| commandEdit.type == 'One-To-One_Unidirectional'|| commandEdit.type == 'Many-To-Many_Unidirectional'|| commandEdit.type == 'Many-To-One_Unidirectional')">
          <InputText placeholder="variable"
                     class="w-full p-inputtext-sm"
                     v-model="commandEdit.mappedBy"/>
        </div>
        <div v-if="commandEdit.type == 'Many-To-One_Unidirectional'" class="mr-2">
          <InputText placeholder="inversedBy"
                     class="w-full p-inputtext-sm"
                     v-model="commandEdit.inversedBy"/>
        </div>
      </div>
      <div class="flex flex-row"
           v-if="(commandEdit.type == 'One-To-One_Bidirectional' || commandEdit.type == 'Many-To-Many_Self-referencing'|| commandEdit.type == 'One-To-Many_Self-referencing'|| commandEdit.type == 'Many-To-Many_Bidirectional')">
        <div v-if="commandEdit.mappedBy != ''" class="basis-1/2">
          <InputText placeholder="mappedBy"
                     class="w-full p-inputtext-sm"
                     v-model="commandEdit.mappedBy"/>
        </div>
        <div v-if="commandEdit.inversedBy != ''" class="basis-1/2 ml-2">
          <InputText placeholder="inversedBy"
                     class="w-full p-inputtext-sm"
                     v-model="commandEdit.inversedBy"/>
        </div>
      </div>
    </td>

    <!-- fetch -->
    <td v-if="!isView">
      <div class="p-inputtext-sm">
        <Dropdown optionLabel="type"
                  v-model="commandEdit.fetch"
                  option-label="label"
                  option-value="code"
                  :options="modelStore.lists.fetchTypes"
                  class="w-full p-dropdown-sm"/>
      </div>
    </td>
    <!-- order by -->
    <td v-if="!isView">
      <div v-if="commandEdit.type == 'One-To-Many_Bidirectional'">
        <InputText class="w-full p-inputtext-sm"
                   v-model="commandEdit.orderBy"
                   placeholder="length"/>
      </div>
    </td>
    <!-- order direction -->
    <td v-if="!isView">
      <div class="p-inputtext-sm" v-if="commandEdit.type == 'One-To-Many_Bidirectional'">
        <Dropdown optionLabel="type"
                  v-model="commandEdit.orderDirection"
                  option-label="label"
                  option-value="code"
                  :options="modelStore.lists.orderTypes"
                  class="w-full p-dropdown-sm"/>
      </div>
    </td>
    <!-- buttons -->
    <td v-if="!isView">
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
import {onMounted, ref} from "vue";
import EditButton from "@/components/common/editButton.vue";
import SaveButton from "@/components/common/saveButton.vue";
import CloseButton from "@/components/common/closeButton.vue";
import type {Relation} from "@/api/query/interface/model";
import {useModelStore} from "@/stores/model";
import {useToast} from "primevue/usetoast";
import type {ChangeRelationCommand} from "@/api/command/interface/domainModelCommands";
import {minValue, required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {changeRelation} from "@/api/command/model/changeRelation";
import DeleteButton from "@/components/common/deleteButton.vue";
import {deleteField} from "@/api/command/model/deleteField";
import {deleteRelation} from "@/api/command/model/deleteRelation";

// -- props

const props = defineProps<{
  relation: Relation
}>();

const isView = ref<boolean>(true);
const isSaving = ref<boolean>(false);
const modelStore = useModelStore();
const toaster = useToast();
const commandEdit = ref<ChangeRelationCommand>({
  id: props.relation.id,
  type: props.relation.type,
  mappedBy: props.relation.mappedBy,
  inversedBy: props.relation.inversedBy,
  fetch: props.relation.fetch,
  orderBy: props.relation.orderBy,
  orderDirection: props.relation.orderDirection,
  targetDomainModelId: props.relation.targetDomainModel.id,
  make: props.relation.make,
  change: props.relation.change,
  required: props.relation.required
});

// -- mounted

onMounted((): void => {
  v$.value.$touch();
});

// -- validation

const rules = {
  type: {required},
  fetch: {required},
  targetDomainModelId: {required,minValueValue: minValue(1)}
};
const v$ = useVuelidate(rules, commandEdit);

// -- functions

function editForm() {
  isView.value = false;
  commandEdit.value.id = props.relation.id;
  commandEdit.value.type = props.relation.type;
  commandEdit.value.mappedBy = props.relation.mappedBy;
  commandEdit.value.inversedBy = props.relation.inversedBy;
  commandEdit.value.fetch = props.relation.fetch;
  commandEdit.value.orderBy = props.relation.orderBy;
  commandEdit.value.orderDirection = props.relation.orderDirection;
  commandEdit.value.targetDomainModelId = props.relation.targetDomainModel.id;
  commandEdit.value.isMake = false;
  commandEdit.value.isChange = false;
}

function viewForm() {
  isView.value = true;
}

async function save() {
  v$.value.$touch();
  if (!v$.value.$invalid) {
    isSaving.value = true;
    await changeRelation(commandEdit.value);
    toaster.add({
      severity: "success",
      summary: "Relation changed",
      detail: "",
      life: modelStore.toastLifeTime,
    });
    await modelStore.reLoadDomainModel();
    isView.value = true;
    isSaving.value = false;
  }
}

async function deleteItem() {
  isSaving.value = true;
  await deleteRelation({id: props.relation.id});
  toaster.add({
    severity: "success",
    summary: "Relation deleted.",
    detail: "",
    life: modelStore.toastLifeTime,
  });
  await modelStore.reLoadDomainModel();
  isSaving.value = false;
}

</script>
