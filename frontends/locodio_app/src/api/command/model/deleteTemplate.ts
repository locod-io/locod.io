/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {DeleteTemplateCommand} from "@/api/command/interface/templateCommands";

export async function deleteTemplate(command: DeleteTemplateCommand) {
  const formData = new FormData();
  formData.append('template', JSON.stringify(command));
  const response = await axios.post<boolean>(`/model/template/${command.id}/delete`, formData);

  return response.data;
}