/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

export interface MasterTemplate {
  id: number;
  uuid: string;
  name: string;
  type: string;
  language: string;
  isPublic: boolean
  description: string;
  tags: Array<string>;
  from: AnonymousUser;
  template: string;
  lastUpdatedAt: string;
}

export interface AnonymousUser {
  color: string;
  initials: string;
}

export interface MasterTemplateCollection {
  collection: Array<MasterTemplate>;
}