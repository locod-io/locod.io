/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {AddAssociationCommand} from "@/api/command/interface/domainModelCommands";

export async function addAssociation(command: AddAssociationCommand) {
  const formData = new FormData();
  formData.append('association', JSON.stringify(command));
  const response = await axios.post<boolean>('/model/association/add', formData);
  return response.data;
}