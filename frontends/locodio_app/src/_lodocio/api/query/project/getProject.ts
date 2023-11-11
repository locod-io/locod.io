/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {DocProject} from "@/_lodocio/api/interface/project";

export async function getDocProjectById(id: number) {
  const response = await axios.get<DocProject>(`/doc/project/${id}`);
  return response.data;
}
