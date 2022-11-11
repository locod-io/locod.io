/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

export interface AddEnumCommand {
  projectId: number;
  domainModelId: number;
  name: string;
}

export interface ChangeEnumCommand {
  id: number;
  domainModelId: number;
  name: string;
  namespace: string;
}

export interface OrderEnumCommand {
  sequence: Array<number>;
}

export interface AddEnumOptionCommand {
  enumId: number;
  code: string;
  value: string;
}

export interface ChangeEnumOptionCommand {
  id: number;
  code: string;
  value: string;
}

export interface OrderEnumOptionCommand {
  sequence: Array<number>;
}

export interface DeleteEnumCommand {
  id: number;
}

export interface DeleteEnumOptionCommand {
  id: number;
}