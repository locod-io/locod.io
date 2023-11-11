/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {Organisation} from "@/api/query/interface/model";

export async function getOrganisationById(id:number) {
  const response = await axios.get<Organisation>('/organisation/'+id);
  return response.data;
}