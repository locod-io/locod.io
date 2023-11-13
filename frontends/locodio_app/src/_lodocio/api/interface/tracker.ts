/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import type {CacheIssue} from "@/api/query/interface/linear";

export interface TrackerStructure {
  nodes: Array<StructureNode>;
  groups: Array<StructureGroup>;
}

export interface StructureNode {
  id: number;
  name: string;
  uuid: string;
  artefactId: number;
  number: string;
  level: number;
  isOpen: boolean;
}

export interface StructureGroup {
  id: number;
  name: string;
  uuid: string;
  number: string;
  artefactId: number;
  isOpen: boolean;
  level: number;
  nodes: Array<StructureNode>;
  groups: Array<StructureGroup>;
}

export interface Tracker {
  id: number;
  uuid: string;
  artefactId: string;
  sequence: number;
  code: string;
  name: string;
  color: string;
  description: string;
  slug: string;
  isPublic: boolean;
  showOnlyFinalNode: boolean;
  structure: TrackerStructure;
  workflow: Array<TrackerNodeStatus>;
  nodes: Array<TrackerNode>;
  groups: Array<TrackerNodeGroup>;
  relatedProjectDocument: relatedProjectDocument;
}

export interface relatedProjectDocument {
  title: string;
  relatedProjectId: string;
  relatedDocumentId: string;
}


export interface TrackerNodeStatus {
  id: number;
  uuid: string;
  artefactId: string;
  sequence: number;
  name: string;
  color: string;
  isStart: boolean;
  isFinal: boolean;
  flowIn: Array<number>;
  flowOut: Array<number>;
  x: number;
  y: number;
  usages: number;
}

export interface TrackerNode {
  id: number;
  uuid: string;
  artefactId: number;
  sequence: number;
  number: string;
  level: number;
  isOpen: boolean;
  name: string;
  description: string;
  finalAt: Date;
  finalBy: string;
  status: TrackerNodeStatus;
  relatedIssues: Array<CacheIssue>;
  files: Array<TrackerFile>;
  relatedProjectDocument: relatedProjectDocument;
}

export interface TrackerFile {
  id: number;
  uuid: string;
  artefactId: number;
  sequence: number;
  name: string;
}

export interface TrackerNodeGroup {
  id: number;
  uuid: string;
  name: string;
  level: number;
  number: string;
  artefactId: number;
  isOpen: boolean;
  nodes: Array<TrackerNode>;
  groups: Array<TrackerNodeGroup>;
  relatedProjectDocument: relatedProjectDocument;
}
