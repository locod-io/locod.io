/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {GeneratedTemplate, Template} from "@/api/query/interface/model";

export async function getTemplateById(id: number) {
  const response = await axios.get<Template>(`/model/template/${id}`);
  return response.data;
}

export async function generateTemplateBySubjectId(id: number, subjectId: number) {
  const response = await axios.get<GeneratedTemplate>(`/model/template/${id}/generate/${subjectId}`);
  return response.data;
}