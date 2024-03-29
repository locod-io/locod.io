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
  <div class="detailWrapper">
    <div class="detail">
      <div>

        <!-- toolbar ------------------------------------------------------------ -->
        <div class="flex gap-2 border-b-[1px] border-gray-300 dark:border-gray-600 h-12">
          <div class="flex-none py-2 pl-2">
            <Button
                v-if="!isSaving"
                @click="change"
                icon="pi pi-save"
                label="SAVE"
                class="p-button-success p-button-sm w-32"/>
            <Button
                v-else
                disabled
                icon="pi pi-spin pi-spinner"
                label="SAVE"
                class="p-button-success p-button-sm w-32"/>
          </div>
          <div class="flex-none  py-2 pl-2">
            <div class="flex">
              <div>
                <Button
                    @click="reload"
                    v-if="!projectStore.trackerDetailLoading"
                    icon="pi pi-refresh"
                    class="p-button-sm"/>
                <Button
                    v-else
                    disabled
                    icon="pi pi-spin pi-spinner"
                    class="p-button-sm"/>
              </div>
            </div>
          </div>
          <div class="flex-grow">&nbsp;</div>
        </div>

        <!-- form ------------------------------------------------------------------------------------------------ -->
        <DetailWrapper :estate-height="125">
          <div class="py-2 px-4 border-b-[1px] border-gray-300 dark:border-gray-600 h-24">
            <div class="flex gap-2 mt-3" v-on:keyup.enter="change">
              <div class="flex-grow">
                <!-- name -->
                <div><label class="text-sm font-bold">Name *</label></div>
                <div>
                      <span class="p-input-icon-right w-full">
                        <InputText class="w-full p-inputtext-sm"
                                   placeholder="Name"
                                   v-model="command.name"/>
                        <i v-if="!v$.name.$invalid" class="pi pi-check text-green-600"/>
                        <i v-if="v$.name.$invalid" class="pi pi-times text-red-600"/>
                      </span>
                </div>
              </div>
              <div class="flex-none w-32">
                <div><label class="text-sm">Code *</label></div>
                <div>
                  <span class="p-input-icon-right w-full">
                      <InputText class="w-full p-inputtext-sm"
                                 placeholder="code"
                                 v-model="command.code"/>
                      <i v-if="!v$.code.$invalid" class="pi pi-check text-green-600"/>
                      <i v-if="v$.code.$invalid" class="pi pi-times text-red-600"/>
                    </span>
                </div>
              </div>
              <div class="flex-none w-8">
                <div class="mt-6">
                  <ColorPicker v-model="command.color"></ColorPicker>
                </div>
              </div>
              <div class="flex-none w-32">
                <div><label class="text-sm">Color *</label></div>
                <div>
                  <span class="p-input-icon-right w-full">
                     <InputText class="w-full p-inputtext-sm" v-model="command.color"></InputText>
                     <i v-if="!v$.color.$invalid" class="pi pi-check text-green-600"/>
                     <i v-if="v$.color.$invalid" class="pi pi-times text-red-600"/>
                  </span>
                </div>
              </div>

            </div>
          </div>
          <div class="flex flex-row">

            <div class="basis-1/2">

              <!-- description -->
              <div class="flex border-b-[1px] border-gray-300 dark:border-gray-600 h-12 p-4 font-bold text-sm">
                Sharing settings
              </div>
              <div class="flex gap-2 mt-4">
                <div class="flex-none w-16 text-right">
                  <div class="mt-1 text-sm">SlugId *</div>
                </div>
                <div class="flex-grow">
                  <span class="p-input-icon-right w-full">
                     <InputText class="w-full p-inputtext-sm" v-model="command.slug"></InputText>
                     <i v-if="!v$.slug.$invalid" class="pi pi-check text-green-600"/>
                     <i v-if="v$.slug.$invalid" class="pi pi-times text-red-600"/>
                  </span>
                </div>
                <div class="flex-none mt-1 pr-2">
                  <random-button @click="command.slug = generateRandomString()"/>
                </div>
              </div>

              <div class="flex gap-4 mt-2 text-sm">
                <div class="w-16 text-right mt-0.5">Public?</div>
                <div><InputSwitch v-model="command.isPublic"/></div>
                <div class="mt-0.5" v-if="command.isPublic">Only final items?</div>
                <div v-if="command.isPublic"><InputSwitch v-model="command.showOnlyFinalNodes"/></div>
              </div>

              <div class="text-sm p-4 flex gap-2">
                <div class="flex-none">
                  <a :href="sharingUrl" target="_blank">
                    <i class="pi pi-link"/>
                  </a>
                </div>
                <div class="flex-grow line-clamp-1">
                  <a :href="sharingUrl" target="_blank">{{sharingUrl}}</a>
                </div>
                <div class="flex-none">
                  <copy-button @click="copyUrlToClipboard"/>
                </div>
              </div>

              <!-- description -->
              <div class="flex border-b-[1px] border-t-[1px] border-gray-300 dark:border-gray-600 h-12 p-4 font-bold text-sm">
                Description
              </div>
              <div class="text-sm">
                <simple-editor v-model="command.description"></simple-editor>
              </div>
            </div>

            <!-- workflow -->
            <div class="basis-1/2 border-l-[1px] border-gray-300 dark:border-gray-600">

              <!-- related teams for this tracker -->
              <div class="flex border-b-[1px] border-gray-300 dark:border-gray-600 h-12 p-4 font-bold text-sm">
                Related teams
              </div>
              <div class="border-b-[1px] border-gray-300 dark:border-gray-600 px-4 py-2 text-sm">
                <MultiSelect v-model="command.relatedTeams"
                             display="chip"
                             :options="appStore.organisation?.teams"
                             optionLabel="name"
                             placeholder="Select teams"
                             :maxSelectedLabels="4"
                             class="w-full"
                />
              </div>

              <div class="flex border-b-[1px] border-gray-300 dark:border-gray-600 h-12 p-4 font-bold text-sm">
                Workflow
              </div>
              <tracker-work-flow/>
            </div>
          </div>

        </DetailWrapper>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {onMounted, ref, computed} from "vue";
import DetailWrapper from "@/components/wrapper/detailWrapper.vue";
import {minValue, required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {useToast} from "primevue/usetoast";
import {useAppStore} from "@/stores/app";
import {useDocProjectStore} from "@/_lodocio/stores/project";
import type {ChangeTrackerCommand} from "@/_lodocio/api/command/tracker/changeTracker";
import {changeTracker} from "@/_lodocio/api/command/tracker/changeTracker";
import TrackerWorkFlow from "@/_lodocio/components/trackers/trackerWorkFlow.vue";
import SimpleEditor from "@/_common/editor/simpleEditor.vue";
import {generateRandomString} from "@/_lodocio/function/slugGenerator";
import RandomButton from "@/components/common/randomButton.vue";
import CopyButton from "@/_common/buttons/copyButton.vue";

const appStore = useAppStore();
const projectStore = useDocProjectStore();
const toaster = useToast();
const isSaving = ref<boolean>(false);
const dragging = ref<boolean>(false);
const command = ref<ChangeTrackerCommand>({
  id: projectStore.selectedTrackerId,
  name: projectStore.trackerDetail?.name ?? "",
  code: projectStore.trackerDetail?.code ?? "",
  color: projectStore.trackerDetail?.color.replace('#', '') ?? "",
  description: projectStore.trackerDetail?.description ?? "",
  relatedTeams: projectStore.trackerDetail ? projectStore.trackerDetail?.teams ?? projectStore.trackerDetail?.teams : [],
  slug: projectStore.trackerDetail?.slug ?? "",
  isPublic: projectStore.trackerDetail?.isPublic ?? false,
  showOnlyFinalNodes: projectStore.trackerDetail?.showOnlyFinalNode ?? true,
});

// -- mounted

onMounted((): void => {
  v$.value.$touch();
});

const sharingUrl = computed(() => {
  return 'https://'+window.location.hostname+'/v/'
      +appStore.organisation.slug+'/'
      +appStore.project.slug+'/'
      +projectStore.trackerDetail.slug;
});
function copyUrlToClipboard() {
  navigator.clipboard.writeText(sharingUrl.value);
}

// -- validation

const rules = {
  name: {required},
  code: {required},
  color: {required},
  slug: {required},
};
const v$ = useVuelidate(rules, command);

// -- change

async function change() {
  v$.value.$touch();
  isSaving.value = true;
  await changeTracker(command.value);
  toaster.add({
    severity: "success",
    summary: "Tracker saved",
    detail: "",
    life: appStore.toastLifeTime,
  });
  await projectStore.reloadProject();
  await projectStore.reloadTrackerDetail();
  isSaving.value = false;
}

async function reload() {
  await projectStore.reloadTrackerDetail();
  command.value.id = projectStore.trackerDetail?.id ?? 0;
  command.value.name = projectStore.trackerDetail?.name ?? "";
  command.value.code = projectStore.trackerDetail?.code ?? "";
  command.value.color = projectStore.trackerDetail?.color.replace('#', '') ?? "";
  command.value.description = projectStore.trackerDetail?.description ?? "";
}

</script>