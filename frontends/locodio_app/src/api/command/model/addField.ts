/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {AddFieldCommand} from "@/api/command/interface/domainModelCommands";

export async function addField(command: AddFieldCommand) {
  const formData = new FormData();
  formData.append('field', JSON.stringify(command));
  const response = await axios.post<boolean>('/model/field/add', formData);
  return response.data;
}