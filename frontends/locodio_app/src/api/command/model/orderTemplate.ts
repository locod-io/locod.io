/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {OrderTemplateCommand} from "@/api/command/interface/templateCommands";

export async function orderTemplates(command: OrderTemplateCommand) {
  const formData = new FormData();
  formData.append('sequence', JSON.stringify(command));
  const response = await axios.post<boolean>('/model/template/order', formData);

  return response.data;
}