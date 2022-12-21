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
  <div id="projectDocumentation">

    <Splitter style="background-color: #EEEEEE;">
      <!-- configuration -->
      <SplitterPanel :size="30">
        <doc-navigation/>
      </SplitterPanel>
      <!-- detail -->
      <SplitterPanel :size="70">
          <doc-content/>
      </SplitterPanel>
    </Splitter>

  </div>
</template>

<script setup lang="ts">
import DocNavigation from "@/components/model/documentation/docNavigation.vue";
import DocContent from "@/components/model/documentation/docContent.vue";

</script>