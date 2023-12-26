/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {WikiNodeStatus} from "@/_lodocio/api/interface/wiki";

export async function getWikiNodeStatusById(id: number) {
  const response = await axios.get<WikiNodeStatus>(`/doc/wiki/node-status/${id}`);
  return response.data;
}
