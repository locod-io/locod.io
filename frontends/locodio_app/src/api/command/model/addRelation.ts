/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {AddRelationCommand} from "@/api/command/interface/domainModelCommands";

export async function addRelation(command: AddRelationCommand) {
  const formData = new FormData();
  formData.append('relation', JSON.stringify(command));
  const response = await axios.post<boolean>('/model/relation/add', formData);
  return response.data;
}