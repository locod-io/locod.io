/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {defineStore} from 'pinia'
import type {navigationItem} from "@/components/overview/model";

export type SchemaState = {
  configuration: Array<navigationItem>;
  counter: number;
}

export const useSchemaStore = defineStore({
  id: "schema",
  state: (): SchemaState => ({
    configuration: [],
    counter: 0
  }),
  actions: {
    incrementCounter() {
      this.counter++;
    }
  },
  getters: {},
});