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
  <div class="border-t-[1px] p-4 border-gray-300 dark:border-gray-600">
    <div class="flex">
      <div v-for="user in users" class="ml-4">
        <user-badge :user="user"/>
      </div>
    </div>
<!--    <div class="border-t-[1px] mt-4 text-center pb-4 pt-4">-->
<!--      <Button label="INVITE USER"-->
<!--              icon="pi pi-user-plus"-->
<!--              @click="toggle"-->
<!--              aria-haspopup="true"-->
<!--              aria-controls="overlay_panel"-->
<!--              class="p-button-sm p-button-outlined p-button-secondary"/>-->
<!--    </div>-->
  </div>

<!--  <OverlayPanel ref="op" :showCloseIcon="true" :dismissable="true">-->
<!--    <invite-user/>-->
<!--  </OverlayPanel>-->

</template>

<script setup lang="ts">
import {ref} from "vue";
import InviteUser from "@/components/organisation/inviteUser.vue";
import type {User} from "@/api/query/interface/user";
import UserBadge from "@/components/common/userBadge.vue";

const props = defineProps<{
  users: Array<User>,
}>();

// -- toggle form
const op = ref();
const toggle = (event: any) => {
  op.value.toggle(event);
};

</script>