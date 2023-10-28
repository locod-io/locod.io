/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {Issue} from "@/api/query/interface/linear";

export async function getDocumentorRelatedIssues(id: number) {
  const response = await axios.get<Array<Issue>>(`/model/documentor/${id}/related-issues`);
  return response.data;
}
