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
  <div class="flex h-2 border border-grey-30 rounded">
    <div
        :style="{ width: `${width}%` }"
        :class="[color]"
        class="progress-bar flex h-full"
    />
  </div>
</template>

<script>
import {computed} from 'vue';

export default {
  props: {
    max: {
      type: Number,
      required: true,
    },
    value: {
      type: Number,
      required: true,
    },
    color: {
      type: String,
      default: 'bg-transparent',
    },
  },
  setup(props) {
    const width = computed(() => (props.value / props.max) * 100);
    return {width};
  },
};
</script>

<style scoped>

.progress-bar {
  transition: 0.5s ease-in-out;
}

</style>