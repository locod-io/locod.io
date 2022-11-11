/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {ChangePasswordCommand} from "@/api/command/interface/userCommands";

export async function changePassword(command: ChangePasswordCommand) {
  const formData = new FormData();
  formData.append('user', JSON.stringify(command));
  const response = await axios.post<boolean>('/user/change-password', formData);
  return response.data;
}