<template>
  <div id="docProjectNavigation">
    <TabMenu :model="items"/>
  </div>
</template>

<script setup lang="ts">
import {computed} from "vue";
import {useDocProjectStore} from "@/_lodocio/stores/project";

const docProjectStore = useDocProjectStore();

const items = computed(() => {
  let navigation = [
    {
      label: 'Roadmap',
      icon: 'pi pi-map',
      to: '/doc/o/' + docProjectStore.organisation?.id + '/dp/' + docProjectStore.docProjectId + '/roadmap'
    },
    {
      label: 'Releases',
      icon: 'pi pi-share-alt',
      to: '/doc/o/' + docProjectStore.organisation?.id + '/dp/' + docProjectStore.docProjectId + '/releases'
    },
    {
      label: 'Trackers',
      icon: 'pi pi-box',
      to: '/doc/o/' + docProjectStore.organisation?.id + '/dp/' + docProjectStore.docProjectId + '/trackers'
    },
  ];
  return navigation;
});

</script>

<style scoped>

</style>