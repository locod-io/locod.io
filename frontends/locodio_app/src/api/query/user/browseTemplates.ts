/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {MasterTemplateCollection} from "@/api/query/interface/templates";

export async function browseTemplates() {
  const response = await axios.get<MasterTemplateCollection>(`model/master-template/browse`);
  return response.data.collection;
}

