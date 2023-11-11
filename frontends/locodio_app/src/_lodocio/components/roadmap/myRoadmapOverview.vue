<!--
/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
-->

<template>
  <div v-if="isLoading">
    <loading-spinner/>
  </div>
  <div v-else>
    <g-gantt-chart
        :color-scheme="colorScheme"
        :chart-start="chartStart"
        :chart-end="chartEnd"
        precision="month"
        bar-start="startDate"
        bar-end="targetDate"
    >
      <g-gantt-row v-for="roadmap in roadMapRows"
                   highlight-on-hover="true"
                   :label="roadmap.name"
                   :bars="roadmap.rows">
        <template #bar-label="{bar}">
          <div class="flex w-full">
            <div class="w-24" v-if="!bar.hasStart"
                 :style="'background:linear-gradient(to left,'+bar.color+',transparent)'">
              &nbsp;
            </div>
            <div class="text-white p-2 flex-grow h-12" :style="'background-color:'+bar.color">
              <!-- @click="showDialogProjectDetailFn(bar.ganttBarConfig.id)" -->
              <div class="line-clamp-1 mt-1">
                <roadmap-project-label-renderer
                    :status="bar.status"
                    :label="bar.ganttBarConfig.label"/>
              </div>
            </div>
            <div class="w-24" v-if="!bar.hasEnd"
                 :style="'background:linear-gradient(to right,'+bar.color+',transparent)'">
              &nbsp;
            </div>
          </div>
        </template>
      </g-gantt-row>
    </g-gantt-chart>
  </div>

  <!-- -- detail related project -->
<!--  <div>-->
<!--    <Dialog-->
<!--        header="&nbsp;"-->
<!--        v-model:visible="showDialogProjectDetail"-->
<!--        :modal="true"-->
<!--        position="top">-->
<!--      <dialog-project-detail :project-id="selectedProjectId"/>-->
<!--    </Dialog>-->
<!--  </div>-->

</template>

<script setup lang="ts">
import {useAppStore} from "@/stores/app";
import type {Roadmap} from "@/api/query/interface/linear";
import {computed, onMounted, ref} from 'vue';
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import {getUserRoadmap} from "@/api/query/user/getUserRoadmap";
import {getMaxDateRoadmaps, getMinDateRoadmaps, getRowBarList} from "@/_lodocio/function/roadmapDateCaculations";
import {GGanttChart, GGanttRow} from "@infectoone/vue-ganttastic";
import RoadmapProjectLabelRenderer from "@/_lodocio/components/roadmap/roadmapProjectLabelRenderer.vue";

const appStore = useAppStore();
const roadmaps = ref<Array<Roadmap>>([]);
const isLoading = ref<boolean>(true);
const chartStart = ref();
const chartEnd = ref();

// -- project detail ------------------------------------------------

// const showDialogProjectDetail = ref<boolean>(false);
// const selectedProjectId = ref<string>('');
//
// function showDialogProjectDetailFn(projectId: string) {
//   selectedProjectId.value = projectId;
//   showDialogProjectDetail.value = true;
// }

// -- gantt chart ----------------------------------------------------

onMounted((): void => {
  void loadRoadmap();
});

async function loadRoadmap() {
  isLoading.value = true;
  roadmaps.value = await getUserRoadmap();
  chartStart.value = getMinDateRoadmaps(roadmaps.value);
  chartEnd.value = getMaxDateRoadmaps(roadmaps.value);
  isLoading.value = false;
}

const colorScheme = computed(() => {
  if (appStore.theme === 'light') {
    return {
      primary: '#f3f4f6',
      secondary: '#e5e7eb',
      ternary: '#f3f4f6',
      quartenary: '#e5e7eb',
      hoverHighlight: '#e5e7eb',
      text: '#1f2937',
      background: '#fff',
      toast: '#cc00cc'
    }
  } else {
    return {
      primary: '#282936',
      secondary: '#374151',
      ternary: '#282936',
      quartenary: '#374151',
      hoverHighlight: '#374151',
      text: '#9ca3af',
      background: '#1D1E27',
      toast: '#cc00cc'
    }
  }
});

const roadMapRows = computed(() => {
  let _rows = [];
  if (roadmaps.value.length > 0) {
    for (const roadmap of roadmaps.value) {
      for (const row of getRowBarList(roadmap)) {
        let _rowObject = {};
        _rowObject.name = roadmap.name;
        _rowObject.rows = [];
        _rowObject.rows.push(row);
        _rows.push(_rowObject);
      }
    }
  }
  return _rows;
});

</script>

<style scoped>

</style>