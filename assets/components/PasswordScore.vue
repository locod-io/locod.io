<template>
  <div class="relative select-none">
    <BaseProgressBar
        :value="score"
        :max="descriptions.length"
        :color="description.color"
    />

    <p class="absolute mt-1 text-xs">
      {{ description.label }}
    </p>
  </div>
</template>

<script>
import { computed, watch } from 'vue';
import { zxcvbn, zxcvbnOptions } from '@zxcvbn-ts/core';
import zxcvbnCommonPackage from '@zxcvbn-ts/language-common';
import zxcvbnEnPackage from '@zxcvbn-ts/language-en';
import BaseProgressBar from './BaseProgressBar.vue';

export default {
  components: {
    BaseProgressBar,
  },

  props: {
    value: {
      type: String,
      required: true,
    },
  },

  emits: ['passed', 'failed'],

  setup(props, { emit }) {
    const descriptions = computed(() => [
      {color: 'bg-red-600', label: 'Your password is very weak.',},
      { color: 'bg-red-300', label: 'Still weak, keep on trying!' },
      { color: 'bg-yellow-400', label: 'We are getting there...' },
      { color: 'bg-green-200', label: 'Nice, but you can still do better' },
      { color: 'bg-green-400', label: 'Congratulations, you made it!',},
    ]);

    const description = computed(() =>
        props.value && props.value.length > 0
            ? descriptions.value[score.value - 1]
            : {
              color: 'bg-transparent',
              label: 'Start typing to check your password strength...',
            }
    );

    const score = computed(() => {
      const hasValue = props.value && props.value.length > 0;
      if (!hasValue) { return 0; }
      return zxcvbn(props.value).score + 1;
    });

    const isPasswordStrong = computed(() => score.value >= 4);

    watch(isPasswordStrong, (value) => {
      value ? emit('passed') : emit('failed');
    });

    const options = {
      dictionary: {
        ...zxcvbnCommonPackage.dictionary,
        ...zxcvbnEnPackage.dictionary,
      },
      graphs: zxcvbnCommonPackage.adjacencyGraphs,
      translations: zxcvbnEnPackage.translations,
    };

    zxcvbnOptions.setOptions(options);

    return { descriptions, description, score };
  },
};
</script>
