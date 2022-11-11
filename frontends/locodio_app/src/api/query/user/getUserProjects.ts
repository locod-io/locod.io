/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {UserProjects} from "@/api/query/interface/user";

export async function getUserProjects() {
  const response = await axios.get<UserProjects>(`/user/projects`);
  return response.data;
}