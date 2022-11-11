/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {DeleteEnumCommand} from "@/api/command/interface/enumCommands";

export async function deleteEnum(command: DeleteEnumCommand) {
  const formData = new FormData();
  formData.append('enum', JSON.stringify(command));
  const response = await axios.post<boolean>(`/model/enum/${command.id}/delete`, formData);

  return response.data;
}