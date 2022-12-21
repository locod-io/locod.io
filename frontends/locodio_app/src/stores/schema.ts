/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {defineStore} from 'pinia'
import type {navigationItem} from "@/components/overview/model";
import type {Documentation, DomainModel} from "@/api/query/interface/model";
import {getDocumentation} from "@/api/query/model/getDocumentation";

export type SchemaState = {
  configuration: Array<navigationItem>;
  showModules: boolean;
  counter: number;
  isDocumentationLoading: boolean;
  documentation: Documentation;
}

export const useSchemaStore = defineStore({
  id: "schema",
  state: (): SchemaState => ({
    configuration: [],
    showModules: false,
    counter: 0,
    isDocumentationLoading: false,
    documentation: {items: []}
  }),
  actions: {
    incrementCounter() {
      this.counter++;
    },
    async loadDocumentation(projectId: number) {
      this.isDocumentationLoading = true;
      this.documentation = await getDocumentation(projectId);
      this.isDocumentationLoading = false;
    },
    async reloadDocumentation(projectId: number) {
      this.isDocumentationLoading = true;
      let openEditorCode = '';
      for (let docItem of this.documentation.items) {
        if(docItem.isEdit === true) {
          openEditorCode = docItem.labelLevel;
        }
      }
      this.documentation = await getDocumentation(projectId);
      for (let docItem of this.documentation.items) {
        if (docItem.labelLevel === openEditorCode) {
          docItem.isEdit = true;
        }
      }
      this.isDocumentationLoading = false;
    },
    closeEditors(exceptLevelLabel: string) {
      for (let docItem of this.documentation.items) {
        if (docItem.labelLevel !== exceptLevelLabel) {
          docItem.isEdit = false;
        }
      }
    }
  },
  getters: {
    getAttributesForModel: (state) =>
    {
      return (domainModelId: number) => state.documentation.items.find((item) => (item.id === domainModelId && item.type === 'domain-model')).item.attributes;
    },
    getAssociationsForModel: (state) => {
      return (domainModelId: number) => state.documentation.items.find((item) => (item.id === domainModelId && item.type === 'domain-model')).item.associations;
    },
  },
});