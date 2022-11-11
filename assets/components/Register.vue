<template>
  <div id="Register">

    <div class="">
      <div>
        <label for="ti_organisation">Organisation / Company</label>
      </div>
      <div class="mt-1">
        <input class="border-2 p-2 rounded-lg w-full"
               v-model="organisation"
               :class="organisationClass"
               id="ti_organisation" type="text"
               required name="ti_organisation">
        <div class="text-right" v-if="organisation.trim() === ''">
          <span class="text-xs text-red-500">Required field.</span>
        </div>
      </div>
    </div>

    <div class="flex flex-row mt-1">
      <div class="basis-1/2">
        <div>
          <label for="ti_firstname">Firstname</label>
        </div>
        <div class="mt-1">
          <input class="border-2 p-2 rounded-lg w-full"
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
          <input class="border-2 p-2 rounded-lg w-full"
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
        <label for="ti_email">Email</label>
      </div>
      <div class="mt-1">
        <input class="border-2 p-2 rounded-lg w-full"
               :class="emailClass"
               v-model="email"
               @change="checkEmail"
               id="ti_email" type="email"
               required name="ti_email">
        <div class="text-right">
          <div v-if="!isEmailChecked">
            <span
                class="text-xs text-gray-500">A confirmation link will be send here. This will be also your login.</span>
          </div>
          <div v-else>
            <div v-if="!isEmailValid">
              <span class="text-xs text-red-500">Sorry this email address is already taken.</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-1">
      <div>
        <label for="ti_password1">Password</label>
      </div>
      <div class="">
        <input class="border-2 p-2 rounded-lg w-full"
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
        <input class="border-2 p-2 rounded-lg w-full"
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
          SIGN UP
        </button>
      </div>
      <div v-else>
        <button class="bg-indigo-200 w-full text-white rounded-lg uppercase p-2">
          SIGN UP
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import {computed, ref} from "vue";
import PasswordScore from "./PasswordScore";
import axios from "axios";

const isPasswordStrong = ref(false);
const organisation = ref('');
const firstname = ref('');
const lastname = ref('');
const email = ref('');
const password1 = ref('');
const password2 = ref('');
const isEmailChecked = ref(false);
const isEmailValid = ref(false);

function checkEmail() {
  if (email.value.trim() !== '') {
    axios.get('/api/user/c/' + email.value).then(response => (checkEmailHandler(response.data)));
  }
}

function checkEmailHandler(data) {
  if (data === true) {
    isEmailChecked.value = true;
    isEmailValid.value = false;
  }
  if (data === false) {
    isEmailChecked.value = true;
    isEmailValid.value = true;
  }
  if (!(data === true || data === false)) {
    isEmailChecked.value = false;
    isEmailValid.value = false;
  }
}

const organisationClass = computed(() => {
  if (organisation.value.trim() === '') return 'border-gray-200';
  else return 'border-green-500';
})

const firstnameClass = computed(() => {
  if (firstname.value.trim() === '') return 'border-gray-200';
  else return 'border-green-500';
})

const lastnameClass = computed(() => {
  if (lastname.value.trim() === '') return 'border-gray-200';
  else return 'border-green-500';
})

const emailClass = computed(() => {
  if (!isEmailValid.value) {
    return 'border-gray-200';
  } else {
    return 'border-green-500';
  }
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
  if(password1.value.trim() === '') return false;
  return password1.value === password2.value;
});

const formIsValid = computed(() => {
  if(organisation.value.trim() === '') return false;
  if(firstname.value.trim() === '') return false;
  if(lastname.value.trim() === '') return false;
  if(!(isEmailChecked.value === true && isEmailValid.value === true)) return false;
  if(!isPasswordMatching.value) return false;

  return true;
});

</script>