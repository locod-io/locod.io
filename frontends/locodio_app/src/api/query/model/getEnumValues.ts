/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import axios from "axios";
import type {EnumValues, ListItem, Lists} from "@/api/query/interface/model";

export async function getLists() {
  const response = await axios.get<EnumValues>(`/model/enum-values`);
  return convertEnumValuesToLists(response.data);
}

function convertEnumValuesToLists(enumValues: EnumValues): Lists {
  let lists = new Object() as Lists;
  lists.fieldTypes = convertToList(enumValues.fieldTypes);
  lists.fetchTypes = convertToList(enumValues.fetchTypes);
  lists.relationTypes = convertToList(enumValues.relationTypes);
  lists.orderTypes = convertToList(enumValues.orderTypes);
  lists.templateTypes = convertToList(enumValues.templateTypes);

  return lists;
}

function convertToList(enumValues: Array<string>): Array<ListItem> {
  let result = [] as Array<ListItem>;
  for (let i = 0; i < enumValues.length; i++) {
    let listItem = new Object() as ListItem;
    listItem.code = enumValues[i];
    listItem.label = enumValues[i];
    result.push(listItem);
  }
  return result;
}