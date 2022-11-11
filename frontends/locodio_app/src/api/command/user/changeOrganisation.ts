/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {ChangeOrganisationCommand} from "@/api/command/interface/userCommands";

export async function changeOrganisation(command: ChangeOrganisationCommand) {
  const formData = new FormData();
  formData.append('organisation', JSON.stringify(command));
  const response = await axios.post<boolean>(`/organisation/${command.id}`, formData);
  return response.data;
}