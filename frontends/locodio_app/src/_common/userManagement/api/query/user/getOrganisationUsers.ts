import axios from "axios";
import type {User} from "@/api/query/interface/user";

export async function getOrganisationUsers(organisationId: number) {
  const response = await axios.get<Array<User>>(`organisation/${organisationId}/users`);
  return response.data;
}