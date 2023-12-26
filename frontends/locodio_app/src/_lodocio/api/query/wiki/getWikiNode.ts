/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {WikiNode} from "@/_lodocio/api/interface/wiki";
import type {IssueCollection} from "@/api/query/interface/linear";

export async function getWikiNodeById(id: number) {
  const response = await axios.get<WikiNode>(`/doc/wiki/node/${id}`);
  return response.data;
}

export async function getFullWikiNodeById(id: number) {
  const response = await axios.get<WikiNode>(`/doc/wiki/node/${id}/full`);
  return response.data;
}

export async function getNodeRelatedIssues(id: number) {
  const response = await axios.get<IssueCollection>(`/doc/wiki/node/${id}/related-issues`);
  return response.data;
}
