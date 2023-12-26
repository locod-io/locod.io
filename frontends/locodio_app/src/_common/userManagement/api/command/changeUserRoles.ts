/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";

export interface ChangeOrganisationUserRolesCommand {
  organisationId: number,
  userId: number,
  roles: Array<string>,
}

export async function changeOrganisationUserRoles(command: ChangeOrganisationUserRolesCommand) {
  const formData = new FormData();
  formData.append('command', JSON.stringify(command));
  const response = await axios.post<boolean>('organisation/' + command.organisationId + '/user/' + command.userId + '/roles', formData);
  return response.data;
}
