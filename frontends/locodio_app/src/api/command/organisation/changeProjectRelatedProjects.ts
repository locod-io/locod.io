import axios from "axios";
import type {Project} from "@/api/query/interface/linear";

export interface ChangeProjectRelatedProjectsCommand {
  id: number;
  relatedProjects: Array<Project>;
}

export async function changeProjectRelatedProjects(command: ChangeProjectRelatedProjectsCommand) {
  const formData = new FormData();
  formData.append('command', JSON.stringify(command));
  const response = await axios.post<boolean>(`/project/${command.id}/related-projects`, formData);
  return response.data;
}