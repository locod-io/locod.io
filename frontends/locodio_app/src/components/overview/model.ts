/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import type {DomainModel, Enum, ModelStatus} from "@/api/query/interface/model";

export interface navigationItem {
  id: string;
  name: string;
  module: string;
  status: ?ModelStatus;
  subject: DomainModel | Enum;
  subjectType: "model" | "enum";
  isSelected: boolean;
  isBasic: boolean;
  isRegular: boolean;
  isFull: boolean;
}
