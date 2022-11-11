/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {AddOrganisationCommand} from "@/api/command/interface/userCommands";

export async function addOrganisation(command: AddOrganisationCommand) {
  const formData = new FormData();
  formData.append('organisation', JSON.stringify(command));
  const response = await axios.post<boolean>('/organisation/add', formData);
  return response.data;
}