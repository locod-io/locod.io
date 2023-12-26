/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {defineStore} from 'pinia'
import {getProjectIssues} from "@/api/query/model/getProject";
import type {CacheIssue, Document} from "@/api/query/interface/linear";
import {getTrackerIssues} from "@/_lodocio/api/query/tracker/getTracker";
import {getWikiIssues} from "@/_lodocio/api/query/wiki/getWiki";

export type LinearState = {
  isLoading: boolean;
  cachedIssues: Array<CacheIssue>;
  cachedDocuments: Array<Document>;
}

export const useLinearStore = defineStore({
  id: "linear",
  state: (): LinearState => ({
    isLoading: false,
    cachedIssues: [],
    cachedDocuments: [],
  }),
  actions: {
    async cacheIssuesByProject(id: number) {
      this.isLoading = true;
      this.cachedIssues = await getProjectIssues(id);
      this.isLoading = false;
    },
    async cacheIssuesByTracker(id: number) {
      this.isLoading = true;
      this.cachedIssues = await getTrackerIssues(id);
      this.isLoading = false;
    },
    async cacheIssuesByWiki(id: number) {
      this.isLoading = true;
      this.cachedIssues = await getWikiIssues(id);
      this.isLoading = false;
    },
  },
  getters: {},
});