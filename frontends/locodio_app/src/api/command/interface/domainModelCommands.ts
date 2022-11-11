/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

export interface AddDomainModelCommand {
  projectId: number;
  name: string;
}

export interface ChangeDomainModelCommand {
  id: number;
  name: string;
  namespace: string;
  repository: string;
}

export interface OrderDomainModelCommand {
  sequence: Array<number>;
}

export interface ChangeFieldCommand {
  id: number;
  name: string;
  type: string;
  length: number;
  identifier: boolean;
  required: boolean;
  unique: boolean;
  make: boolean;
  change: boolean;
  enumId: number;
}

export interface AddFieldCommand {
  domainModelId: number;
  name: string;
  length: number;
  type: string;
  identifier: boolean;
  required: boolean;
  unique: boolean;
  make: boolean;
  change: boolean;
  enumId: number;
}

export interface OrderFieldCommand {
  sequence: Array<number>;
}

export interface ChangeRelationCommand {
  id: number;
  type: string;
  mappedBy: string;
  inversedBy: string;
  fetch: string;
  orderBy: string;
  orderDirection: string;
  targetDomainModelId: number;
  make: boolean;
  change: boolean;
  required: boolean;
}

export interface AddRelationCommand {
  domainModelId: number;
  type: string;
  mappedBy: string;
  inversedBy: string;
  fetch: string;
  orderBy: string;
  orderDirection: string;
  targetDomainModelId: number;
}

export interface OrderRelationCommand {
  sequence: Array<number>;
}

export interface DeleteDomainModelCommand {
  id: number;
}

export interface DeleteFieldCommand {
  id: number;
}

export interface DeleteRelationCommand {
  id: number;
}