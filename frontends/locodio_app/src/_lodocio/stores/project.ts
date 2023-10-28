import {defineStore} from "pinia";
import type {DocProject} from "@/_lodocio/api/query/interface/project";
import type {Organisation} from "@/api/query/interface/model";

export type ProjectState = {
  organisation?: Organisation;
  docProject?: DocProject;
  docProjectId: number;
}

export const useDocProjectStore = defineStore({
  id: "lodocio-project",
  state: (): ProjectState => ({
    organisation: undefined,
    docProject: undefined,
    docProjectId: 0,
  }),
  actions: {
    setCurrentWorkspace(organisation: Organisation, docProject: DocProject) {
      this.organisation = organisation;
      this.docProject = docProject;
      this.docProjectId = docProject.id;
    },
    resetStore() {
      this.organisation = undefined;
      this.docProject = undefined;
    }
  },
  getters: {},
});
