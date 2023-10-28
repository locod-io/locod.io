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
  <div id="navigationItem" class="text-sm font-semibold">
    <div class="hover:bg-indigo-100 bg-white dark:bg-gray-900 dark:hover:bg-indigo-900">
      <div class="flex py-1 px-2 border-b-[1px] border-gray-300 dark:border-gray-600 ">
        <div class="text-gray-400 mr-2 w-4 flex-none">
          <div class="cursor-pointer" @click="item.isOpen = true"
               v-if="!item.isOpen && item.children">
            <font-awesome-icon icon="fa-solid fa-chevron-right"/>
          </div>
          <div class="cursor-pointer" @click="item.isOpen = false"
               v-if="item.isOpen && item.children">
            <font-awesome-icon icon="fa-solid fa-chevron-down"/>
          </div>
        </div>
        <div class="flex-none">
          <span v-for="i in item.level">
            <span v-if="i != 1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
          </span>
        </div>
        <div class="cursor-pointer flex-none" @click="scrollToElement(item)">{{ item.levelLabel }}</div>
        <status-badge-very-small :status="item.status" v-if="item.status" class="ml-1 mt-0.5"/>
        <div class="ml-1 cursor-pointer flex-grow line-clamp-1" @click="scrollToElement(item)">{{ item.label }}</div>
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
      ?.scrollIntoView({behavior: "smooth", block: "start"});
}

</script>