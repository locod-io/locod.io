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
  <div id="loadingSpinner">
    <div class="mx-auto my-auto text-gray-400" style="width:50px;">
      <ProgressSpinner />
      <!-- <Skeleton shape="circle" size="100px"/>-->
    </div>
  </div>
</template>

<script setup lang="ts">

</script>

<style scoped>

#loadingSpinner {
  position: absolute;
  display: block;
  top:50%;
  left:0;
  right:0;
}

</style>