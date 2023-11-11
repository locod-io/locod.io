/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import type {Tracker} from "@/_lodocio/api/interface/tracker";
import type {Project} from "@/api/query/interface/model";

export interface DocProject {
  id: number;
  uuid: string;
  code: string;
  name: string;
  color: string;
  trackers: Array<Tracker>;
  project: Project;
}