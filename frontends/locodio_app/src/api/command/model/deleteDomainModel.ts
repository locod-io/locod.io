/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {DeleteDomainModelCommand} from "@/api/command/interface/domainModelCommands";

export async function deleteDomainModel(command: DeleteDomainModelCommand) {
  const formData = new FormData();
  formData.append('domainModel', JSON.stringify(command));
  const response = await axios.post<boolean>(`/model/domain-model/${command.id}/delete`, formData);

  return response.data;
}