/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";

export async function removeDocumentorImage(id: number) {
  const response = await axios.post<boolean>(`/model/documentor/${id}/remove-image`);
  return response.data;
}