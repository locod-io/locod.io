/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {ModelStatusWorkflowCommand} from "@/api/command/interface/modelConfiguration";

export async function saveModelStatusWorkflow(projectId: number, command: ModelStatusWorkflowCommand) {
  const formData = new FormData();
  formData.append('workflow', JSON.stringify(command));
  const response = await axios.post<boolean>(`/model/model-status/${projectId}/workflow`, formData);
  return response.data;
}