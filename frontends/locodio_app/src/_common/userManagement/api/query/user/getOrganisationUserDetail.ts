import axios from "axios";
import type {User} from "@/api/query/interface/user";

export async function getOrganisationUser(organisationId: number, userId: number) {
  const response = await axios.get<User>(`organisation/${organisationId}/user/${userId}`);
  return response.data;
}