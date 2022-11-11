/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";

import type {AddMasterTemplateCommand} from "@/api/command/interface/masterTemplateCommands";

export async function addMasterTemplate(command: AddMasterTemplateCommand) {
  const formData = new FormData();
  formData.append('masterTemplate', JSON.stringify(command));
  const response = await axios.post<boolean>('/model/master-template/add', formData);

  return response.data;
}
