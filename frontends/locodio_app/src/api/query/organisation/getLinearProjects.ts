/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {Project} from "@/api/query/interface/linear";

export async function getLinearProjects(organisationId: number): Promise<Array<Project>> {
  const response = await axios.get<Array<Project>>(`/organisation/${organisationId}/linear-projects`);
  return response.data;
}

export async function getLinearProjectDetail(projectId: number, uuid: string): Promise<Array<Project>> {
  const response = await axios.get<Array<Project>>(`/project/${projectId}/linear-project/${uuid}`);
  return response.data;
}