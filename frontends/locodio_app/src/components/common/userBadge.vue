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
  <div class="rounded-full px-2 py-1 text-xs text-white"
       :style="'background-color:'+user.color">
    <i class="pi pi-user"></i>&nbsp;&nbsp;{{ user.initials }}
  </div>
</template>

<script setup lang="ts">
import type {User} from "@/api/query/interface/user";

const props = defineProps<{
  user: User,
}>();
</script>
