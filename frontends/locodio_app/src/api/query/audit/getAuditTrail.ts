/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {AuditItem} from "@/api/query/interface/audit";

export async function getAuditTrail(type: string, subjectId: number) {
  const response = await axios.get<Array<AuditItem>>(`/audit/${type}/${subjectId}`);
  return response.data;
}