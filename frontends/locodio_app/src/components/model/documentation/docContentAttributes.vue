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
  <div id="docContentAttributes" class="ml-8 mb-4 mt-2">
    <table class="text-xs">
      <thead>
      <tr>
        <th>Name</th>
        <th class="pl-3">Type</th>
        <th class="pl-3">Length</th>
        <th class="pl-3">Default</th>
        <th class="pl-3">Identifier</th>
        <th class="pl-3">Unique</th>
        <th class="pl-3">Required</th>
        <th class="pl-3">Make</th>
        <th class="pl-3">Change</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="attribute in attributes" class="border-t-[1px] border-gray-300">
        <td class="py-0.5 font-semibold">{{ attribute.name }}</td>
        <td class="pl-3">
          <span v-if="attribute.type == 'enum'">{{ attribute.type }}
            <span class="text-xs">({{ attribute.enum.name }})</span>
          </span>
          <span v-else>{{ attribute.type }}</span>
        </td>
        <td class="pl-3"><span v-if="attribute.length !== 0">{{ attribute.length }}</span></td>
        <td class="text-center"></td>
        <td class="pl-3 text-center">
          <font-awesome-icon icon="fa-solid fa-check" v-if="attribute.identifier"/>
        </td>
        <td class="pl-3 text-center">
          <font-awesome-icon icon="fa-solid fa-check" v-if="attribute.unique"/>
        </td>
        <td class="pl-3 text-center">
          <font-awesome-icon icon="fa-solid fa-check" v-if="attribute.required"/>
        </td>
        <td class="pl-3 text-center">
          <font-awesome-icon icon="fa-solid fa-check" v-if="attribute.make"/>
        </td>
        <td class="pl-3 text-center">
          <font-awesome-icon icon="fa-solid fa-check" v-if="attribute.change"/>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import type {Attribute, DocumentationItem} from "@/api/query/interface/model";
import {useSchemaStore} from "@/stores/schema";
import {computed} from "vue";

// -- props
const props = defineProps<{
  docItem: DocumentationItem
}>();

const schemaStore = useSchemaStore();
const attributes = computed<Attribute[]>(() => schemaStore.getAttributesForModel(props.docItem.id));

</script>
