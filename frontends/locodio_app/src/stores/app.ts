/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {defineStore} from 'pinia'
import type {User, UserProjects} from "@/api/query/interface/user";
import {getUser} from "@/api/query/user/getUser";
import {getUserProjects} from "@/api/query/user/getUserProjects";
import type {Organisation, Project} from "@/api/query/interface/model";
import type {ChangeThemeCommand} from "@/api/command/interface/userCommands";
import {changeTheme} from "@/api/command/user/changeTheme";
import {getProjectById} from "@/api/query/model/getProject";
import {getLinearProjects} from "@/api/query/organisation/getLinearProjects";
import type {Team, Project as LinearProject} from "@/api/query/interface/linear";
import {getLinearRoadmaps} from "@/api/query/organisation/getLinearRoadmaps";

export type AppState = {
  backgroundColor: string;
  toastLifeTime: number;
  isLoading: boolean;
  configLoaded: boolean;
  user?: User;
  userProjects?: UserProjects;
  organisation?: Organisation;
  projectIsLoading: boolean;
  projectId: number;
  project?: Project;
  theme: 'light' | 'dark';
}

// @ts-ignore
export const useAppStore = defineStore({
  id: "app",
  state: (): AppState => ({
    backgroundColor: '#EFF3F8', // 333544
    toastLifeTime: 3000,
    isLoading: false,
    configLoaded: false,
    user: undefined,
    userProjects: undefined,
    organisation: undefined,
    projectIsLoading: false,
    projectId: 0,
    project: undefined,
    theme: "light",
  }),
  actions: {
    async loadUser() {
      this.isLoading = true;
      this.user = await getUser();
      if(this.user) {
        this.setTheme(this.user.theme);
        const delay = (ms: number | undefined) => new Promise((resolve) => setTimeout(resolve, ms));
        await delay(500);
      }
      if (this.user && this.userProjects) {
        this.configLoaded = true;
      }
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
    async loadFullProject(id: number) {
      this.projectIsLoading = true;
      this.projectId = id;
      this.project = await getProjectById(id);
      this.projectIsLoading = false;
    },
    setCurrentWorkspace(organisation: Organisation, project: Project) {
      this.organisation = organisation;
      this.project = project;
      void this.loadFullProject(project.id);
    },
    async setCurrentWorkspaceById(organisationId: number, projectId: number) {
      if (this.userProjects) {
        for (const organisation of this.userProjects?.collection) {
          if (organisationId === organisation.id) {
            this.organisation = organisation;
            for (const project of organisation.projects) {
              if (project.id === projectId) {
                // @ts-ignore
                this.projectId = projectId;
                this.project = project;
                await this.loadFullProject(project.id);
                break;
              }
            }
            // -- get some linear information
            // @ts-ignore
            this.organisation.relatedProjects = await getLinearProjects(organisationId);
            // @ts-ignore
            this.organisation.relatedRoadmaps = await getLinearRoadmaps(organisationId);
            // @ts-ignore
            this.organisation.teams = this.getUniqueTeams(this.organisation.relatedProjects);
            break;
          }
        }
      }
    },
    getUniqueTeams(projects:Array<LinearProject>): Array<Team> {
      let _result = [];
      for (const _project of projects) {
        for (const _team of _project.teams) {
          let _hasFound = false;
          for (const _existingTeam of _result) {
            if(_existingTeam.id === _team.id) {
              _hasFound = true;
            }
          }
          if(!_hasFound) {
            _result.push(_team);
          }
        }
      }
      return _result;
    },
    setTheme(theme: string): void {
      let themeElement = document.getElementById('theme-link');
      if (theme === 'light') {
        this.theme = "light";
        // @ts-ignore
        themeElement.setAttribute('href', themeElement.getAttribute('href').replace('dark', 'light'));
        this.backgroundColor = "#EFF3F8";
        document.documentElement.classList.remove('dark');
      } else {
        this.theme = "dark";
        // @ts-ignore
        themeElement.setAttribute('href', themeElement.getAttribute('href').replace('light', 'dark'));
        this.backgroundColor = "#282936";
        document.documentElement.classList.add('dark');
      }
      if (this.user) {
        const command: ChangeThemeCommand = {id: this.user.id, theme: theme};
        void changeTheme(command);
      }
    },
    resetStore() {
      this.organisation = undefined;
      this.project = undefined;
    }
  },
  getters: {},
});
