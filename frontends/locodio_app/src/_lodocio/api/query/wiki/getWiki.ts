/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {Wiki} from "@/_lodocio/api/interface/wiki";
import type {CacheIssue} from "@/api/query/interface/linear";

export async function getWikiByDocProject(projectId: number) {
  const response = await axios.get<Array<Wiki>>(`/doc/${projectId}/wiki`);
  return response.data;
}

export async function getWikiById(id: number) {
  const response = await axios.get<Wiki>(`/doc/wiki/${id}`);
  return response.data;
}

export async function getFullWikiById(id: number) {
  const response = await axios.get<Wiki>(`/doc/wiki/${id}/full`);
  return response.data;
}

export async function getWikiIssues(id: number): Promise<Array<CacheIssue>> {
  const response = await axios.get<Array<CacheIssue>>(`/doc/wiki/${id}/issues`);
  return response.data;
}
