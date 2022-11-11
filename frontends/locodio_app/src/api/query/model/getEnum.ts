/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import type {Enum} from "@/api/query/interface/model";
import axios from "axios";

export async function getEnumById(id: number) {
  const response = await axios.get<Enum>(`/model/enum/${id}`);
  return response.data;
}
