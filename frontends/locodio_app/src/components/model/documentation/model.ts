/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import type {ModelStatus} from "@/api/query/interface/model";

export interface NavigationItem {
  id: number;
  level: number;
  levelLabel: string;
  label: string;
  status: ModelStatus;
  type: string;
  isOpen: boolean;
  children: NavigationItem[];
}