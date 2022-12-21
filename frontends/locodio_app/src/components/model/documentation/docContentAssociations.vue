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
  <div id="docContentAssociations" class="ml-8 mb-4 mt-2">
    <table class="text-xs" v-if="associations.length > 0">
      <thead>
      <tr>
        <th>Type</th>
        <th class="pl-3">Target</th>
        <th class="pl-3">MappedBy</th>
        <th class="pl-3">InversedBy</th>
        <th class="pl-3">Fetch</th>
        <th class="pl-3">Sort</th>
        <th class="pl-3">Direction</th>
        <th class="pl-3">R</th>
        <th class="pl-3">M</th>
        <th class="pl-3">C</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="association in associations" class="border-t-[1px] border-gray-300">
        <td class="py-0.5 font-semibold">{{association.type}}</td>
        <td class="pl-3">{{association.targetDomainModel.name}}</td>
        <td class="pl-3">{{association.mappedBy}}</td>
        <td class="pl-3 text-xs">{{association.inversedBy}}</td>
        <td class="pl-3 text-xs">{{association.fetch}}</td>
        <td class="pl-3 text-xs">{{association.orderBy}}</td>
        <td class="pl-3 text-xs">{{association.orderDirection}}</td>
        <td class="pl-3">
          <font-awesome-icon icon="fa-solid fa-check" v-if="association.required"/>
        </td>
        <td class="pl-3">
          <font-awesome-icon icon="fa-solid fa-check" v-if="association.make"/>
        </td>
        <td class="pl-3">
          <font-awesome-icon icon="fa-solid fa-check" v-if="association.change"/>
        </td>
      </tr>
      </tbody>
    </table>
    <div v-else>
      <p class="text-xs">None.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import type {Association, DocumentationItem} from "@/api/query/interface/model";
import {computed} from "vue";
import {useSchemaStore} from "@/stores/schema";

// -- props
const props = defineProps<{
  docItem: DocumentationItem
}>();

const schemaStore = useSchemaStore();

const associations = computed<Association[]>(() => schemaStore.getAssociationsForModel(props.docItem.id));
</script>
