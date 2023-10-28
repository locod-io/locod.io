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
  Module,
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
import {getDocumentorRelatedIssues} from "@/api/query/model/getDocumentorRelatedIssues";
import type {AuditItem} from "@/api/query/interface/audit";
import {getAuditTrail} from "@/api/query/audit/getAuditTrail";

export type ModelState = {

  toastLifeTime: number;
  lists: Lists;

  isProjectLoading: boolean;
  project?: Project;
  projectId: number;
  isAuditTrailLoading: boolean;

  templateLoading: boolean;
  templateReloading: boolean;
  template?: Template;
  templateSelectedId: number;

  moduleLoading: boolean;
  moduleReloading: boolean;
  module?: Module;
  moduleSelectedId: number;
  moduleExtendedView: boolean;
  moduleDocumentor?: Documentor;
  moduleAuditTrails: Array<AuditItem>;

  domainModelLoading: boolean;
  domainModelReloading: boolean;
  domainModel?: DomainModel;
  domainModelSelectedId: number;
  domainModelExtendedView: boolean;
  domainModelDocumentor?: Documentor;
  domainModelAuditTrails: Array<AuditItem>;

  enumLoading: boolean;
  enumReloading: boolean;
  enum?: Enum;
  enumSelectedId: number;
  enumExtendedView: boolean;
  enumDocumentor?: Documentor,
  enumAuditTrails: Array<AuditItem>;

  queryLoading: boolean;
  queryReloading: boolean;
  query?: Query;
  querySelectedId: number;
  queryExtendedView: boolean;
  queryDocumentor?: Documentor,
  queryAuditTrails: Array<AuditItem>;

  commandLoading: boolean;
  commandReloading: boolean;
  command?: Command;
  commandSelectedId: number;
  commandExtendedView: boolean;
  commandDocumentor?: Documentor,
  commandAuditTrails: Array<AuditItem>;

  masterTemplates: Array<UserMasterTemplate>;
  isMasterTemplatesLoading: boolean;
  masterTemplateLoading: boolean;
  masterTemplateReloading: boolean;
  masterTemplate?: UserMasterTemplate;
  masterTemplateSelectedId: number;

  modelStatus: ModelStatusCollection;

  showDocumentor: boolean;
  documentorLoading: boolean;
  documentorIssuesLoading: boolean;
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

    isAuditTrailLoading: false,

    // project -----------------------------------------------------
    isProjectLoading: false,
    project: undefined,
    projectId: 0,

    // templates ---------------------------------------------------
    templateLoading: false,
    templateReloading: false,
    template: undefined,
    templateSelectedId: 0,

    // module ------------------------------------------------------
    moduleLoading: false,
    moduleReloading: false,
    module: undefined,
    moduleSelectedId: 0,
    moduleExtendedView: false,
    moduleDocumentor: undefined,
    moduleAuditTrails: [],

    // domain model ------------------------------------------------
    domainModelLoading: false,
    domainModelReloading: false,
    domainModel: undefined,
    domainModelSelectedId: 0,
    domainModelExtendedView: false,
    domainModelDocumentor: undefined,
    domainModelAuditTrails: [],

    // enum --------------------------------------------------------
    enumLoading: false,
    enumReloading: false,
    enum: undefined,
    enumSelectedId: 0,
    enumExtendedView: false,
    enumDocumentor: undefined,
    enumAuditTrails: [],

    // query -------------------------------------------------------
    queryLoading: false,
    queryReloading: false,
    query: undefined,
    querySelectedId: 0,
    queryExtendedView: false,
    queryDocumentor: undefined,
    queryAuditTrails: [],

    // command -----------------------------------------------------
    commandLoading: false,
    commandReloading: false,
    command: undefined,
    commandSelectedId: 0,
    commandExtendedView: false,
    commandDocumentor: undefined,
    commandAuditTrails: [],

    // templates ---------------------------------------------------
    masterTemplates: [],
    isMasterTemplatesLoading: false,
    masterTemplateLoading: false,
    masterTemplateReloading: false,
    masterTemplate: undefined,
    masterTemplateSelectedId: 0,

    // modelstatus -------------------------------------------------
    modelStatus: {collection: []},

    // documentor --------------------------------------------------
    showDocumentor: false,
    documentorLoading: false,
    documentorIssuesLoading: false,
    documentorId: 0,
    documentorType: '',
    documentorSubjectId: 0,
    documentor: undefined,

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
        this.domainModelDocumentor = this.domainModel?.documentor;
        if (this.domainModelExtendedView) {
          await this.loadAuditTrails('domain-model', id);
        }
        this.domainModelLoading = false;
      }
    },
    async reLoadDomainModel() {
      this.domainModelReloading = true;
      this.domainModel = await getDomainModelById(this.domainModelSelectedId);
      this.domainModelDocumentor = this.domainModel?.documentor;
      if (this.domainModelExtendedView) {
        await this.loadAuditTrails('domain-model', this.domainModelSelectedId);
      }
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
        this.enumDocumentor = this.enum?.documentor;
        if (this.enumExtendedView) {
          await this.loadAuditTrails('enum', id);
        }
        this.enumLoading = false;
      }
    },
    async reLoadEnum() {
      this.enumReloading = true;
      this.enum = await getEnumById(this.enumSelectedId);
      this.enumDocumentor = this.enum?.documentor;
      if (this.enumExtendedView) {
        await this.loadAuditTrails('enum', this.enumSelectedId);
      }
      this.enumReloading = false;
    },

    // query -----------------------------------------------------
    async loadQuery(id: number) {
      if (this.querySelectedId !== id) {
        this.queryLoading = true;
        this.querySelectedId = id;
        this.query = await getQueryById(id);
        this.queryDocumentor = this.query?.documentor;
        if (this.queryExtendedView) {
          await this.loadAuditTrails('query', id);
        }
        this.queryLoading = false;
      }
    },
    async reLoadQuery() {
      this.queryReloading = true;
      this.query = await getQueryById(this.querySelectedId);
      this.queryDocumentor = this.query?.documentor;
      if (this.queryExtendedView) {
        await this.loadAuditTrails('query', this.querySelectedId);
      }
      this.queryReloading = false;
    },

    // command ---------------------------------------------------
    async loadCommand(id: number) {
      if (this.commandSelectedId !== id) {
        this.commandLoading = true;
        this.commandSelectedId = id;
        this.command = await getCommandById(id);
        this.commandDocumentor = this.command?.documentor;
        if (this.commandExtendedView) {
          await this.loadAuditTrails('command', id);
        }
        this.commandLoading = false;
      }
    },
    async reLoadCommand() {
      this.commandReloading = true;
      this.command = await getCommandById(this.commandSelectedId);
      this.commandDocumentor = this.command?.documentor;
      if (this.commandExtendedView) {
        await this.loadAuditTrails('command', this.commandSelectedId);
      }
      this.commandReloading = false;
    },

    // master templates -------------------------------------------
    async loadMasterTemplates() {
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

    async loadDocumentor(
      id: number,
      type: string,
      subjectId: number,
      showDocumentor: boolean = true,
      loadAsGeneral: boolean = true
    ) {
      this.documentorLoading = true;
      this.showDocumentor = showDocumentor;
      this.documentorId = id;
      this.documentorType = type;
      this.documentorSubjectId = subjectId;
      const _documentor: Documentor = await getDocumentor(type.toLowerCase(), subjectId);
      this.documentor = _documentor;
      switch (type) {
        case 'module':
          this.moduleDocumentor = _documentor;
          break;
        case 'domain-model':
          this.domainModelDocumentor = _documentor;
          break;
        case 'enum':
          this.enumDocumentor = _documentor;
          break;
        case 'query':
          this.queryDocumentor = _documentor;
          break;
        case 'command':
          this.commandDocumentor = _documentor;
          break;
      }
      this.documentorLoading = false;
    },

    async loadDocumentorIssueDetails(type: string, id: number) {
      this.documentorIssuesLoading = true;
      const issues = await getDocumentorRelatedIssues(id);
      switch (type) {
        case 'domain-model':
          if (this.domainModelDocumentor) this.domainModelDocumentor.linearIssuesDetails = issues;
          break;
        case 'enum':
          if (this.enumDocumentor) this.enumDocumentor.linearIssuesDetails = issues;
          break;
        case 'query':
          if (this.queryDocumentor) this.queryDocumentor.linearIssuesDetails = issues;
          break;
        case 'command':
          if (this.commandDocumentor) this.commandDocumentor.linearIssuesDetails = issues;
          break;
        default:
          if (this.moduleDocumentor) this.moduleDocumentor.linearIssuesDetails = issues;
          break;
      }
      this.documentorIssuesLoading = false;
    },

    async loadAuditTrails(type: string, id: number) {
      this.isAuditTrailLoading = true;
      const _auditTrail = await getAuditTrail(type, id);
      switch (type) {
        case 'domain-model':
          this.domainModelAuditTrails = _auditTrail;
          break;
        case 'enum':
          this.enumAuditTrails = _auditTrail;
          break;
        case 'query':
          this.queryAuditTrails = _auditTrail;
          break;
        case 'command':
          this.commandAuditTrails = _auditTrail;
          break;
        default:
          this.moduleAuditTrails = _auditTrail;
          break;
      }
      this.isAuditTrailLoading = false;
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
      if (state.domainModel) {
        if (state.domainModel.documentor.status.isFinal) {
          return true;
        }
      }
      return false;
    },
    isEnumFinal: (state) => {
      if (state.enum) {
        if (state.enum.documentor.status.isFinal) {
          return true;
        }
      }
      return false;
    },
    isQueryFinal: (state) => {
      if (state.query) {
        if (state.query.documentor.status.isFinal) {
          return true;
        }
      }
      return false;
    },
    isCommandFinal: (state) => {
      if (state.command) {
        if (state.command.documentor.status.isFinal) {
          return true;
        }
      }
      return false;
    },
  },
});
