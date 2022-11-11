/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

export interface AddTemplateCommand {
  projectId: number;
  type: string;
  name: string;
  language: string;
}

export interface ChangeTemplateCommand {
  id: number;
  type: string;
  name: string;
  language: string;
  template: string;
}

export interface OrderTemplateCommand {
  sequence: Array<number>;
}

export interface ExportTemplateToMasterTemplateCommand {
  userId: number;
  templateId: number;
}

export interface ImportTemplatesFromMasterTemplatesCommand {
  projectId: number;
  masterTemplateIds: Array<number>;
}

export interface DeleteTemplateCommand {
  id: number;
}