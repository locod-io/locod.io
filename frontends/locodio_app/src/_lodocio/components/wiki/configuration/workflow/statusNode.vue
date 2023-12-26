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
  <div class="drop-shadow-md status-node border-[1px] text-white text-center pt-1.5 px-4 pb-2 rounded-full text-sm"
       :style="'background-color:#'+data.color">
    <div>{{label}}</div>
    <Handle id="a" type="target" :position="Position.Left"
            v-if="data.type !== 'output'"
            class="drop-shadow-md bg-black text-white text-xs" :style="sourceHandleStyleA">
      >
    </Handle>
    <Handle id="b" type="source" :position="Position.Right"
            v-if="data.type !== 'input'"
            class="drop-shadow-md bg-black text-white text-xs"
            :style="sourceHandleStyleB">
      >
    </Handle>
    <!-- reset handle for the final status -->
    <Handle id="c" type="source" :position="Position.Right"
            v-if="(data.type !== 'output') && (data.type === 'input')"
            class="drop-shadow-md bg-black text-white text-xs"
            :style="sourceHandleStyleB">
      ...
    </Handle>
  </div>
</template>

<script setup lang="ts">
import { Handle, Position } from '@vue-flow/core'
import { computed } from 'vue'

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
  label: {
    type: String,
    required: true,
  }
})

const sourceHandleStyleA = computed(() => ({
  width:'20px',
  height:'20px',
  border:'0px',
  margin:'0 0 0 -8px',
  backgroundColor:'black',
}))

const sourceHandleStyleB = computed(() => ({
  width:'20px',
  height:'20px',
  border:'0px',
  margin:'0 -8px 0 0',
  backgroundColor:'black',
}))

</script>