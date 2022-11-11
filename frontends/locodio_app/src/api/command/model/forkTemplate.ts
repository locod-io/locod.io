/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {ForkTemplateCommand} from "@/api/command/interface/userCommands";

export async function forkTemplate(command: ForkTemplateCommand) {
  const formData = new FormData();
  formData.append('fork-template', JSON.stringify(command));
  const templateId = command.templateId;
  const response = await axios.post<boolean>(`/user/fork-template/${templateId}`, formData);

  return response.data;
}