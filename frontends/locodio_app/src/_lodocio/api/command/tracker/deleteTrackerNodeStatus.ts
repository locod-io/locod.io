/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";

export interface DeleteTrackerNodeStatus {
  id: number;
}

export async function deleteTrackerNodeStatus(command: DeleteTrackerNodeStatus) {
  const formData = new FormData();
  formData.append('command', JSON.stringify(command));
  const response = await axios.post<boolean>('/doc/tracker/node/status/' + command.id + '/delete', formData);
  return response.data;
}