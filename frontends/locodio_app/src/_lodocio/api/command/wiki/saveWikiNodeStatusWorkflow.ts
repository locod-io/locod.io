/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {WorkflowItem} from "@/api/command/interface/modelConfiguration";

export interface WikiNodeStatusWorkflowCommand {
  workflow: Array<WorkflowItem>;
}

export async function saveWikiNodeStatusWorkflow(wikiId: number, command: WikiNodeStatusWorkflowCommand) {
  const formData = new FormData();
  formData.append('workflow', JSON.stringify(command));
  const response = await axios.post<boolean>(`/doc/wiki/${wikiId}/node-status/workflow`, formData);
  return response.data;
}