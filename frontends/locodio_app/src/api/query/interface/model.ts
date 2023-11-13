/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import type {UserMasterTemplate} from "@/api/query/interface/user";
import type {DocProject} from "@/_lodocio/api/interface/project";
import type {CacheIssue, Issue, Team, Project as LinearProject, Roadmap} from "@/api/query/interface/linear";

// -- documentation ------------------------------------------------------

export interface Documentation {
  items: DocumentationItem[];
}

export interface DocumentationItem {
  id: number;
  label: string;
  labelLevel: string;
  level: number;
  type: string;
  typeCode: string;
  item: Module | DomainModel | Enum | Query | Command;
  isEdit: boolean;
  artefactId: string;
}

// -- documentor ---------------------------------------------------------

export interface Documentor {
  id: number;
  uuid: string;
  type: string;
  description: string;
  image: string;
  schema: string;
  status: ModelStatus;
  linearIssues: Array<CacheIssue>;
  linearIssuesDetails: Array<Issue>;
}

// -- model status --------------------------------------------------------

export interface ModelStatusCollection {
  collection: Array<ModelStatus>;
}

export interface ModelStatus {
  id: number;
  uuid: string;
  name: string;
  color: string;
  isStart: boolean;
  isFinal: boolean;
  sequence: number;
  x: number;
  y: number;
  flowIn: Array<number>;
  flowOut: Array<number>;
  usages: number;
}

// -- workflow ------------------------------------------------------------

export interface ModelStatusWorkflow {
  workflow: Array<ModelStatusWorkflowStatus | ModelStatusWorkflowRelation>
}

export interface ModelStatusWorkflowStatus {
  id: string;
  label: string;
  type: string;
  data: ModelStatusWorkflowStatusData;
  position: ModelStatusWorkflowStatusPosition;
}

export interface ModelStatusWorkflowStatusData {
  color: string;
  type: 'input' | 'output' | 'default'
}

export interface ModelStatusWorkflowStatusPosition {
  x: number;
  y: number;
}

export interface ModelStatusWorkflowRelation {
  id: string;
  source: string;
  target: string;
  // todo
}

// -- project -----------------------------------------------------------

export interface Project {
  id: number;
  uuid: string;
  code: string;
  name: string;
  color: string;
  logo: string;
  slug: string;
  description: string;
  docProject: DocProject;
  organisation: Organisation;
  domainLayer: string;
  applicationLayer: string;
  infrastructureLayer: string;
  modelSettings?: ModelSettings;
  modules: Array<Module>;
  domainModels: Array<DomainModel>;
  enums: Array<Enum>;
  queries: Array<Query>;
  commands: Array<Command>;
  templates: Array<Template>;
  status: Array<ModelStatus>;
  relatedProjects: Array<LinearProject>;
  relatedRoadmaps: Array<Roadmap>;
}

export interface Organisation {
  id: number;
  uuid: string;
  code: string;
  name: string;
  color: string;
  slug: string;
  linearApiKey: string;
  projects: Array<Project>;
  relatedProjects: Array<LinearProject>;
  relatedRoadmaps: Array<Roadmap>;
  teams: Array<Team>;
}

export interface Module {
  id: number;
  uuid: string;
  artefactId: string;
  name: string;
  namespace: string;
  documentor: Documentor;
  usages: number;
}

export interface ModelSettings {
  id: number;
  uuid: string;
  domainLayer: string;
  applicationLayer: string;
  infrastructureLayer: string;
  teams: Array<Team>;
}

export interface DomainModel {
  id: number;
  uuid: string;
  artefactId: string;
  sequence: number;
  name: string;
  namespace: string;
  repository: string;
  attributes: Array<Attribute>;
  associations: Array<Association>;
  project: Project;
  module?: Module;
  documentor: Documentor;
}

export interface Attribute {
  id: number;
  uuid: string;
  artefactId: string;
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
  artefactId: string;
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
  artefactId: string;
  name: string;
  namespace: string;
  domainModel: DomainModel;
  options: Array<EnumOption>;
  project: Project;
  documentor: Documentor;
}

export interface EnumOption {
  id: number;
  uuid: string;
  artefactId: string;
  sequence: number;
  code: string;
  value: string;
}

export interface Query {
  id: number;
  uuid: string;
  artefactId: string;
  name: string;
  namespace: string;
  mapping: Array<Mapping>;
  domainModel: DomainModel;
  project: Project;
  documentor: Documentor;
}

export interface Command {
  id: number;
  uuid: string;
  artefactId: string;
  name: string;
  namespace: string;
  mapping: Array<Mapping>;
  domainModel: DomainModel;
  project: Project;
  documentor: Documentor;
}

export interface Mapping {
  name: string;
  type: string;
}

// -- templates ---------------------------------------------------------

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
  isGenerated: boolean;
  errorMessage: string;
}

// -- lists --------------------------------------------------------------
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
