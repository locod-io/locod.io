/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {ExportTemplateToMasterTemplateCommand} from "@/api/command/interface/templateCommands";

export async function exportTemplateToMasterTemplate(command: ExportTemplateToMasterTemplateCommand) {
  const formData = new FormData();
  formData.append('export', JSON.stringify(command));
  const response = await axios.post<boolean>(`/model/template/${command.templateId}/export`, formData);
  return response.data;
}
