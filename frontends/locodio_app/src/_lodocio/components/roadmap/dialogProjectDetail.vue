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
  <div style="width:380px">

    <div v-if="!linearProjectDetailLoading">

      <div class="flex gap-2" :style="'background-color:'+linearProjectDetail.project.color">
        <div class="flex-none py-4 pl-4 text-white mt-0.5">
          <a :href="linearProjectDetail.project.url" target="_blank"><i class="pi pi-link"></i></a>
        </div>
        <div class="flex-grow line-clamp-1 mt-4">
          <roadmap-project-label-renderer
              :status="linearProjectDetail.project.state"
              :label="linearProjectDetail.project.name"/>
        </div>
      </div>

      <div>
        <div v-if="linearProjectDetail.project.description !== ''"
             class="border-b-[1px] border-gray-300 dark:border-gray-600 py-4">
          {{ linearProjectDetail.project.description }}
        </div>

        <div class="flex gap-2 mt-4">
          <div class="w-24 text-xs flex-none">Status</div>
          <div class="flex-grow line-clamp-1">{{ linearProjectDetail.project.state }}</div>
        </div>

        <div class="flex gap-2 mt-2">
          <div class="w-24 text-xs flex-none">Start</div>
          <div class="flex-grow line-clamp-1" v-if="linearProjectDetail.project.startDate">{{
              moment(linearProjectDetail.project.startDate).format("DD/MM/YYYY")
            }}
          </div>
          <div v-else>...</div>
        </div>

        <div class="flex gap-2 mt-2">
          <div class="w-24 text-xs flex-none">Target</div>
          <div class="flex-grow line-clamp-1" v-if="linearProjectDetail.project.targetDate">{{
              moment(linearProjectDetail.project.targetDate).format("DD/MM/YYYY")
            }}
          </div>
          <div v-else>...</div>
        </div>

        <div class="flex gap-2 mt-2" v-if="linearProjectDetail.project.lead">
          <div class="w-24 text-xs flex-none">Lead</div>
          <div class="flex-grow line-clamp-1 font-bold">
            {{ linearProjectDetail.project.lead.name }}
          </div>
        </div>
        <div class="flex gap-2 mt-2">
          <div class="w-24 text-xs flex-none">Members</div>
          <div class="flex-grow">
            <div v-for="member in linearProjectDetail.project.members.nodes">
              {{ member.name }}
            </div>
          </div>
        </div>

        <!-- -- milestones -->
        <div v-if="linearProjectDetail.project.projectMilestones.nodes.length != 0"
             class="flex gap-2 mt-2 border-t-[1px] border-gray-300 dark:border-gray-600 py-4">
          <div class="w-24 text-xs flex-none">Milestones</div>
          <div class="flex-grow">
            <div v-for="milestone in linearProjectDetail.project.projectMilestones.nodes">
              {{ milestone.name }} <span
                v-if="milestone.targetDate"> -> {{ moment(milestone.targetDate).format("DD/MM/YYYY") }}</span>
            </div>
          </div>
        </div>

        <!-- -- project updates -->
        <div v-if="linearProjectDetail.project.projectUpdates.nodes.length != 0"
             class="flex gap-2 mt-2 border-t-[1px] border-gray-300 dark:border-gray-600 py-4">
          <div class="flex-none w-24 text-xs">Updates</div>
          <div class="flex-grow">
            <div v-for="projectUpdate in linearProjectDetail.project.projectUpdates.nodes" class="mb-4">
              <div class="text-xs">
                <Timeago :datetime="projectUpdate.createdAt"/>
              </div>
              <div class="font-bold mt-2 text-xs">{{ projectUpdate.health }}</div>
              <div id="descriptionWrapper">
                <div class="">{{ projectUpdate.body }}</div>
              </div>
            </div>
          </div>
        </div>


      </div>
    </div>
    <div v-else>
      <br>
      <loading-spinner/>
      <br><br>&nbsp;
    </div>
  </div>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import {getLinearProjectDetail} from "@/api/query/organisation/getLinearProjects";
import {useAppStore} from "@/stores/app";
import moment from "moment/moment";
import LoadingSpinner from "@/components/common/loadingSpinner.vue";
import RoadmapProjectLabelRenderer from "@/_lodocio/components/roadmap/roadmapProjectLabelRenderer.vue";
import {Timeago} from 'vue2-timeago';

const props = defineProps<{ projectId: string }>();
const appStore = useAppStore();
const linearProjectDetail = ref(undefined);
const linearProjectDetailLoading = ref<boolean>(true);

onMounted((): void => {
  void loadLinearProjectDetail();
});

async function loadLinearProjectDetail() {
  if (appStore.project && props.projectId !== '') {
    linearProjectDetailLoading.value = true;
    linearProjectDetail.value = await getLinearProjectDetail(appStore.project.id, props.projectId);
    linearProjectDetailLoading.value = false;
  }
}

</script>

<style scoped>

</style>