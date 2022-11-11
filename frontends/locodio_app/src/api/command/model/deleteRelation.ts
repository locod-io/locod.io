/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {DeleteRelationCommand} from "@/api/command/interface/domainModelCommands";

export async function deleteRelation(command: DeleteRelationCommand) {
  const formData = new FormData();
  formData.append('relation', JSON.stringify(command));
  const response = await axios.post<boolean>(`/model/relation/${command.id}/delete`, formData);

  return response.data;
}