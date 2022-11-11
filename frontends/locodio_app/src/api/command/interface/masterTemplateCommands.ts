/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

export interface AddMasterTemplateCommand {
  userId: number;
  type: string;
  name: string;
  language: string;
}

export interface ChangeMasterTemplateCommand {
  id: number;
  type: string;
  name: string;
  language: string;
  template: string;
  isPublic: boolean;
  description: string;
  tags: Array<string>;
}

export interface DeleteMasterTemplateCommand {
  id: number;
}

export interface OrderMasterTemplateCommand {
  sequence: Array<number>;
}