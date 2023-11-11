import axios from "axios";
import type {Document} from "@/api/query/interface/linear";

export async function getProjectDocuments(id: number) {
  const response = await axios.get<Array<Document>>('/model/project/' + id + '/documents');
  return response.data;
}