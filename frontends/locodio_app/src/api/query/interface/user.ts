/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import type {DocProject} from "@/_lodocio/api/interface/project";
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
  hasLocodio: boolean;
  hasLodocio: boolean;
  organisationPermissions: Array<UserOrganisationPermission>;
}

export interface Invitation {
  id:number;
  uuid: string;
  email: string;
  code: string;
  isUsed: boolean;
}

export interface UserOrganisation {
  id: number;
  uuid: string;
  code: string;
  name: string;
  color: string;
  slug: string;
  linearApiKey: string;
  figmaApiKey: string;
  projects: Array<UserProject>;
  teams: Array<Team>;
}

export interface UserOrganisationPermission {
  id: number;
  uuid: string;
  code: string;
  name: string;
  color: string;
  icon: string;
  slug: string;
  roles: Array<string>;
}


export interface UserProject {
  id: number;
  uuid: string;
  code: string;
  name: string;
  color: string;
  slug: string;
  logo: string;
  docProject: DocProject;
  gitRepository: string;
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
