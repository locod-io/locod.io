/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import type {DocProject} from "@/_lodocio/api/query/interface/project";
import type {Team} from "@/api/query/interface/linear";

export interface User {
  id: number;
  uuid: string;
  firstname: string;
  email: string;
  lastname: string;
  color: string;
  initials: string;
  theme: string;
  organisationLabel: string;
  organisations: Array<UserOrganisation>;
}

export interface UserOrganisation {
  id: number;
  uuid: string;
  code: string;
  name: string;
  color: string;
  linearApiKey: string;
  projects: Array<UserProject>;
  teams: Array<Team>;
}

export interface UserProject {
  id: number;
  uuid: string;
  code: string;
  name: string;
  color: string;
  docProject: DocProject;
  domainLayer: string;
  applicationLayer: string;
  infrastructureLayer: string;
}

export interface UserProjects {
  collection: Array<UserOrganisation>;
}

export interface UserMasterTemplate {
  id: number;
  uuid: string;
  name: string;
  type: string; // type
  language: string;
  template: string;
  isPublic: boolean;
  description: string;
  tags: Array<string>;
}
