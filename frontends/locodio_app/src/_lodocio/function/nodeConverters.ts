/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

import type {TrackerNode, TrackerNodeGroup} from "@/_lodocio/api/interface/tracker";
import {Converter} from 'showdown';

export function convertNodeToMarkDown(node: TrackerNode): string {
  const _content = new Converter().makeMarkdown(node.description);
  let _title = '';
  for (let i = 0; i < node.level + 1; i++) {
    _title = _title + '#';
  }
  _title = _title + ' ' + node.number + '. ' + node.name + "\n\n";
  let _result = _title + '' + _content
  _result = _result.replace(/<br>\n/g, ' ');
  _result = _result.replace(/<mark>/g, '**');
  _result = _result.replace(/<s>/g, '~~');
  _result = _result.replace(/<\/mark>\n\n/g, '**');
  _result = _result.replace(/<\/s>\n\n/g, '~~ ');
  _result = _result.replace(/~~\s*(.*?)\s*~~/g, '~~$1~~');
  _result = _result.replace(/<!--/g, '');
  _result = _result.replace(/-->/g, '');
  _result = _result.replace(/\*\*([A-Za-z0-9]+)\s*\*\*/g, '**$1** ');
  _result = _result.replace(/\n{3,}/g, '\n');
  return _result;
}

export function convertGroupToMarkDown(group: TrackerNodeGroup): string {
  let _title = '';
  for (let i = 0; i < group.level + 1; i++) {
    _title = _title + '#';
  }
  _title = _title + ' ' + group.number + '. ' + group.name + "\n\n";
  return _title;
}