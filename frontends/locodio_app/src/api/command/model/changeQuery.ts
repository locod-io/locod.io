/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {ChangeQueryCommand} from "@/api/command/interface/queryCommands";

export async function changeQuery(command: ChangeQueryCommand) {
  const formData = new FormData();
  formData.append('query', JSON.stringify(command));
  const response = await axios.post<boolean>(`/model/query/${command.id}`, formData);
  return response.data;
}
