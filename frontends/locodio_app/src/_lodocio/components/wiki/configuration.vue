<!--
/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
-->

<template>
  <div class="flex flex-row">
    <div class="basis-3/4 border-r-[1px] border-gray-300 dark:border-gray-600 border-b-[1px]">
      <wiki-detail/>
    </div>
    <div class="basis-1/4">
      &nbsp;
    </div>
  </div>

</template>

<script setup lang="ts">
import {useWikiStore} from "@/_lodocio/stores/wiki";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import WikiDetail from "@/_lodocio/components/wiki/configuration/wikiDetail.vue";

const wikiStore = useWikiStore();
const projectStore = useDocProjectStore();

</script>

<style scoped>

</style>