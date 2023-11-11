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
  <TabView>
    <TabPanel header="Documentation">
      <extension-wrapper :estate-height="85">
        <documentation :type="props.type" :id="props.id"/>
      </extension-wrapper>
    </TabPanel>
    <TabPanel header="Related Issues">
      <extension-wrapper :estate-height="85">
        <issues :type="props.type" :id="props.id"/>
      </extension-wrapper>
    </TabPanel>
<!--    <TabPanel header="Comments">-->
<!--      <extension-wrapper :estate-height="85">-->
<!--        <comments :type="props.type" :id="props.id"/>-->
<!--      </extension-wrapper>-->
<!--    </TabPanel>-->
    <TabPanel header="Activity">
      <extension-wrapper :estate-height="85">
        <activity :type="props.type" :id="props.id"/>
      </extension-wrapper>
    </TabPanel>
  </TabView>
</template>

<script setup lang="ts">
import Documentation from "@/_locodio/extension/documentation.vue";
import Issues from "@/_locodio/extension/issues.vue";
import Comments from "@/_locodio/extension/comments.vue";
import Activity from "@/_locodio/extension/activity.vue";
import ExtensionWrapper from "@/_locodio/extension/extensionWrapper.vue";

const props = defineProps<{
  type: 'module' | 'domain-model' | 'enum' | 'query' | 'command',
  id: number
}>();

</script>

<style scoped>

</style>
