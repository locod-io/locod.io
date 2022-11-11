/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import type {Mapping} from "@/api/query/interface/model";

export interface AddQueryCommand {
  projectId: number;
  domainModelId: number;
  name: string;
}

export interface ChangeQueryCommand {
  id: number;
  domainModelId: number;
  name: string;
  namespace: string;
  mapping: Array<Mapping>;
}

export interface OrderQueryCommand {
  sequence: Array<number>;
}

export interface DeleteQueryCommand {
  id: number;
}