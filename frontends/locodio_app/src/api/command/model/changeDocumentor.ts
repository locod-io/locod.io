/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {ChangeDocumentorCommand} from "@/api/command/interface/modelConfiguration";

export async function changeDocumentor(command: ChangeDocumentorCommand) {
  const formData = new FormData();
  formData.append('documentor', JSON.stringify(command));
  const response = await axios.post<boolean>(`/model/documentor/${command.id}`, formData);
  return response.data;
}
