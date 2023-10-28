import {defineStore} from "pinia";

export type TrackerState = {
  trackerId: number;
  selectedTrackerItemId: number;
}

export const useTrackerStore = defineStore({
  id: "lodocio-tracker",
  state: (): TrackerState => ({
    trackerId: 0,
    selectedTrackerItemId: 0,
  }),
  actions: {},
  getters: {},
});
