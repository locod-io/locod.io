/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import ace from 'ace-builds';

import themeChromeUrl from 'ace-builds/src-noconflict/theme-chrome?url';
ace.config.setModuleUrl('ace/theme/chrome', themeChromeUrl);

import themeNordDarkUrl from 'ace-builds/src-noconflict/theme-nord_dark?url';
ace.config.setModuleUrl('ace/theme/nord_dark', themeNordDarkUrl);

import 'ace-builds/src-noconflict/ext-language_tools';
ace.require("ace/ext/language_tools");