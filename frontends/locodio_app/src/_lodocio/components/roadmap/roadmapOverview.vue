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
            <div class="line-clamp-1 mt-1 cursor-pointer" @click="showDialogProjectDetailFn(bar.ganttBarConfig.id)">
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

  <!-- -- detail related project -->
  <div>
    <Dialog
        header="&nbsp;"
        v-model:visible="showDialogProjectDetail"
        :modal="true"
        position="top">
      <dialog-project-detail :project-id="selectedProjectId"/>
    </Dialog>
  </div>

</template>

<script setup lang="ts">
import {computed, ref} from "vue";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import {useAppStore} from "@/stores/app";
import {GGanttChart, GGanttRow} from "@infectoone/vue-ganttastic";
import {getMaxDateRoadmaps, getMinDateRoadmaps, getRowBarList} from "@/_lodocio/function/roadmapDateCaculations";
import RoadmapProjectLabelRenderer from "@/_lodocio/components/roadmap/roadmapProjectLabelRenderer.vue";
import DialogProjectDetail from "@/_lodocio/components/roadmap/dialogProjectDetail.vue";

const projectStore = useDocProjectStore();
const appStore = useAppStore();

// -- project detail ------------------------------------------------

const showDialogProjectDetail = ref<boolean>(false);
const selectedProjectId = ref<string>('');

function showDialogProjectDetailFn(projectId: string) {
  selectedProjectId.value = projectId;
  showDialogProjectDetail.value = true;
}

// -- gantt chart ----------------------------------------------------

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

const chartStart = computed(() => {
  return getMinDateRoadmaps(projectStore.roadmaps);
});

const chartEnd = computed(() => {
  return getMaxDateRoadmaps(projectStore.roadmaps);
});

const roadMapRows = computed(() => {
  let _rows = [];
  if (projectStore.roadmaps) {
    for (const roadmap of projectStore.roadmaps) {
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