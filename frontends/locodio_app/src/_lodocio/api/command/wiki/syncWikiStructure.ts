/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {WikiStructure} from "@/_lodocio/api/interface/wiki";

export interface SyncWikiStructureCommand {
  id: number;
  structure: WikiStructure;
}

export async function syncWikiStructure(command: SyncWikiStructureCommand) {
  const formData = new FormData();
  formData.append('command', JSON.stringify(command));
  const response = await axios.post<boolean>(
    '/doc/wiki/' + command.id + '/sync-structure',
    formData,
  );
  return response.data;
}