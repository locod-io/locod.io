<template>

  <div class="mt-2">
    <label for="resetPassword1">Password</label>
  </div>
  <div class="mt-2">
    <input class="border-2 p-2 rounded-lg w-full"
           :class="strongPasswordClass"
           autoComplete="new-password"
           v-model="password1"
           id="resetPassword1" type="password"
           required name="resetPassword1">
    <PasswordScore
        :value="password1"
        class="mt-2 mb-6"
        @passed="isPasswordStrong = true"
        @failed="isPasswordStrong = false"
    />
  </div>

  <div v-if="isPasswordStrong" class="mt-4 pt-4 border-t-[1px]">
    <div>
      <label for="resetPassword2">Repeat password</label>
    </div>
    <div class="mt-2">
      <input class="border-2 p-2 rounded-lg w-full"
             v-model="password2"
             :class="passwordMatchClass"
             autoComplete="new-password"
             id="resetPassword2" type="password"
             required name="resetPassword2">
      <div>
        <span class="text-xs" v-if="password2 === ''">Please repeat your password.</span>
        <span class="text-xs text-red-500" v-else-if="!isPasswordMatching">The passwords don't match.</span>
      </div>
    </div>
  </div>

  <div class="mt-4 pt-4" v-if="canReset">
    <button type="submit"
            :disabled="!canReset"
            class="bg-blue-500 w-full text-white rounded-lg uppercase p-2 hover:bg-blue-700">
      Reset password
    </button>
  </div>
  <div v-else class="mt-4 pt-4">
    <button class="bg-blue-300 w-full text-white rounded-lg uppercase p-2">
      Reset password
    </button>
  </div>

</template>

<script setup>
import {computed, ref} from "vue";
import PasswordScore from "./PasswordScore";

const isPasswordStrong = ref(false);
const password1 = ref('');
const password2 = ref('');

const strongPasswordClass = computed(() => {
  if (password1.value === '') return 'border-gray-200';
  if (!isPasswordStrong.value) return 'border-red-500';
  if (isPasswordStrong.value) return 'border-green-500';
})

const passwordMatchClass = computed(() => {
  if (password1.value !== password2.value) return 'border-gray-200'
  if (password1.value === password2.value) return 'border-green-500'
})

const isPasswordMatching = computed(() => {
  return password1.value === password2.value;
});

const canReset = computed(() => {
  return password1.value === password2.value && isPasswordStrong.value;
});

</script>