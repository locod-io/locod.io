/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import type {Roadmap} from "@/api/query/interface/linear";
import moment from "moment/moment";

export function getMaxDateRoadmaps(roadmaps: Array<Roadmap>): Date {
  let currentMaxDate = moment();
  for (const roadmap of roadmaps) {
    let _targetDate = moment(getMaxDateRoadmap(roadmap));
    if (_targetDate.isAfter(currentMaxDate)) {
      currentMaxDate = _targetDate;
    }
  }
  return currentMaxDate.add(45, 'days').toDate();
}

export function getMinDateRoadmaps(roadmaps: Array<Roadmap>): Date {
  let currentMinDate = moment();
  for (const roadmap of roadmaps) {
    let _startDate = moment(getMinDateRoadmap(roadmap));
    if (_startDate.isBefore(currentMinDate)) {
      currentMinDate = _startDate;
    }
  }
  return currentMinDate.subtract(20, 'days').toDate();
}

export function getMaxDateRoadmap(roadmap: Roadmap): Date {
  let currentMaxDate = moment();
  for (const project of roadmap.projects) {
    if (project.targetDate !== '') {
      let _targetDate = moment(project.targetDate);
      if (_targetDate.isAfter(currentMaxDate)) {
        currentMaxDate = _targetDate;
      }
    }
  }
  return currentMaxDate.toDate();
}

export function getMinDateRoadmap(roadmap: Roadmap): Date {
  let currentMinDate = moment();
  for (const project of roadmap.projects) {
    if (project.startDate !== '') {
      let _startDate = moment(project.startDate);
      if (_startDate.isBefore(currentMinDate)) {
        currentMinDate = _startDate;
      }
    }
  }
  return currentMinDate.toDate();
}

export function getRowBarList(roadmap: Roadmap) {
  let _projects = [];
  for (const project of roadmap.projects) {
    let _project = {};
    if (project.startDate || project.targetDate) {
      if (project.startDate) {
        _project.startDate = moment(project.startDate).toDate();
        _project.hasStart = true;
      } else {
        _project.startDate = moment(project.targetDate).subtract(45, 'day').toDate();
        _project.hasStart = false;
      }
      if (project.targetDate) {
        _project.targetDate = moment(project.targetDate).toDate();
        _project.hasEnd = true;
      } else {
        _project.targetDate = moment(project.startDate).add(45, "day").toDate();
        _project.hasEnd = false;
      }
    } else {
      _project.startDate = moment().subtract(30, 'day').toDate();
      _project.hasStart = false;
      _project.targetDate = moment().add(30, "day").toDate();
      _project.hasEnd = false;
    }
    _project.status = project.state;
    _project.color = project.color;
    _project.ganttBarConfig = {};
    _project.ganttBarConfig.id = project.id;
    _project.ganttBarConfig.label = project.name;
    _project.ganttBarConfig.handles = false;
    _projects.push(_project);
  }
  return _projects;
}