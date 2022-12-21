/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {ChangeModuleCommand} from "@/api/command/interface/modelConfiguration";

export async function changeModule(command: ChangeModuleCommand) {
  const formData = new FormData();
  formData.append('module', JSON.stringify(command));
  const response = await axios.post<boolean>(`/model/module/${command.id}`, formData);
  return response.data;
}