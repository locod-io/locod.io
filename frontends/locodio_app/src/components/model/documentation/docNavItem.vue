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
  <div id="navigationItem" class="text-sm">

    <div class="hover:bg-white">
      <div v-if="(item.level === 1)" class="border-t-[1px] border-gray-600"></div>
      <div v-else class="border-t-[1px] border-gray-300"></div>
      <div class="flex py-0.5">
        <div class="text-gray-400 mr-2 w-4">
          <div class="cursor-pointer" @click="item.isOpen = true"
               v-if="!item.isOpen && item.children">
            <font-awesome-icon icon="fa-solid fa-chevron-right"/>
          </div>
          <div class="cursor-pointer" @click="item.isOpen = false"
               v-if="item.isOpen && item.children">
            <font-awesome-icon icon="fa-solid fa-chevron-down"/>
          </div>
        </div>
        <div>
          <span v-for="i in item.level">
            <span v-if="i != 1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
          </span>
        </div>
        <div class="cursor-pointer" @click="scrollToElement(item)">{{ item.levelLabel }}</div>
        <status-badge-very-small :status="item.status" v-if="item.status" class="ml-1 mt-0.5"/>
        <div class="ml-1 cursor-pointer" @click="scrollToElement(item)">{{ item.label }}</div>
      </div>
    </div>

    <!-- --  children --------------------------------------------------------------------  -->
    <div v-for="navItem in item.children" v-if="item.isOpen">
      <doc-nav-item :item="navItem"/>
    </div>

  </div>
</template>

<script setup lang="ts">
import StatusBadgeVerySmall from "@/components/common/statusBadgeVerySmall.vue";
import type {NavigationItem} from "@/components/model/documentation/model";

// -- props
const props = defineProps<{
  item: NavigationItem
}>();

function scrollToElement(item: NavigationItem) {
  document
      .getElementById(`doc-${item.levelLabel}`)
      ?.scrollIntoView({ behavior: "smooth", block: "start" });
}

</script>