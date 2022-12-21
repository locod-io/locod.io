<template>
  <div id="dialogOrderItems" style="width: 600px;">
    <TabView>
      <TabPanel header="Modules">
        <sequence-module/>
      </TabPanel>
      <TabPanel header="Models">
        <sequence-domain-model/>
      </TabPanel>
      <TabPanel header="Enums">
        <sequence-enum/>
      </TabPanel>
      <TabPanel header="Queries">
        <sequence-query/>
      </TabPanel>
      <TabPanel header="Commands">
        <sequence-command/>
      </TabPanel>
    </TabView>
  </div>
</template>

<script setup lang="ts">
import SequenceModule from "@/components/model/documentation/sequenceModule.vue";
import SequenceDomainModel from "@/components/model/documentation/sequenceDomainModel.vue";
import SequenceEnum from "@/components/model/documentation/sequenceEnum.vue";
import SequenceQuery from "@/components/model/documentation/sequenceQuery.vue";
import SequenceCommand from "@/components/model/documentation/sequenceCommand.vue";
</script>