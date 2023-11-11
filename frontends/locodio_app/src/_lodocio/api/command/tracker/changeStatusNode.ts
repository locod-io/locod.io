/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/


import axios from "axios";

export interface ChangeStatusNodeCommand {
  id: number;
  trackerNodeStatusId: number;
}

export async function changeStatusNode(command: ChangeStatusNodeCommand) {
  const formData = new FormData();
  formData.append('command', JSON.stringify(command));
  const response = await axios.post<boolean>(`/doc/tracker/node/${command.id}/change-status`, formData);
  return response.data;
}