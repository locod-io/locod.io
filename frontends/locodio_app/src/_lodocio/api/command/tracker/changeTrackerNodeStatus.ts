/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";

export interface ChangeTrackerNodeStatusCommand {
  id: number;
  name: string;
  color: string;
  isStart: boolean;
  isFinal: boolean;
}

export async function changeTrackerNodeStatus(command: ChangeTrackerNodeStatusCommand) {
  const formData = new FormData();
  formData.append('command', JSON.stringify(command));
  const response = await axios.post<boolean>('/doc/tracker/node-status/' + command.id, formData);
  return response.data;
}