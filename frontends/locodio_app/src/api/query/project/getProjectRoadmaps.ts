import axios from "axios";
import type {Roadmap} from "@/api/query/interface/linear";

export async function getProjectRoadmaps(id: number) {
  const response = await axios.get<Array<Roadmap>>('/project/' + id + '/roadmaps');
  return response.data;
}
