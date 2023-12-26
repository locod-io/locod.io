/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {Team} from "@/api/query/interface/linear";

export interface ChangeWikiCommand {
  id: number;
  name: string;
  code: string;
  color: string;
  description: string;
  relatedTeams: Array<Team>;
  slug: string;
  isPublic: boolean;
  showOnlyFinalNodes: boolean;
}

export async function changeWiki(command: ChangeWikiCommand) {
  const formData = new FormData();
  formData.append('command', JSON.stringify(command));
  const response = await axios.post<boolean>('/doc/wiki/' + command.id, formData);
  return response.data;
}