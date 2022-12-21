import axios from "axios";
import type {ChangeModelSettingsCommand} from "@/api/command/interface/projectCommands";

export async function changeModelSettings(command: ChangeModelSettingsCommand) {
  const formData = new FormData();
  formData.append('modelSettings', JSON.stringify(command));
  const response = await axios.post<boolean>(`/model/model-settings`, formData);

  return response.data;
}