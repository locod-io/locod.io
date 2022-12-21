/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {AddModuleCommand} from "@/api/command/interface/modelConfiguration";

export async function addModule(command: AddModuleCommand) {
  const formData = new FormData();
  formData.append('module', JSON.stringify(command));
  const response = await axios.post<boolean>('/model/module/add', formData);
  return response.data;
}