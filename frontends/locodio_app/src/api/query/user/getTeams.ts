/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {Team} from "@/api/query/interface/linear";

export async function getTeams(organisationId: number): Promise<Array<Team>> {
  const response = await axios.get<Array<Team>>(`/model/organisation/${organisationId}/teams`);
  return response.data;
}