/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {defineStore} from 'pinia'
import type {Invitation, User} from "@/api/query/interface/user";
import {getOrganisationUsers} from "@/_common/userManagement/api/query/user/getOrganisationUsers";
import {getOrganisationUser} from "@/_common/userManagement/api/query/user/getOrganisationUserDetail";
import {getOrganisationInvitations} from "@/_common/userManagement/api/query/user/getOrganisationInvitations";

export interface Role {
  label: string;
  role: 'ROLE_ORGANISATION_VIEWER' | 'ROLE_ORGANISATION_ADMIN' | 'ROLE_ORGANISATION_USER';
}

export type AppState = {
  organisationId: number;
  users: Array<User>;
  invitations: Array<Invitation>;
  isUsersLoading: boolean;
  isUsersReloading: boolean;
  userId: number;
  userDetailLoading: boolean;
  userDetailReloading: boolean;
  userDetail: User | null;
  availableRoles: Array<Role>;
}

export const useUserManagementStore = defineStore({
  id: "userManagement",
  state: (): AppState => ({
    organisationId: 0,
    users: [],
    invitations: [],
    isUsersLoading: false,
    isUsersReloading: false,
    userId: 0,
    userDetailLoading: false,
    userDetailReloading: false,
    userDetail: null,
    availableRoles: [
      {label: 'Viewer', role: 'ROLE_ORGANISATION_VIEWER'},
      {label: 'User', role: 'ROLE_ORGANISATION_USER'},
      {label: 'Admin', role: 'ROLE_ORGANISATION_ADMIN'},
    ]
  }),
  actions: {
    async loadUsers(organisationId: number) {
      this.organisationId = organisationId;
      this.isUsersLoading = true;
      this.users = await getOrganisationUsers(this.organisationId);
      this.invitations = await getOrganisationInvitations(this.organisationId);
      this.isUsersLoading = false;
    },
    async reloadUsers() {
      this.isUsersReloading = true;
      this.users = await getOrganisationUsers(this.organisationId);
      this.invitations = await getOrganisationInvitations(this.organisationId);
      this.isUsersReloading = false;
    },
    async loadUser(userId: number) {
      this.userDetailLoading = true;
      this.userId = userId;
      this.userDetail = await getOrganisationUser(this.organisationId, this.userId);
      this.userDetailLoading = false;
    },
    async reloadUser() {
      if (this.userId !== 0) {
        this.userDetailReloading = true;
        this.userDetail = await getOrganisationUser(this.organisationId, this.userId);
        this.userDetailReloading = false;
      }
    },
    resetStore() {
      this.users = [];
      this.userId = 0;
      this.userDetail = null;
    }
  },
  getters: {},
});
