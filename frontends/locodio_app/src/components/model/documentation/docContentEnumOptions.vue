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
  <div id="docContentEnumOptions" class="ml-8 mb-4">
    <table class="text-xs">
      <thead>
      <tr>
        <th>Code</th>
        <th class="pl-3">Value</th>
      </tr>
      </thead>
      <tbody>
        <tr v-for="option in docItem.item.options" class="border-t-[1px] border-gray-300">
          <td class="py-0.5 font-semibold">{{option.code}}</td>
          <td class="pl-3">{{option.value}}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import type {DocumentationItem} from "@/api/query/interface/model";

// -- props
const props = defineProps<{
  docItem: DocumentationItem
}>();
</script>

