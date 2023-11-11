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

export interface TrackerNodeStatusWorkflowCommand {
  workflow: Array<WorkflowItem>;
}

export async function saveTrackerNodeStatusWorkflow(trackerId: number, command: TrackerNodeStatusWorkflowCommand) {
  const formData = new FormData();
  formData.append('workflow', JSON.stringify(command));
  const response = await axios.post<boolean>(`/doc/tracker/${trackerId}/node-status/workflow`, formData);
  return response.data;
}