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
import type {CacheIssue} from "@/api/query/interface/linear";

export type LinearState = {
  isLoading: boolean;
  cachedIssues: Array<CacheIssue>;
}

export const useLinearStore = defineStore({
  id: "linear",
  state: ():LinearState => ({
    isLoading: false,
    cachedIssues: [],
  }),
  actions: {
    async cacheIssuesByProject(id: number) {
      this.isLoading = true;
      this.cachedIssues = await getProjectIssues(id)
      this.isLoading = false;
    },
  },
  getters: {},
});