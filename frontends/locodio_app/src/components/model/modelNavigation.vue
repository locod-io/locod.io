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
  <div id="modelNavigation">
    <TabMenu :model="items"/>
  </div>
</template>

<script setup lang="ts">
import {computed, ref} from 'vue';
import {useOrganisationStore} from "@/stores/organisation";

const organisationStore = useOrganisationStore();

const items = computed(() => {
  let navigation = [
    {
      label: 'Overview',
      icon: 'pi pi-sitemap',
      to: '/model/o/' + organisationStore.organisationId + '/p/' + organisationStore.projectId + '/overview'
    },
    {
      label: 'Configuration',
      icon: 'pi pi-cog',
      to: '/model/o/' + organisationStore.organisationId + '/p/' + organisationStore.projectId + '/configuration'
    },
    {
      label: 'Model',
      icon: 'pi pi-database',
      to: '/model/o/' + organisationStore.organisationId + '/p/' + organisationStore.projectId + '/domain-model'
    },
    {
      label: 'Enum',
      icon: 'pi pi-align-justify',
      to: '/model/o/' + organisationStore.organisationId + '/p/' + organisationStore.projectId + '/enum'
    },
    {
      label: 'Query',
      icon: 'pi pi-upload',
      to: '/model/o/' + organisationStore.organisationId + '/p/' + organisationStore.projectId + '/query'
    },
    {
      label: 'Command',
      icon: 'pi pi-download',
      to: '/model/o/' + organisationStore.organisationId + '/p/' + organisationStore.projectId + '/command'
    },
    {
      label: 'Documentation',
      icon: 'pi pi-map',
      to: '/model/o/' + organisationStore.organisationId + '/p/' + organisationStore.projectId + '/documentation'
    },
    {
      label: 'Templates',
      icon: 'pi pi-file',
      to: '/model/o/' + organisationStore.organisationId + '/p/' + organisationStore.projectId + '/template'
    },
  ];
  return navigation;
});

</script>
