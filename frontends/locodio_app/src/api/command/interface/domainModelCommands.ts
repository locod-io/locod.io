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
  moduleId: number;
  name: string;
}

export interface ChangeDomainModelCommand {
  id: number;
  moduleId: number;
  name: string;
  namespace: string;
  repository: string;
}

export interface OrderDomainModelCommand {
  sequence: Array<number>;
}

export interface ChangeAttributeCommand {
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

export interface AddAttributeCommand {
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

export interface OrderAttributeCommand {
  sequence: Array<number>;
}

export interface ChangeAssociationCommand {
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

export interface AddAssociationCommand {
  domainModelId: number;
  type: string;
  mappedBy: string;
  inversedBy: string;
  fetch: string;
  orderBy: string;
  orderDirection: string;
  targetDomainModelId: number;
}

export interface OrderAssociationCommand {
  sequence: Array<number>;
}

export interface DeleteDomainModelCommand {
  id: number;
}

export interface DeleteAttributeCommand {
  id: number;
}

export interface DeleteAssociationCommand {
  id: number;
}