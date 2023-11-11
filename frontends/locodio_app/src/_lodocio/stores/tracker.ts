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
  Tracker,
  TrackerNode,
  TrackerNodeGroup,
  TrackerStructure
} from "@/_lodocio/api/interface/tracker";
import {getFullTrackerById} from "@/_lodocio/api/query/tracker/getTracker";
import {syncTrackerStructure} from "@/_lodocio/api/command/tracker/syncTrackerStructure";
import type {SyncTrackerStructureCommand} from "@/_lodocio/api/command/tracker/syncTrackerStructure";
import {getFullTrackerNodeById, getNodeRelatedIssues} from "@/_lodocio/api/query/tracker/getTrackerNode";
import type {IssueCollection} from "@/api/query/interface/linear";

export type TrackerState = {
  trackerId: number;
  trackerIsLoading: boolean;
  trackerIsReloading: boolean;
  tracker?: Tracker;
  trackerNodeId: number;
  trackerNode?: TrackerNode;
  trackerNodeGroupId: number;
  trackerNodeIsLoading: boolean;
  trackerNodeIsReloading: boolean;
  trackerNodeGroup?: TrackerNodeGroup;
  trackerStructure?: TrackerStructure;
  trackerNodeRelatedIssues: IssueCollection;
}

export const useTrackerStore = defineStore({
  id: "lodocio-tracker",
  state: (): TrackerState => ({
    trackerId: 0,
    trackerIsLoading: false,
    trackerIsReloading: false,
    tracker: undefined,
    trackerNodeId: 0,
    trackerNode: undefined,
    trackerNodeGroupId: 0,
    trackerNodeGroup: undefined,
    trackerStructure: {nodes: [], groups: []},
    trackerNodeIsLoading: false,
    trackerNodeIsReloading: false,
    trackerNodeRelatedIssues: {collection: []},
  }),
  actions: {

    // -- tracker loading functions ----------------------------------

    async loadTracker(id: number) {
      this.trackerIsLoading = true;
      this.tracker = await getFullTrackerById(id);
      this.trackerId = id;
      if (this.tracker) {
        this.trackerStructure = this.tracker.structure;
      }
      this.trackerIsLoading = false;
    },
    async reloadTracker() {
      this.trackerIsReloading = true;
      this.tracker = await getFullTrackerById(this.trackerId);
      if (this.tracker) {
        this.trackerStructure = this.tracker.structure;
      }
      this.trackerIsReloading = false;
    },

    // -- node detail functions ---------------------------------------

    async selectNode(nodeId: number) {
      this.trackerNodeGroupId = 0;
      this.trackerNodeId = nodeId;
      await this.loadTrackerNode(nodeId);

    },
    async loadTrackerNode(id: number) {
      this.trackerNodeIsLoading = true;
      this.trackerNode = await getFullTrackerNodeById(id);
      this.trackerNodeRelatedIssues = await getNodeRelatedIssues(id);
      this.trackerNodeIsLoading = false;
    },
    async reloadTrackerNode() {
      if (this.trackerNodeId !== 0) {
        this.trackerNodeIsReloading = true;
        this.trackerNode = await getFullTrackerNodeById(this.trackerNodeId);
        this.trackerNodeRelatedIssues = await getNodeRelatedIssues(this.trackerNodeId);
        this.trackerNodeIsReloading = false;
      }
    },
    selectGroup(groupId: number): void {
      this.trackerNodeId = 0;
      this.trackerNodeGroupId = groupId;
    },

    // -- synchronisation functions -------------------------------------

    async renumberTree() {
      console.log('-- renumber tree');
      let counter = 0;
      if (this.tracker) {
        for (let i = 0; i < this.tracker.nodes.length; i++) {
          counter++
          this.tracker.nodes[i].number = counter.toString();
          this.tracker.nodes[i].level = 0;
        }
        for (let j = 0; j < this.tracker.groups.length; j++) {
          counter++;
          this.tracker.groups[j].number = counter.toString();
          this.tracker.groups[j].level = 0;
          let group = this.tracker.groups[j];
          this.numberGroup(group, counter.toString(), 0);
        }
      }
      await this.syncStructure();
    },
    numberGroup(group: TrackerNodeGroup, baseNumber: string, level: number) {
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
      this.trackerStructure = {nodes: [], groups: []};
      if (this.tracker) {
        console.log('-- compile structure');
        // -- take and convert the root elements of the tracker
        for (let i = 0; i < this.tracker.nodes.length; i++) {
          let _srcNode = this.tracker.nodes[i];
          let _node = shortenNode(_srcNode);
          this.trackerStructure.nodes.push(_node);
        }
        // -- shorten the groups
        for (let i = 0; i < this.tracker.groups.length; i++) {
          let _srcGroup = this.tracker.groups[i];
          let _group = shortenGroup(_srcGroup);
          this.trackerStructure.groups.push(_group);
        }
      }
    },
    async syncStructure() {
      this.compileStructure();
      if (this.tracker && this.trackerStructure) {
        console.log('-- sync structure');
        const command: SyncTrackerStructureCommand = {
          id: this.tracker.id,
          structure: this.trackerStructure
        }
        await syncTrackerStructure(command);
      }
      await this.reloadTracker();
    },
    async openGroups() {
      console.log('-- close all groups');
      if (this.tracker) {
        this.openGroup(this.tracker.groups, true);
        await this.renumberTree();
      }
    },
    async closeGroups() {
      console.log('-- close all groups');
      if (this.tracker) {
        this.openGroup(this.tracker.groups, false);
        await this.renumberTree();
      }
    },
    openGroup(groups: Array<TrackerNodeGroup>, isOpen: boolean) {
      for (let i = 0; i < groups.length; i++) {
        let group: TrackerNodeGroup = groups[i];
        group.isOpen = isOpen;
        if (group.groups.length != 0) {
          this.openGroup(group.groups, isOpen);
        }
      }
    },
    async openNodes() {
      if (this.tracker) {
        for (let i = 0; i < this.tracker.nodes.length; i++) {
          let _srcNode = this.tracker.nodes[i];
          this.openNode(_srcNode, true);
        }
        this.openNodeInGroups(this.tracker.groups, true);
        await this.renumberTree();
      }
    },
    async closeNodes() {
      if (this.tracker) {
        for (let i = 0; i < this.tracker.nodes.length; i++) {
          let _srcNode = this.tracker.nodes[i];
          this.openNode(_srcNode, false);
        }
        this.openNodeInGroups(this.tracker.groups, false);
        await this.renumberTree();
      }
    },
    openNodeInGroups(groups: Array<TrackerNodeGroup>, isOpen: boolean) {
      for (let i = 0; i < groups.length; i++) {
        let _group: TrackerNodeGroup = groups[i];
        for (let j = 0; j < _group.nodes.length; j++) {
          this.openNode(_group.nodes[j] as TrackerNode, isOpen);
        }
        if (_group.groups.length != 0) {
          this.openNodeInGroups(_group.groups, isOpen);
        }
      }
    },
    openNode(node: TrackerNode, isOpen: boolean) {
      node.isOpen = isOpen;
    },

    // -- remove element from tracker
    async removeNodeFromStructure(uuid: string) {
      console.log('-- delete node: ' + uuid);
      // -- search in the root elements
      let _isFound = false
      if (this.tracker) {
        for (let i = 0; i < this.tracker.nodes.length; i++) {
          let _srcElement = this.tracker.nodes[i];
          if (_srcElement.uuid == uuid) {
            this.tracker.nodes.splice(i, 1);
            _isFound = true;
          }
        }
        // -- search in groups for that element
        if (!_isFound) {
          for (let j = 0; j < this.tracker.groups.length; j++) {
            let group = this.tracker.groups[j];
            this.removeNodeFromGroup(group,uuid);
          }
        }
        // -- deselect all
        this.trackerNodeId = 0;
        this.trackerNodeGroupId = 0;
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
        this.removeNodeFromGroup(group.groups[j],uuid);
      }
    },

    // -- remove group from tracker
    async removeGroupFromStructure(uuid: string) {
      console.log('-- delete node group: ' + uuid);
      let _isFound = false;
      if (this.tracker) {
        for (let j = 0; j < this.tracker.groups.length; j++) {
          let _group = this.tracker.groups[j]
          if (_group.uuid == uuid) {
            this.tracker.groups.splice(j, 1);
            _isFound = true;
          }
          if (!_isFound) {
            this.removeGroupFromGroups(_group, uuid);
          }
        }
        // -- deselect all
        this.trackerNodeId = 0;
        this.trackerNodeGroupId = 0;
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

function shortenNode(node: TrackerNode): StructureNode {
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

function shortenGroup(group: TrackerNodeGroup): StructureGroup {
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