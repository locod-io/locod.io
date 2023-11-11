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

  <div
      class="flex gap-2 p-3 border-b-[1px] border-gray-300 dark:border-gray-600 h-12 text-sm bg-white dark:bg-gray-900">
    <div class="flex-none mt-0.5">
      <a :href="issue.url" class="text-xs" target="_blank">
        <i class="pi pi-link"></i>
      </a>
    </div>
    <div class="flex-grow line-clamp-1 font-bold">
      {{ issue.title }}
    </div>
  </div>

  <div
      class="flex gap-2 p-3 border-b-[1px] border-gray-300 dark:border-gray-600 h-12 text-sm bg-white dark:bg-gray-900">
    <div class="flex-none">
      <Timeago :datetime="issue.createdAt" long/>
    </div>
    <div class="flex-grow line-clamp-1">
      | &nbsp; assigned to {{ issue.assigneeName }}
    </div>
    <div class="flex-none">
      {{ issue.state.name }}
      <span v-if="issue.state.type === 'completed'">
        <Timeago :datetime="issue.completedAt" long/>
      </span>
    </div>
  </div>

  <div
      class="descriptionWrapper p-4 text-sm border-b-[1px] border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
    <VueShowdown :markdown="issue.description"/>
  </div>

  <!-- subIssues -->
  <div v-for="subIssue in issue.subIssues.collection" :key="subIssue.id">
    <div
        class="flex gap-2 p-3 border-b-[1px] border-gray-300 dark:border-gray-600 h-12 text-sm bg-white dark:bg-gray-900">
      <div class="flex-none">
      <span class="rounded-full px-2 text-white font-bold text-xs py-1"
            :title="subIssue.state.name"
            :style="'background-color: '+subIssue.state.color+''">
        {{ subIssue.identifier }}
      </span>
      </div>
      <div class="flex-none mt-0.5">
        <a :href="subIssue.url" class="text-xs" target="_blank">
          <i class="pi pi-link"></i>
        </a>
      </div>
      <div class="flex-grow line-clamp-1">
        {{ subIssue.title }}
      </div>
      <div class="flex-none mt-0.5">
        <i class="pi pi-info-circle" v-tooltip.left="`${subIssue.assigneeName} -> ${subIssue.state.name}`"></i>
      </div>
    </div>
    <div v-if="subIssue.description != ''"
         class="descriptionWrapper p-4 text-xs border-b-[1px] border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      <VueShowdown :markdown="subIssue.description"/>
    </div>
  </div>

  <!-- comments -->
  <div v-if="issue.comments.collection.length !== 0"
      class="flex gap-2 p-3 border-b-[1px] border-gray-300 dark:border-gray-600 h-12 text-sm bg-white dark:bg-gray-900">
    <i class="pi pi-comment mt-0.5"></i> Comments
  </div>

  <div class="p-3 border-b-[1px] border-gray-300 dark:border-gray-600 text-sm bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-300"
      v-for="comment in issue.comments.collection" :key="comment.id">
    <div class="text-xs text-gray-400 dark:text-gray-500">
      <Timeago :datetime="comment.createdAt" long/>
      {{ comment.userName }} wrote:
    </div>
    <div class="mt-2 commentWrapper">
      <VueShowdown :markdown="comment.body"/>
    </div>
    <div v-if="comment.replies.collection.length !== 0">
      <div class="flex mt-2" v-for="reply in comment.replies.collection">
        <div class="flex-none w-12">&nbsp;</div>
        <div class="flex-grow">
          <div class="text-xs text-gray-400 dark:text-gray-500">
            <Timeago :datetime="reply.createdAt" long/>
            {{ comment.userName }} replied:
          </div>
          <div class="mt-2 commentWrapper">
            <VueShowdown :markdown="reply.body"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type {Issue} from "@/api/query/interface/linear";
import {Timeago} from 'vue2-timeago';

const props = defineProps<{
  issue: Issue
}>();

</script>

<style scoped>

</style>