/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import type {UserMasterTemplate} from "@/api/query/interface/user";

export interface Project {
  id: number;
  uuid: string;
  code: string;
  name: string;
  organisation: Organisation;
  domainLayer: string;
  applicationLayer: string;
  infrastructureLayer: string;
  domainModels: Array<DomainModel>
  enums: Array<Enum>
  queries: Array<Query>
  commands: Array<Command>
  templates: Array<Template>
}

export interface Organisation {
  id: number;
  uuid: string;
  code: string;
  name: string;
}

export interface DomainModel {
  id: number;
  uuid: string;
  sequence: number;
  name: string;
  namespace: string;
  repository: string;
  attributes: Array<Attribute>;
  associations: Array<Association>;
  project: Project;
}

export interface Attribute {
  id: number;
  uuid: string;
  sequence: 0,
  name: string;
  length: number;
  type: string; // type
  identifier: boolean;
  required: boolean;
  unique: boolean;
  make: boolean;
  change: boolean;
  enum: Enum;
}

export interface Association {
  id: number;
  uuid: string;
  type: string;
  mappedBy: string;
  inversedBy: string;
  fetch: string; // type
  orderBy: string;
  orderDirection: string;
  targetDomainModel: DomainModel;
  make: boolean;
  change: boolean;
  required: boolean;
}

export interface Enum {
  id: number;
  uuid: string;
  name: string;
  namespace: string;
  domainModel: DomainModel;
  options: Array<EnumOption>;
  project: Project;
}

export interface EnumOption {
  id: number;
  uuid: string;
  sequence: number;
  code: string;
  value: string;
}

export interface Query {
  id: number;
  uuid: string;
  name: string;
  namespace: string;
  mapping: Array<Mapping>;
  domainModel: DomainModel;
  project: Project;
}

export interface Command {
  id: number;
  uuid: string;
  name: string;
  namespace: string;
  mapping: Array<Mapping>;
  domainModel: DomainModel;
  project: Project;
}

export interface Template {
  id: number;
  uuid: string;
  name: string;
  type: string; // type
  language: string;
  template: string;
  masterTemplate?: UserMasterTemplate;
  linkedAt?: string;
  linkedAtNumber?: number;
}

export interface GeneratedTemplate {
  code: string;
}

export interface EnumValues {
  attributeTypes: Array<string>;
  fetchTypes: Array<string>;
  associationTypes: Array<string>;
  orderTypes: Array<string>;
  templateTypes: Array<string>;
}

export interface Lists {
  attributeTypes: Array<ListItem>;
  fetchTypes: Array<ListItem>;
  associationTypes: Array<ListItem>;
  orderTypes: Array<ListItem>;
  templateTypes: Array<ListItem>;
}

export interface ListItem {
  code: string;
  label: string;
}

export interface Mapping {
  name: string;
  type: string;
}
