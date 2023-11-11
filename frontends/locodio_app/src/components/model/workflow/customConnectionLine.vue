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
  <g>
    <path
        class="vue-flow__connection animated"
        fill="none"
        stroke="#CCC"
        :stroke-width="4"
        :d="`M${sourceX},${sourceY} C ${sourceX} ${targetY} ${sourceX} ${targetY} ${targetX},${targetY}`"
    />
    <circle :cx="targetX" :cy="targetY" fill="#fff" :r="6" stroke="#CCC" :stroke-width="1.5" />
  </g>
</template>

<script setup lang="ts">
const props = defineProps({
  sourceX: {
    type: Number,
    required: true,
  },
  sourceY: {
    type: Number,
    required: true,
  },
  targetX: {
    type: Number,
    required: true,
  },
  targetY: {
    type: Number,
    required: true,
  },
})
</script>