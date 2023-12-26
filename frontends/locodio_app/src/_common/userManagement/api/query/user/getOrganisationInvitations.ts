import axios from "axios";
import type {Invitation} from "@/api/query/interface/user";

export async function getOrganisationInvitations(organisationId: number) {
  const response = await axios.get<Array<Invitation>>(`organisation/${organisationId}/invitations`);
  return response.data;
}