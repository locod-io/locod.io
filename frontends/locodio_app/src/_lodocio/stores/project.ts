/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import {defineStore} from "pinia";
import type {Organisation} from "@/api/query/interface/model";
import type {DocProject} from "@/_lodocio/api/interface/project";
import {getOrganisationById} from "@/api/query/organisation/getOrganisation";
import {getDocProjectById} from "@/_lodocio/api/query/project/getProject";
import type {Tracker} from "@/_lodocio/api/interface/tracker";
import {getTrackerById} from "@/_lodocio/api/query/tracker/getTracker";
import type {Roadmap} from "@/api/query/interface/linear";
import {getProjectRoadmaps} from "@/api/query/project/getProjectRoadmaps";
import type {Wiki} from "@/_lodocio/api/interface/wiki";
import {getWikiByDocProject, getWikiById} from "@/_lodocio/api/query/wiki/getWiki";

export type ProjectState = {
  organisation?: Organisation;
  docProject?: DocProject;
  docProjectId: number;
  isLoading: boolean;

  // -- trackers
  selectedTrackerId: number;
  trackerDetailLoading: boolean;
  trackerDetail?: Tracker;

  // -- wiki
  selectedWikiId: number;
  wikiDetailLoading: boolean;
  wikiDetail?: Wiki;

  // -- roadmap
  roadmapsLoading: boolean;
  roadmaps: Array<Roadmap>;

  // -- releases
  // -- issues

}

export const useDocProjectStore = defineStore({
  id: "lodocio-project",
  state: (): ProjectState => ({
    organisation: undefined,
    docProject: undefined,
    docProjectId: 0,
    isLoading: false,
    selectedTrackerId: 0,
    trackerDetailLoading: false,
    trackerDetail: undefined,
    roadmapsLoading: false,
    roadmaps: [],
    selectedWikiId: 0,
    wikiDetailLoading: false,
    wikiDetail: undefined,
  }),
  actions: {
    async setCurrentWorkspace(organisation: Organisation, docProject: DocProject) {
      this.organisation = organisation;
      this.docProject = await getDocProjectById(docProject.id);
      const wikis: Array<Wiki> = await getWikiByDocProject(docProject.id);
      if (wikis.length > 0) {
        await this.loadWikiDetail(wikis[0].id);
      } else {
        this.resetWikiDetail();
      }
      this.docProjectId = docProject.id;
    },
    resetStore() {
      this.organisation = undefined;
      this.docProject = undefined;
    },
    async loadWorkspace(organisationId: number, docProjectId: number) {
      this.isLoading = true;
      this.organisation = await getOrganisationById(organisationId);
      this.docProjectId = docProjectId;
      this.docProject = await getDocProjectById(docProjectId);
      const wikis: Array<Wiki> = await getWikiByDocProject(docProjectId);
      if (wikis.length > 0) {
        await this.loadWikiDetail(wikis[0].id);
      } else {
        this.resetWikiDetail();
      }
      this.isLoading = false;
    },
    async reloadProject() {
      this.isLoading = true;
      this.docProject = await getDocProjectById(this.docProjectId);
      const wikis: Array<Wiki> = await getWikiByDocProject(this.docProjectId);
      if (wikis.length > 0) {
        await this.loadWikiDetail(wikis[0].id);
      } else {
        this.resetWikiDetail();
      }
      this.isLoading = false;
    },

    // -- tracker
    async loadTrackerDetail(id: number) {
      this.trackerDetailLoading = true;
      this.selectedTrackerId = id;
      this.trackerDetail = await getTrackerById(id);
      this.trackerDetailLoading = false;
    },
    async reloadTrackerDetail() {
      this.trackerDetail = await getTrackerById(this.selectedTrackerId);
    },

    // -- wiki
    async loadWikiDetail(id: number) {
      this.wikiDetailLoading = true;
      this.selectedWikiId = id;
      this.wikiDetail = await getWikiById(id);
      this.wikiDetailLoading = false;
    },
    resetWikiDetail() {
      this.wikiDetailLoading = true;
      this.selectedWikiId = 0;
      this.wikiDetail = undefined;
      this.wikiDetailLoading = false;
    },

    async reloadWikiDetail() {
      this.wikiDetail = await getWikiById(this.selectedWikiId);
    },

    // -- roadmaps
    async loadRoadmaps() {
      if (this.docProject) {
        this.roadmapsLoading = true;
        this.roadmaps = await getProjectRoadmaps(this.docProject.project.id);
        this.roadmapsLoading = false;
      }
    }

  },
  getters: {},
});
