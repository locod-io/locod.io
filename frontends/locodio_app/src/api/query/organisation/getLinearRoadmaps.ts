import type {Roadmap} from "@/api/query/interface/linear";
import axios from "axios";

export async function getLinearRoadmaps(organisationId: number): Promise<Array<Roadmap>> {
  const response = await axios.get<Array<Roadmap>>(`/organisation/${organisationId}/linear-roadmaps`);
  return response.data;
}