/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {ImportTemplatesFromMasterTemplatesCommand} from "@/api/command/interface/templateCommands";

export async function importTemplatesFromMasterTemplates(command: ImportTemplatesFromMasterTemplatesCommand) {
  const formData = new FormData();
  formData.append('import', JSON.stringify(command));
  const response = await axios.post<boolean>(`/model/template/import`, formData);
  return response.data;
}
