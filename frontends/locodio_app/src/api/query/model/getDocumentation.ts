/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {Documentation} from "@/api/query/interface/model";

export async function getDocumentation(projectId: number) {
  const response = await axios.get<Documentation>(`/model/project/${projectId}/documentation`);
  return response.data;
}