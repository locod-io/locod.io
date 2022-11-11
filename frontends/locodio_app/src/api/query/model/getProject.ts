/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import type {Project} from "@/api/query/interface/model";
import axios from "axios";

export async function getProjectById(id: number) {
  const response = await axios.get<Project>(`/model/project/${id}`);
  return response.data;
}
