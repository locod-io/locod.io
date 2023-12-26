<template>
  <div id="CreateAccount">

    <div class="flex flex-row mt-1">
      <div class="basis-1/2">
        <div>
          <label for="ti_firstname">Firstname</label>
        </div>
        <div class="mt-1">
          <input class="border-[1px] border-gray-300 p-2 rounded-lg w-full"
                 v-model="firstname"
                 :class="firstnameClass"
                 id="ti_firstname" type="text"
                 required name="ti_firstname">
          <div class="text-right" v-if="firstname.trim() === ''">
            <span class="text-xs text-red-500">Required field.</span>
          </div>
        </div>
      </div>

      <div class="basis-1/2 ml-8">
        <div>
          <label for="ti_lastname">Lastname</label>
        </div>
        <div class="mt-1">
          <input class="border-[1px] border-gray-300 p-2 rounded-lg w-full"
                 v-model="lastname"
                 :class="lastnameClass"
                 id="ti_lastname" type="text"
                 required name="ti_lastname">
          <div class="text-right" v-if="lastname.trim() === ''">
            <span class="text-xs text-red-500">Required field.</span>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-1">
      <div>
        <label for="ti_password1">Password</label>
      </div>
      <div class="">
        <input class="border-[1px] border-gray-300 p-2 rounded-lg w-full"
               :class="strongPasswordClass"
               autoComplete="new-password"
               v-model="password1"
               id="ti_password1" type="password"
               required name="ti_password1">
        <PasswordScore
            :value="password1"
            class="mt-2 mb-6"
            @passed="isPasswordStrong = true"
            @failed="isPasswordStrong = false"
        />
      </div>
    </div>

    <div v-if="isPasswordStrong" class="mt-1">
      <div>
        <label for="ti_password2">Repeat password</label>
      </div>
      <div class="mt-2">
        <input class="border-[1px] border-gray-300 p-2 rounded-lg w-full"
               v-model="password2"
               :class="passwordMatchClass"
               autoComplete="new-password"
               id="ti_password2" type="password"
               required name="ti_password2">
        <div>
          <span class="text-xs" v-if="password2 === ''">Please repeat your password.</span>
          <span class="text-xs text-red-500" v-else-if="!isPasswordMatching">The passwords don't match.</span>
        </div>
      </div>
    </div>

    <div class="mt-2 pt-4">
      <div v-if="formIsValid">
        <button type="submit" class="bg-indigo-500 w-full text-white rounded-lg uppercase p-2 hover:bg-indigo-600">
          CREATE ACCOUNT
        </button>
      </div>
      <div v-else>
        <button class="bg-indigo-200 w-full text-white rounded-lg uppercase p-2">
          CREATE ACCOUNT
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import {computed, ref} from "vue";
import PasswordScore from "./PasswordScore";

const isPasswordStrong = ref(false);
const firstname = ref('');
const lastname = ref('');
const password1 = ref('');
const password2 = ref('');

const firstnameClass = computed(() => {
  if (firstname.value.trim() === '') return 'border-gray-200';
  else return 'border-green-500';
})

const lastnameClass = computed(() => {
  if (lastname.value.trim() === '') return 'border-gray-200';
  else return 'border-green-500';
})

const strongPasswordClass = computed(() => {
  if (password1.value.trim() === '') return 'border-gray-200';
  if (!isPasswordStrong.value) return 'border-red-500';
  if (isPasswordStrong.value) return 'border-green-500';
})

const passwordMatchClass = computed(() => {
  if (password1.value !== password2.value) return 'border-gray-200'
  if (password1.value === password2.value) return 'border-green-500'
})

const isPasswordMatching = computed(() => {
  if (password1.value.trim() === '') return false;
  return password1.value === password2.value;
});

const formIsValid = computed(() => {
  if (firstname.value.trim() === '') return false;
  if (lastname.value.trim() === '') return false;
  if (!isPasswordMatching.value) return false;

  return true;
});

</script>