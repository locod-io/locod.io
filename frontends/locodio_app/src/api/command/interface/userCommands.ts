/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

export interface ChangeProfileCommand {
  userId: number;
  firstname: string;
  lastname: string;
  color: string;
}

export interface ChangeThemeCommand {
  id: number;
  theme: string;
}

export interface ChangePasswordCommand {
  userId: number;
  password1: string;
  password2: string;
}

export interface AddOrganisationCommand {
  userId: number;
  name: string;
}

export interface AddProjectCommand {
  organisationId: number;
  name: string;
}

export interface ChangeOrganisationCommand {
  id: number;
  code: string;
  name: string;
  color: string;
  linearApiKey: string;
}

export interface ChangeProjectCommand {
  id: number;
  code: string;
  name: string;
  color: string;
  domainLayer: string;
  applicationLayer: string;
  infrastructureLayer: string;
}

export interface OrderOrganisationCommand {
  sequence: Array<number>;
}

export interface OrderProjectCommand {
  sequence: Array<number>;
}

export interface ForkTemplateCommand {
  userId: number;
  templateId: number;
}
