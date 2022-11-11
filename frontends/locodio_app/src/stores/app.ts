/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {defineStore} from 'pinia'
import type {User, UserProject, UserProjects} from "@/api/query/interface/user";
import {getUser} from "@/api/query/user/getUser";
import {getUserProjects} from "@/api/query/user/getUserProjects";
import type {Organisation, Project} from "@/api/query/interface/model";

export type AppState = {
  toastLifeTime: number;
  isLoading: boolean;
  configLoaded: boolean;
  user?: User;
  userProjects?: UserProjects;
  organisation?: Organisation;
  project?: Project;
}

export const useAppStore = defineStore({
  id: "app",
  state: (): AppState => ({
    toastLifeTime: 3000,
    isLoading: false,
    configLoaded: false,
    user: undefined,
    userProjects: undefined,
    organisation: undefined,
    project: undefined
  }),
  actions: {
    async loadUser() {
      this.isLoading = true;
      this.user = await getUser();
      if (this.user && this.userProjects) this.configLoaded = true;
      this.isLoading = false;
    },
    async loadUserProjects() {
      this.isLoading = true;
      this.userProjects = await getUserProjects();
      if (this.user && this.userProjects) this.configLoaded = true;
      this.isLoading = false;
    },
    async reloadUserProjects() {
      this.isLoading = true;
      this.userProjects = await getUserProjects();
      this.isLoading = false;
    },
    setCurrentWorkspace(organisation: Organisation, project: Project) {
      this.organisation = organisation;
      this.project = project;
    },
    setCurrentWorkspaceById(organisationId: number, projectId: number) {
      if (this.userProjects) {
        for (const organisation of this.userProjects?.collection) {
          if (organisationId === organisation.id) {
            this.organisation = organisation;
            for (const project of organisation.projects) {
              if (project.id === projectId) {
                // @ts-ignore
                this.project = project;
                break;
              }
            }
            break;
          }
        }
      }
    }
  },
  getters: {},
});
