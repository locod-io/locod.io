/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {ModelStatusWorkflow} from "@/api/query/interface/model";

export async function getTrackerNodeStatusWorkflow(trackerId: number) {
  const response = await axios.get<ModelStatusWorkflow>(`/doc/tracker/${trackerId}/node-status/workflow`);
  return response.data;
}