/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {defineStore} from 'pinia'
import {getProjectById} from "@/api/query/model/getProject";
import type {
  Command, Documentor,
  DomainModel,
  Enum,
  Lists,
  ModelStatusCollection,
  Project,
  Query,
  Template
} from "@/api/query/interface/model";
import {getTemplateById} from "@/api/query/model/getTemplate";
import {getEnumById} from "@/api/query/model/getEnum";
import {getDomainModelById} from "@/api/query/model/getDomainModel";
import {getQueryById} from "@/api/query/model/getQuery";
import {getCommandById} from "@/api/query/model/getCommand";
import {getLists} from "@/api/query/model/getEnumValues";
import {getMasterTemplateById} from "@/api/query/user/getMasterTemplate";
import type {UserMasterTemplate} from "@/api/query/interface/user";
import {getUserMasterTemplates} from "@/api/query/user/getUserMasterTemplates";
import {getModelStatusByProject} from "@/api/query/model/getModelStatus";
import {getDocumentor} from "@/api/query/model/getDocumentor";

export type ModelState = {

  toastLifeTime: number;
  lists: Lists;

  isProjectLoading: boolean;
  project?: Project;
  projectId: number;

  templateLoading: boolean;
  templateReloading: boolean;
  template?: Template;
  templateSelectedId: number;

  domainModelLoading: boolean;
  domainModelReloading: boolean;
  domainModel?: DomainModel;
  domainModelSelectedId: number;

  enumLoading: boolean;
  enumReloading: boolean;
  enum?: Enum;
  enumSelectedId: number;

  queryLoading: boolean;
  queryReloading: boolean;
  query?: Query;
  querySelectedId: number;

  commandLoading: boolean;
  commandReloading: boolean;
  command?: Command;
  commandSelectedId: number;

  masterTemplates: Array<UserMasterTemplate>;
  isMasterTemplatesLoading: boolean;
  masterTemplateLoading: boolean;
  masterTemplateReloading: boolean;
  masterTemplate?: UserMasterTemplate;
  masterTemplateSelectedId: number;

  modelStatus: ModelStatusCollection;

  showDocumentor: boolean;
  documentorLoading: boolean;
  documentorId: number;
  documentorType: string;
  documentorSubjectId: number;
  documentor?: Documentor;
}

export const useModelStore = defineStore({
  id: "model",
  state: (): ModelState => ({

    toastLifeTime: 3000,
    lists: {
      fetchTypes: [],
      attributeTypes: [],
      orderTypes: [],
      associationTypes: [],
      templateTypes: []
    },

    // project -----------------------------------------------------
    isProjectLoading: false,
    project: undefined,
    projectId: 0,

    // templates ---------------------------------------------------
    templateLoading: false,
    templateReloading: false,
    template: undefined,
    templateSelectedId: 0,

    // domain model ------------------------------------------------
    domainModelLoading: false,
    domainModelReloading: false,
    domainModel: undefined,
    domainModelSelectedId: 0,

    // enum --------------------------------------------------------
    enumLoading: false,
    enumReloading: false,
    enum: undefined,
    enumSelectedId: 0,

    // query -------------------------------------------------------
    queryLoading: false,
    queryReloading: false,
    query: undefined,
    querySelectedId: 0,

    // command -----------------------------------------------------
    commandLoading: false,
    commandReloading: false,
    command: undefined,
    commandSelectedId: 0,

    // templates ---------------------------------------------------
    masterTemplates: [],
    isMasterTemplatesLoading: false,
    masterTemplateLoading: false,
    masterTemplateReloading: false,
    masterTemplate: undefined,
    masterTemplateSelectedId: 0,

    // modelstatus -------------------------------------------------
    modelStatus: {collection:[]},

    // documentor --------------------------------------------------
    showDocumentor: false,
    documentorLoading: false,
    documentorId: 0,
    documentorType: '',
    documentorSubjectId:0,
    documentor:undefined,

  }),
  actions: {

    async loadLists() {
      this.lists = await getLists();
    },

    // project -----------------------------------------------------
    async loadProject(id: number) {
      this.isProjectLoading = true;
      this.project = await getProjectById(id);
      this.projectId = this.project.id;
      await this.loadModelStatus();
      this.isProjectLoading = false;
    },
    async reLoadProject() {
      if (this.projectId !== 0) {
        this.isProjectLoading = true;
        this.project = await getProjectById(this.projectId);
        await this.loadModelStatus();
        this.isProjectLoading = false;
      }
    },

    // domainModel ----------------------------------------------------
    async loadDomainModel(id: number) {
      if (this.domainModelSelectedId !== id) {
        this.domainModelLoading = true;
        this.domainModelSelectedId = id;
        this.domainModel = await getDomainModelById(id);
        this.domainModelLoading = false;
      }
    },
    async reLoadDomainModel() {
      this.domainModelReloading = true;
      this.domainModel = await getDomainModelById(this.domainModelSelectedId);
      this.domainModelReloading = false;
    },


    // template -----------------------------------------------------
    async loadTemplate(id: number) {
      if (this.templateSelectedId !== id) {
        this.templateLoading = true;
        this.templateSelectedId = id;
        this.template = await getTemplateById(id);
        this.templateLoading = false;
      }
    },
    async reLoadTemplate() {
      this.templateReloading = true;
      this.template = await getTemplateById(this.templateSelectedId);
      this.templateReloading = false;
    },

    // enum -----------------------------------------------------
    async loadEnum(id: number) {
      if (this.enumSelectedId !== id) {
        this.enumLoading = true;
        this.enumSelectedId = id;
        this.enum = await getEnumById(id);
        this.enumLoading = false;
      }
    },
    async reLoadEnum() {
      this.enumReloading = true;
      this.enum = await getEnumById(this.enumSelectedId);
      this.enumReloading = false;
    },

    // query -----------------------------------------------------
    async loadQuery(id: number) {
      if (this.querySelectedId !== id) {
        this.queryLoading = true;
        this.querySelectedId = id;
        this.query = await getQueryById(id);
        this.queryLoading = false;
      }
    },
    async reLoadQuery() {
      this.queryReloading = true;
      this.query = await getQueryById(this.querySelectedId);
      this.queryReloading = false;
    },

    // command ---------------------------------------------------
    async loadCommand(id: number) {
      if (this.commandSelectedId !== id) {
        this.commandLoading = true;
        this.commandSelectedId = id;
        this.command = await getCommandById(id);
        this.commandLoading = false;
      }
    },
    async reLoadCommand() {
      this.commandReloading = true;
      this.command = await getCommandById(this.commandSelectedId);
      this.commandReloading = false;
    },

    // master templates -------------------------------------------
    async loadMasterTemplates()
    {
      this.isMasterTemplatesLoading = true;
      this.masterTemplates = await getUserMasterTemplates();
      this.isMasterTemplatesLoading = false;
    },
    async loadMasterTemplate(id: number) {
      if (this.masterTemplateSelectedId !== id) {
        this.masterTemplateLoading = true;
        this.masterTemplateSelectedId = id;
        this.masterTemplate = await getMasterTemplateById(id);
        this.masterTemplateLoading = false;
      }
    },
    async reLoadMasterTemplate() {
      this.masterTemplateReloading = true;
      this.masterTemplate = await getMasterTemplateById(this.masterTemplateSelectedId);
      this.masterTemplateReloading = false;
    },

    // -- model status ----------------------------------------------
    async loadModelStatus() {
      this.modelStatus = await getModelStatusByProject(this.projectId);
    },

    // -- documentor -------------------------------------------------

    async loadDocumentor(id:number, type:string, subjectId: number, showDocumentor: boolean = true)
    {
      this.documentorLoading = true;
      this.showDocumentor = showDocumentor;
      this.documentorId = id;
      this.documentorType = type;
      this.documentorSubjectId = subjectId;
      this.documentor = await getDocumentor(type.toLowerCase(),subjectId);
      this.documentorLoading = false;
    },

    // -- reset store ------------------------------------------------

    resetStore() {
      this.domainModel = undefined;
      this.domainModelSelectedId = 0;
      this.enum = undefined;
      this.enumSelectedId = 0;
      this.command = undefined;
      this.commandSelectedId = 0;
      this.query = undefined;
      this.querySelectedId = 0;
      this.template = undefined;
      this.templateSelectedId = 0;
    }

  },
  getters: {
    isDomainModelFinal: (state) => {
      if(state.domainModel) {
        if(state.domainModel.documentor.status.isFinal) {
          return true;
        }
      }
      return false;
    },
    isEnumFinal: (state) => {
      if(state.enum) {
        if(state.enum.documentor.status.isFinal) {
          return true;
        }
      }
      return false;
    },
    isQueryFinal: (state) => {
      if(state.query) {
        if(state.query.documentor.status.isFinal) {
          return true;
        }
      }
      return false;
    },
    isCommandFinal: (state) => {
      if(state.command) {
        if(state.command.documentor.status.isFinal) {
          return true;
        }
      }
      return false;
    },
  },
});
