/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";

export interface RemoveUserFromOrganisationCommand {
  userId: number;
  organisationId: number;
}

export async function removeUserFromOrganisation(command: RemoveUserFromOrganisationCommand) {
  const formData = new FormData();
  formData.append('command', JSON.stringify(command));
  const response = await axios.post<boolean>('organisation/' + command.organisationId + '/remove-user/' + command.userId, formData);
  return response.data;
}