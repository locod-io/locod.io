/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {ModelStatusCollection} from "@/api/query/interface/model";

export async function getModelStatusByProject(projectId: number) {
  const response = await axios.get<ModelStatusCollection>(`/model/model-status/project/${projectId}`);
  return response.data;
}