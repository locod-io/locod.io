/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {DeleteAttributeCommand} from "@/api/command/interface/domainModelCommands";

export async function deleteAttribute(command: DeleteAttributeCommand) {
  const formData = new FormData();
  formData.append('attribute', JSON.stringify(command));
  const response = await axios.post<boolean>(`/model/attribute/${command.id}/delete`, formData);

  return response.data;
}