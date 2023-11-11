/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/


import axios from "axios";
import type {CacheIssue} from "@/api/query/interface/linear";

export interface ChangeNodeRelatedIssuesCommand {
  id: number;
  relatedIssues: Array<CacheIssue>;
}

export async function changeNodeRelatedIssues(command: ChangeNodeRelatedIssuesCommand) {
  const formData = new FormData();
  formData.append('command', JSON.stringify(command));
  const response = await axios.post<boolean>(`/doc/tracker/node/${command.id}/related-issues`, formData);
  return response.data;
}