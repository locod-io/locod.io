/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import {defineStore} from "pinia";
import type {
  StructureGroup,
  StructureNode,
  Wiki,
  WikiNode,
  WikiNodeGroup,
  WikiStructure
} from "@/_lodocio/api/interface/wiki";
import {getFullWikiById} from "@/_lodocio/api/query/wiki/getWiki";
import {syncWikiStructure} from "@/_lodocio/api/command/wiki/syncWikiStructure";
import type {SyncWikiStructureCommand} from "@/_lodocio/api/command/wiki/syncWikiStructure";
import {getFullWikiNodeById, getNodeRelatedIssues} from "@/_lodocio/api/query/wiki/getWikiNode";
import type {IssueCollection} from "@/api/query/interface/linear";

export type WikiState = {
  wikiId: number;
  wikiIsLoading: boolean;
  wikiIsReloading: boolean;
  wiki?: Wiki;
  wikiNodeId: number;
  wikiNode?: WikiNode;
  wikiNodeGroupId: number;
  wikiNodeIsLoading: boolean;
  wikiNodeIsReloading: boolean;
  wikiNodeGroup?: WikiNodeGroup;
  wikiStructure?: WikiStructure;
  wikiNodeRelatedIssues: IssueCollection;
}

export const useWikiStore = defineStore({
  id: "lodocio-wiki",
  state: (): WikiState => ({
    wikiId: 0,
    wikiIsLoading: false,
    wikiIsReloading: false,
    wiki: undefined,
    wikiNodeId: 0,
    wikiNode: undefined,
    wikiNodeGroupId: 0,
    wikiNodeGroup: undefined,
    wikiStructure: {nodes: [], groups: []},
    wikiNodeIsLoading: false,
    wikiNodeIsReloading: false,
    wikiNodeRelatedIssues: {collection: []},
  }),
  actions: {

    // -- wiki loading functions ----------------------------------

    async loadWiki(id: number) {
      this.wikiIsLoading = true;
      this.wiki = await getFullWikiById(id);
      this.wikiId = id;
      if (this.wiki) {
        this.wikiStructure = this.wiki.structure;
      }
      this.wikiIsLoading = false;
    },
    async reloadWiki() {
      this.wikiIsReloading = true;
      this.wiki = await getFullWikiById(this.wikiId);
      if (this.wiki) {
        this.wikiStructure = this.wiki.structure;
      }
      this.wikiIsReloading = false;
    },

    // -- store reset function ----------------------------------------

    reset(): void {
      this.wiki = undefined;
      this.wikiNodeId = 0;
      this.wikiNode = undefined;
      this.wikiNodeGroupId = 0;
      this.wikiNodeGroup = undefined;
      this.wikiStructure = {nodes: [], groups: []};
      this.wikiNodeRelatedIssues = {collection: []};
    },

    // -- node detail functions ---------------------------------------

    async selectNode(nodeId: number) {
      this.wikiNodeGroupId = 0;
      this.wikiNodeId = nodeId;
      await this.loadWikiNode(nodeId);

    },
    async loadWikiNode(id: number) {
      this.wikiNodeIsLoading = true;
      this.wikiNode = await getFullWikiNodeById(id);
      this.wikiNodeRelatedIssues = await getNodeRelatedIssues(id);
      this.wikiNodeIsLoading = false;
    },
    async reloadWikiNode() {
      if (this.wikiNodeId !== 0) {
        this.wikiNodeIsReloading = true;
        this.wikiNode = await getFullWikiNodeById(this.wikiNodeId);
        this.wikiNodeRelatedIssues = await getNodeRelatedIssues(this.wikiNodeId);
        this.wikiNodeIsReloading = false;
      }
    },
    selectGroup(groupId: number): void {
      this.wikiNodeId = 0;
      this.wikiNodeGroupId = groupId;
    },

    // -- synchronisation functions -------------------------------------

    async renumberTree() {
      console.log('-- renumber tree');
      let counter = 0;
      if (this.wiki) {
        for (let i = 0; i < this.wiki.nodes.length; i++) {
          counter++
          this.wiki.nodes[i].number = counter.toString();
          this.wiki.nodes[i].level = 0;
        }
        for (let j = 0; j < this.wiki.groups.length; j++) {
          counter++;
          this.wiki.groups[j].number = counter.toString();
          this.wiki.groups[j].level = 0;
          let group = this.wiki.groups[j];
          this.numberGroup(group, counter.toString(), 0);
        }
      }
      await this.syncStructure();
    },
    numberGroup(group: WikiNodeGroup, baseNumber: string, level: number) {
      let counter: number = 0;
      let _level = level + 1;
      for (let i: number = 0; i < group.nodes.length; i++) {
        counter++;
        group.nodes[i].number = baseNumber + '.' + counter;
        group.nodes[i].level = _level;
      }
      for (let j = 0; j < group.groups.length; j++) {
        counter++;
        let _group = group.groups[j];
        _group.number = baseNumber + '.' + counter;
        _group.level = _level;
        this.numberGroup(_group, _group.number, _level);
      }
    },
    compileStructure() {
      this.wikiStructure = {nodes: [], groups: []};
      if (this.wiki) {
        console.log('-- compile structure');
        // -- take and convert the root elements of the wiki
        for (let i = 0; i < this.wiki.nodes.length; i++) {
          let _srcNode = this.wiki.nodes[i];
          let _node = shortenNode(_srcNode);
          this.wikiStructure.nodes.push(_node);
        }
        // -- shorten the groups
        for (let i = 0; i < this.wiki.groups.length; i++) {
          let _srcGroup = this.wiki.groups[i];
          let _group = shortenGroup(_srcGroup);
          this.wikiStructure.groups.push(_group);
        }
      }
    },
    async syncStructure() {
      this.compileStructure();
      if (this.wiki && this.wikiStructure) {
        console.log('-- sync structure');
        const command: SyncWikiStructureCommand = {
          id: this.wiki.id,
          structure: this.wikiStructure
        }
        await syncWikiStructure(command);
      }
      await this.reloadWiki();
    },
    async openGroups() {
      console.log('-- close all groups');
      if (this.wiki) {
        this.openGroup(this.wiki.groups, true);
        await this.renumberTree();
      }
    },
    async closeGroups() {
      console.log('-- close all groups');
      if (this.wiki) {
        this.openGroup(this.wiki.groups, false);
        await this.renumberTree();
      }
    },
    openGroup(groups: Array<WikiNodeGroup>, isOpen: boolean) {
      for (let i = 0; i < groups.length; i++) {
        let group: WikiNodeGroup = groups[i];
        group.isOpen = isOpen;
        if (group.groups.length != 0) {
          this.openGroup(group.groups, isOpen);
        }
      }
    },
    async openNodes() {
      if (this.wiki) {
        for (let i = 0; i < this.wiki.nodes.length; i++) {
          let _srcNode = this.wiki.nodes[i];
          this.openNode(_srcNode, true);
        }
        this.openNodeInGroups(this.wiki.groups, true);
        await this.renumberTree();
      }
    },
    async closeNodes() {
      if (this.wiki) {
        for (let i = 0; i < this.wiki.nodes.length; i++) {
          let _srcNode = this.wiki.nodes[i];
          this.openNode(_srcNode, false);
        }
        this.openNodeInGroups(this.wiki.groups, false);
        await this.renumberTree();
      }
    },
    openNodeInGroups(groups: Array<WikiNodeGroup>, isOpen: boolean) {
      for (let i = 0; i < groups.length; i++) {
        let _group: WikiNodeGroup = groups[i];
        for (let j = 0; j < _group.nodes.length; j++) {
          this.openNode(_group.nodes[j] as WikiNode, isOpen);
        }
        if (_group.groups.length != 0) {
          this.openNodeInGroups(_group.groups, isOpen);
        }
      }
    },
    openNode(node: WikiNode, isOpen: boolean) {
      node.isOpen = isOpen;
    },

    // -- remove element from wiki
    async removeNodeFromStructure(uuid: string) {
      console.log('-- delete node: ' + uuid);
      // -- search in the root elements
      let _isFound = false
      if (this.wiki) {
        for (let i = 0; i < this.wiki.nodes.length; i++) {
          let _srcElement = this.wiki.nodes[i];
          if (_srcElement.uuid == uuid) {
            this.wiki.nodes.splice(i, 1);
            _isFound = true;
          }
        }
        // -- search in groups for that element
        if (!_isFound) {
          for (let j = 0; j < this.wiki.groups.length; j++) {
            let group = this.wiki.groups[j];
            this.removeNodeFromGroup(group, uuid);
          }
        }
        // -- deselect all
        this.wikiNodeId = 0;
        this.wikiNodeGroupId = 0;
        // --  renumber the tree, shorten and sync
        await this.renumberTree();
      }
    },
    removeNodeFromGroup(group: StructureGroup, uuid: string) {
      for (let i = 0; i < group.nodes.length; i++) {
        if (group.nodes[i].uuid == uuid) {
          group.nodes.splice(i, 1)
        }
      }
      // -- recursively search in the group
      for (let j = 0; j < group.groups.length; j++) {
        this.removeNodeFromGroup(group.groups[j], uuid);
      }
    },

    // -- remove group from wiki
    async removeGroupFromStructure(uuid: string) {
      console.log('-- delete node group: ' + uuid);
      let _isFound = false;
      if (this.wiki) {
        for (let j = 0; j < this.wiki.groups.length; j++) {
          let _group = this.wiki.groups[j]
          if (_group.uuid == uuid) {
            this.wiki.groups.splice(j, 1);
            _isFound = true;
          }
          if (!_isFound) {
            this.removeGroupFromGroups(_group, uuid);
          }
        }
        // -- deselect all
        this.wikiNodeId = 0;
        this.wikiNodeGroupId = 0;
        // --  renumber the tree, shorten and sync
        await this.renumberTree();
      }
    },
    removeGroupFromGroups(group: StructureGroup, uuid: string) {
      let _isFound = false;
      for (let j = 0; j < group.groups.length; j++) {
        let _group: StructureGroup = group.groups[j]
        if (_group.uuid == uuid) {

          console.log(j);
          console.log(_group);

          group.groups.splice(j, 1);
          _isFound = true;
        }
        if (!_isFound) {
          this.removeGroupFromGroups(_group, uuid);
        }
      }
    },
  },
  getters: {},
});

// -------------------------------------------------------------------------------
// -- general function to shorten the nodes, for synchronisation
// -------------------------------------------------------------------------------

function shortenNode(node: WikiNode): StructureNode {
  return {
    id: node.id,
    name: node.name,
    uuid: node.uuid,
    artefactId: node.artefactId,
    number: node.number,
    level: node.level,
    isOpen: node.isOpen,
  }
}

function shortenGroup(group: WikiNodeGroup): StructureGroup {
  let _group: StructureGroup = {
    id: group.id,
    name: group.name,
    uuid: group.uuid,
    number: group.number,
    artefactId: group.artefactId,
    isOpen: group.isOpen,
    level: group.level,
    nodes: [],
    groups: []
  };

  // -- loop over the nodes
  for (let i = 0; i < group.nodes.length; i++) {
    let _srcNode = group.nodes[i];
    let _node = shortenNode(_srcNode);
    _group.nodes.push(_node);
  }
  // -- loop over the groups
  for (let i = 0; i < group.groups.length; i++) {
    let _srcGroup = group.groups[i];
    let _groupShort = shortenGroup(_srcGroup);
    _group.groups.push(_groupShort);
  }

  return _group;
}