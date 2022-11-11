/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {ChangeDomainModelCommand} from "@/api/command/interface/domainModelCommands";

export async function changeDomainModel(command: ChangeDomainModelCommand) {
  const formData = new FormData();
  formData.append('domainModel', JSON.stringify(command));
  const response = await axios.post<boolean>(`/model/domain-model/${command.id}`, formData);
  return response.data;
}
