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
import { computed } from 'vue';

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
    return { width };
  },
};
</script>