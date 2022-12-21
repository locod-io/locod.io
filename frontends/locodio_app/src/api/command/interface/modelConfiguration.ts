import type {ModelStatusWorkflowStatusPosition} from "@/api/query/interface/model";

export interface DeleteModelStatusCommand {
  id: number;
}

export interface DeleteModuleCommand {
  id: number;
}

export interface ChangeDocumentorCommand {
  id: number;
  statusId: number;
  description: string;
}

export interface ChangeDocumentorStatusCommand {
  id: number;
  statusId: number;
}
export interface ChangeModelSettingsCommand {
  projectId: number;
  id: number;
  domainLayer: string;
  applicationLayer: string;
  infrastructureLayer: string;
}

export interface AddModuleCommand {
  projectId: number;
  name: string;
  namespace: string;
}

export interface ChangeModuleCommand {
  id: number;
  name: string;
  namespace: string;
}

export interface OrderModuleCommand {
  sequence: Array<number>;
}

export interface AddModelStatusCommand {
  projectId: number;
  name: string;
  color: string;
  isStart: boolean;
  isFinal: boolean;
}

export interface ChangeModelStatusCommand {
  id: number;
  name: string;
  color: string;
  isStart: boolean;
  isFinal: boolean;
}

export interface OrderModelStatusCommand {
  sequence: Array<number>;
}

export interface ModelStatusWorkflowCommand {
  workflow: Array<WorkflowItem>;
}

export interface WorkflowItem {
  id: string;
  label: string;
  position: ModelStatusWorkflowStatusPosition;
  flowIn: Array<string>;
  flowOut: Array<string>;
}