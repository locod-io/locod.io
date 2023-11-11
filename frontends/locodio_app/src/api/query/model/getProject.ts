/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {Project} from "@/api/query/interface/model";
import type {CacheIssue} from "@/api/query/interface/linear";

export async function getProjectById(id: number): Promise<Project> {
  const response = await axios.get<Project>(`/project/${id}`);
  return response.data;
}




export async function getProjectIssues(id: number): Promise<Array<CacheIssue>> {
  const response = await axios.get<Array<CacheIssue>>(`/model/project/${id}/issues`);
  return response.data;
}
