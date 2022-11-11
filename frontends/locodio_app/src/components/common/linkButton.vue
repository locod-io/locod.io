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
  <a :href="link" target="_blank" class="text-gray-500 hover:text-gray-700">{{link}}</a>
</template>

<script setup lang="ts">

const props = defineProps<{ link: string }>();

</script>
