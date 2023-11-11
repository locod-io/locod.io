/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {TrackerNodeStatus} from "@/_lodocio/api/interface/tracker";

export async function getTrackerNodeStatusById(id: number) {
  const response = await axios.get<TrackerNodeStatus>(`/doc/tracker/node-status/${id}`);
  return response.data;
}
