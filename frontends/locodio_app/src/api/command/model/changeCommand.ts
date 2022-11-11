/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {ChangeCommandCommand} from "@/api/command/interface/commandCommands";

export async function changeCommand(command: ChangeCommandCommand) {
  const formData = new FormData();
  formData.append('command', JSON.stringify(command));
  const response = await axios.post<boolean>(`/model/command/${command.id}`, formData);
  return response.data;
}
