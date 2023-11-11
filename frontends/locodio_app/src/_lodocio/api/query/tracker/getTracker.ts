/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {Tracker} from "@/_lodocio/api/interface/tracker";
import type {CacheIssue} from "@/api/query/interface/linear";

export async function getTrackerById(id: number) {
  const response = await axios.get<Tracker>(`/doc/tracker/${id}`);
  return response.data;
}

export async function getFullTrackerById(id: number) {
  const response = await axios.get<Tracker>(`/doc/tracker/${id}/full`);
  return response.data;
}

export async function getTrackerIssues(id: number): Promise<Array<CacheIssue>> {
  const response = await axios.get<Array<CacheIssue>>(`/doc/tracker/${id}/issues`);
  return response.data;
}

