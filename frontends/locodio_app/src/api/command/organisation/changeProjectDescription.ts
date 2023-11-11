import axios from "axios";

export interface ChangeProjectDescriptionCommand {
  id: number;
  description: string;
}

export async function changeProjectDescription(command: ChangeProjectDescriptionCommand) {
  const formData = new FormData();
  formData.append('command', JSON.stringify(command));
  const response = await axios.post<boolean>(`/project/${command.id}/description`, formData);
  return response.data;
}