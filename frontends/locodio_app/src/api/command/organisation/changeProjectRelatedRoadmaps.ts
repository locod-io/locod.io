import axios from "axios";
import type {Roadmap} from "@/api/query/interface/linear";

export interface ChangeProjectRelatedRoadmapsCommand {
  id: number;
  relatedRoadmaps: Array<Roadmap>;
}

export async function changeProjectRelatedRoadmaps(command: ChangeProjectRelatedRoadmapsCommand) {
  const formData = new FormData();
  formData.append('command', JSON.stringify(command));
  const response = await axios.post<boolean>(`/project/${command.id}/related-roadmaps`, formData);
  return response.data;
}