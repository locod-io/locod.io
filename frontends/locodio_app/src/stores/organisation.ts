/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {defineStore} from 'pinia'

export const useOrganisationStore = defineStore({
  id: "organisation",
  state: () => ({
    projectId: 0,
    organisationId: 0,
  }),
  actions: {
    setWorkingVersion(organisationId: number, projectId: number) {
      this.organisationId = organisationId;
      this.projectId = projectId;
    }
  },
  getters: {},
});