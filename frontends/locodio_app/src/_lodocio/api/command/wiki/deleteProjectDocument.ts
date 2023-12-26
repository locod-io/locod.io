/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";

export interface DeleteProjectDocumentCommand {
  subjectId: number;
  type: string;
  relatedProjectId: string;
  relatedDocumentId: string;
}

export async function deleteProjectDocument(command: DeleteProjectDocumentCommand) {
  const formData = new FormData();
  formData.append('command', JSON.stringify(command));
  const response = await axios.post<boolean>('/doc/wiki/delete-project-document', formData);
  return response.data;
}