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
  <div id="mappingSelectionBox">
    <i class="pi pi-check-square cursor-pointer" v-if="selection" @click="unSetSelected"></i>
    <i class="pi pi-stop cursor-pointer" v-if="!selection" @click="setSelected"></i>
  </div>
</template>

<script setup lang="ts">

const emit = defineEmits(["select", "unselect"]);
const props = defineProps<{ selection: boolean }>();

function setSelected() {
  emit("select");
}

function unSetSelected() {
  emit("unselect");
}

</script>