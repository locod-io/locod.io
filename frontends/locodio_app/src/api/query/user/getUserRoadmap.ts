/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {Roadmap} from "@/api/query/interface/linear";

export async function getUserRoadmap() {
  const response = await axios.get<Array<Roadmap>>(`/user/roadmap`);
  return response.data;
}