/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";

import type {ChangeTemplateContentCommand} from "@/api/command/interface/templateCommands";

export async function changeTemplateContent(command: ChangeTemplateContentCommand) {
  const formData = new FormData();
  formData.append('config', JSON.stringify(command));
  const response = await axios.post<boolean>('/model/template/change-content', formData);

  return response.data;
}
