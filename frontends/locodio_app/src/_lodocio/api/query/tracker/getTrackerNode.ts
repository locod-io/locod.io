/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {TrackerNode} from "@/_lodocio/api/interface/tracker";
import type {IssueCollection} from "@/api/query/interface/linear";

export async function getTrackerNodeById(id: number) {
  const response = await axios.get<TrackerNode>(`/doc/tracker/node/${id}`);
  return response.data;
}

export async function getFullTrackerNodeById(id: number) {
  const response = await axios.get<TrackerNode>(`/doc/tracker/node/${id}/full`);
  return response.data;
}

export async function getNodeRelatedIssues(id: number) {
  const response = await axios.get<IssueCollection>(`/doc/tracker/node/${id}/related-issues`);
  return response.data;
}
