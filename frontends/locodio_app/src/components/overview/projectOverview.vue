<!--
/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
-->

<template>
  <model-top-bar type="overview"/>
  <div>
    <Splitter :style="'background-color:'+appStore.backgroundColor+';'">
      <!-- configuration -->
      <SplitterPanel :size="25">
        <navigation></navigation>
      </SplitterPanel>
      <!-- detail -->
      <SplitterPanel :size="75">
        <div class="bg-gray-300">
          <detail-wrapper :estate-height="84">
            <!-- debug string -->
            <!--          <div class="text-xs">-->
            <!--            <pre><code>{{ schemaString }}</code></pre>-->
            <!--          </div>-->
            <!-- render schema -->
            <div v-html="svgData" class="w-full " :style="'zoom: '+zoomLevel+'%'"/>
          </detail-wrapper>
        </div>
      </SplitterPanel>
    </Splitter>

    <!-- toolbar   -->
    <div class="h-12 p-2 border-t-[1px] border-gray-300 dark:border-gray-600">
      <div class="flex">
        <div class="">
          <Button
              v-if="!modelStore.isProjectLoading"
              icon="pi pi-refresh"
              class="p-button-sm"
              @click="refreshProject"/>
          <Button
              v-else
              class="p-button-sm"
              disabled
              icon="pi pi-spin pi-spinner"/>
        </div>
        <div class="flex-grow">&nbsp;</div>
        <div class="flex">
          <div class="ml-2">
            <Button
                icon="pi pi-search-plus"
                class="p-button-sm "
                @click="zoomLevel = zoomLevel + 10"/>
          </div>
          <div class="ml-2">
            <Button
                icon="pi pi-search-minus"
                class="p-button-sm "
                @click="zoomLevel = zoomLevel - 10"/>
          </div>
        </div>
        <div class="mr-2 ml-2">
          <a id="downloadPngSvg" download="file.png">
            <Button @click="saveAsPngFromSvg()"
                    icon="pi pi-download"
                    label="save as .png"
                    class="p-button-sm p-button-outlined p-button-secondary"/>
          </a>
        </div>
        <div class="mr-2">
          <a id="downloadSvgSvg" download="file.svg">
            <Button @click="saveAsSvgSvg()"
                    icon="pi pi-download"
                    label="save as .svg"
                    class="p-button-sm p-button-outlined p-button-secondary"/>
          </a>
        </div>
        <div>
          <a id="downloadPuml" download="file.puml">
            <Button @click="saveAsPuml()"
                    icon="pi pi-download"
                    label="save as .puml"
                    class="p-button-sm p-button-outlined p-button-secondary"/>
          </a>
        </div>

      </div>
    </div>

    <!-- temp image & canvas wrapper for downloading the resulting rendering -->
    <div class="flex flex-row" style="display:none;">
      <div class="basis-1/2 mr-2">
        <img ref="downloadImage" id="downloadImage"/>
      </div>
      <div class="basis-1/2">
        <canvas ref="downloadCanvasSvg" id="downloadCanvas" class="bg-stone-300"></canvas>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import {useModelStore} from "@/stores/model";
import {computed, onMounted, ref, watch} from "vue";
import * as noml from "nomnoml/dist/nomnoml";
import type {Template} from "@/api/query/interface/model";
import Navigation from "@/components/overview/navigation.vue";
import DetailWrapper from "@/components/wrapper/detailWrapper.vue";
import {useSchemaStore} from "@/stores/schema";
import type {navigationItem} from "@/components/overview/model";
import ModelTopBar from "@/_common/topBar/modelTopBar.vue";
import {useAppStore} from "@/stores/app";

const modelStore = useModelStore();
const schemaStore = useSchemaStore();
const appStore = useAppStore();
const svgData = ref<string>('');
const schemaString = ref<string>('');
const zoomLevel = ref<number>(80);
const plantUmlString = ref<string>('');
const schemaLines = ref([]);

onMounted((): void => {
  renderSchema();
  if (schemaStore.configuration.length > 10) {
    zoomLevel.value = 50;
  }
});

function refreshProject() {
  void modelStore.reLoadProject();
  if (schemaStore.configuration.length > 10) {
    zoomLevel.value = 50;
  }
}

function renderSchema() {
  let isOneSelected = false;
  for (const item of schemaStore.configuration) {
    if (item.isSelected) {
      isOneSelected = true;
      break;
    }
  }
  if (schemaStore.configuration.length > 0 && isOneSelected) {

    let schema = '#lineWidth:1\r\n#zoom:1\r\n#leading: 1.75 \r\n#fontSize: 10\r\n#.box: dashed\r\n#edges:rounded\r\n#bendSize: 0.5\r\n#direction: down\r\n';
    plantUmlString.value = '@startuml\r\n\r\nskinparam shadowing false\r\n';

    schemaLines.value = [];
    for (const item of schemaStore.configuration) {
      schema += renderItemString(item);
    }
    schema += renderEnumRelations();
    schema += renderRelations();
    schemaString.value = complineSchemaLineToString();
    svgData.value = noml.renderSvg(schemaString.value)
    plantUmlString.value += '\r\n@enduml';

  } else {
    schemaString.value = "";
    svgData.value = "";
    plantUmlString.value = "";
  }
}

function complineSchemaLineToString(): string {
  let string = '#lineWidth:1\r\n#zoom:1\r\n#leading: 1.75 \r\n#fontSize: 10\r\n#.box: dashed\r\n#edges:rounded\r\n#bendSize: 1\r\n#direction: down\r\n';
  if (!schemaStore.showModules) {
    for (const schemaLine of schemaLines.value) {
      string += schemaLine.line;
    }
  } else {
    // sort first on module and then render in modules
    schemaLines.value.sort(compare);
    let prevModule = '';
    for (const schemaLine of schemaLines.value) {
      if (prevModule !== schemaLine.module) {
        if (prevModule === '') {
          string += '[<package> ' + schemaLine.module + '|\r\n';
        } else {
          string += '\r\n]\r\n[<package> ' + schemaLine.module + '|\r\n';
        }
      }
      string += schemaLine.line;
      prevModule = schemaLine.module;
    }
    string += '\r\n]';
  }
  return string;
}

function compare(a, b) {
  let comparison = 0;
  if (a.module > b.module) {
    comparison = 1;
  } else {
    comparison = -1;
  }
  return comparison;
}

function renderItemString(item: navigationItem): string {
  let string = '';
  if (item.isSelected) {
    if (item.subjectType === 'model') {
      if (item.isFull || item.isRegular) {
        plantUmlString.value += '\r\npackage "' + item.subject.namespace + '"{ \r\n\tclass ' + item.subject.name + ' {';
        string = '[' + item.name + '|';
        for (let i = 0; i < item.subject.attributes.length; i++) {
          string += '- ' + item.subject.attributes[i].name;
          plantUmlString.value += '\r\n\t\t' + item.subject.attributes[i].name;
          if (item.isFull) {
            string += ' (' + item.subject.attributes[i].type + ')';
            plantUmlString.value += ': ' + item.subject.attributes[i].type;
          }
          if (i !== (item.subject.attributes.length - 1)) string += ';'
        }
        if (item.isFull) {
          string += '|'
          for (let i = 0; i < item.subject.associations.length; i++) {
            string += '- ' + item.subject.associations[i].targetDomainModel.name + '';
            plantUmlString.value += "\r\n\t\t{method} " + item.subject.associations[i].targetDomainModel.name;
            if (item.subject.associations[i].type === 'One-To-Many_Bidirectional') {
              string += 's ';
              plantUmlString.value += 's ';
            }
            if (item.subject.associations[i].type === 'Many-To-Many_Bidirectional') {
              string += 's ';
              plantUmlString.value += 's ';
            }
            if (item.subject.associations[i].type === 'Many-To-Many_Unidirectional') {
              string += 's ';
              plantUmlString.value += 's ';
            }
            if (i !== (item.subject.associations.length - 1)) string += ';'
          }
        }
        string += ']';
        plantUmlString.value += '\r\n\t}\r\n}';
      } else {
        string = '[' + item.subject.name + ']';
        plantUmlString.value += '\r\npackage "' + item.subject.namespace + '"{ \t\r\nclass ' + item.subject.name + ' {\r\n\t}\r\n}';
      }
    } else if (item.subjectType === 'enum') {
      if (item.isFull || item.isRegular) {
        plantUmlString.value += '\r\npackage "' + item.subject.namespace + '"{ \r\n\tenum ' + item.subject.name + ' {';
        string = '[<box>' + item.name + '|';
        for (let i = 0; i < item.subject.options.length; i++) {
          string += '- ' + item.subject.options[i].value;
          if (i !== (item.subject.options.length - 1)) string += ';'
          plantUmlString.value += '\r\n\t\t' + item.subject.options[i].code;
        }
        string += ']';
        plantUmlString.value += '\r\n\t}\r\n}';
      } else {
        string = '[<box>' + item.name + ']';
        plantUmlString.value += '\r\npackage "' + item.subject.namespace + '"{ \t\r\nenum ' + item.subject.name + ' {\r\n\t}\r\n}';
      }
    }
  }
  // compile object for rendering the schema
  if (string !== '') {
    string = string + '\r\n';
    const schemaLine = {name: item.name, module: item.module, line: string}
    schemaLines.value.push(schemaLine);
  }

  return string;
}

function renderEnumRelations(): string {
  let string = '';
  let associations = [];
  for (const item of schemaStore.configuration) {
    if (item.isSelected) {
      if (item.subjectType == 'model') {
        for (const attribute of item.subject.attributes) {
          if (attribute.type === 'enum') {
            let association = {
              name: item.subject.name + '-' + attribute.enum.name,
              module: item.module,
              source: item.subject.name,
              target: attribute.enum.name
            };
            associations[association.name] = association;
          }
        }
      } else if (item.subjectType == 'enum') {
        let association = {
          name: item.subject.domainModel.name + '-' + item.name,
          module: item.module,
          source: item.subject.domainModel.name,
          target: item.name
        };
        associations[association.name] = association;
      }
    }
  }
  // -- render the relations
  for (const associationKey in associations) {
    const association = associations[associationKey]
    let resultString = '[<box>' + association.target + '] --> [' + association.source + ']\r\n';
    string = string + resultString;
    plantUmlString.value += '\r\n' + association.target + '...> ' + association.source + '\r\n'
    // compile object for rendering the schema
    const schemaLine = {name: association.name, module: association.module, line: resultString}
    schemaLines.value.push(schemaLine);
  }
  return string;
}

function renderRelations(): string {
  let string = '';
  let associations = [];
  for (const item of schemaStore.configuration) {
    if (item.isSelected) {
      if (item.subjectType == 'model') {
        for (const associationModel of item.subject.associations) {
          let key = '';
          if (associationModel.type === 'One-To-One_Bidirectional' || associationModel.type === 'Many-To-Many_Bidirectional') {
            let names = [];
            names.push(item.name);
            names.push(associationModel.targetDomainModel.name);
            names.sort();
            key = names[0] + '-' + names[1];
          } else {
            key = item.name + '-' + associationModel.targetDomainModel.name;
          }
          let association = {
            name: key,
            module: item.module,
            reverseName: associationModel.targetDomainModel.name + '-' + item.name,
            source: item.name,
            target: associationModel.targetDomainModel.name,
            targetModule: associationModel.targetDomainModel.module,
            type: associationModel.type
          };
          associations[association.name] = association;
        }
      }
    }
  }

  // -- render the relations
  for (const associationKey in associations) {
    const association = associations[associationKey]
    let resultString = '';
    switch (association.type) {
      case 'One-To-Many_Bidirectional':
        resultString = '[' + association.source + '] 1 o-> ..* [' + association.target + ']\r\n';
        plantUmlString.value += '\r\n' + association.source + ' "1" *--> "..*" ' + association.target + '\r\n';
        break;
      case 'One-To-Many_Self-referencing':
        resultString = '[' + association.source + '] 1 o-> ..* [' + association.target + ']\r\n';
        plantUmlString.value += '\r\n' + association.source + ' "1" *--> "..*" ' + association.target + '\r\n';
        break;
      case 'One-To-One_Bidirectional':
        resultString = '[' + association.source + '] 1 <-> 1 [' + association.target + ']\r\n';
        plantUmlString.value += '\r\n' + association.source + ' "1" <--> "1" ' + association.target + '\r\n';
        break;
      case 'One-To-One_Unidirectional':
        resultString = '[' + association.source + '] 1 <-> 1 [' + association.target + ']\r\n';
        plantUmlString.value += '\r\n' + association.source + ' "1" <--> "1" ' + association.target + '\r\n';
        break;
      case 'One-To-One_Self-referencing':
        resultString = '[' + association.source + '] 1 <-> 1 [' + association.target + ']\r\n';
        plantUmlString.value += '\r\n' + association.source + ' "1" <--> "1" ' + association.target + '\r\n';
        break;
      case 'Many-To-Many_Bidirectional':
        resultString = '[' + association.source + '] ..* o-o ..* [' + association.target + ']\r\n';
        plantUmlString.value += '\r\n' + association.source + ' "..*" *--* "..*" ' + association.target + '\r\n';
        break;
      case 'Many-To-Many_Unidirectional':
        resultString = '[' + association.source + '] ..* o-o ..* [' + association.target + ']\r\n';
        plantUmlString.value += '\r\n' + association.source + ' "..*" *--* "..*" ' + association.target + '\r\n';
        break;
      case 'Many-To-Many_Self-referencing':
        resultString = '[' + association.source + '] ..* o-o ..* [' + association.target + ']\r\n';
        plantUmlString.value += '\r\n' + association.source + ' "..*" *--* "..*" ' + association.target + '\r\n';
        break;
    }
    if (resultString !== '') {
      // compile object for rendering the schema
      const schemaLine = {name: association.name, module: association.module, line: resultString}
      schemaLines.value.push(schemaLine);
      string = string + resultString;
    }
  }
  return string;
}

const schemaCounter = computed(() => {
  return schemaStore.counter;
});

watch(schemaCounter, (): void => {
  renderSchema();
});

// const project = computed(() => {
//   return modelStore.project;
// });
//
// watch(project, (): void => {
//   renderSchema();
// });

// -----------------------------------------------------------------------------

function saveAsSvgSvg() {
  let url = "data:image/svg+xml;utf8," + encodeURIComponent(svgData.value);
  let link = document.getElementById("downloadSvgSvg")
  link.setAttribute("href", url)
  link.setAttribute("download", modelStore.project.name + ".svg")
}

function saveAsPuml() {
  let url = "data:text/plain;utf8," + encodeURIComponent(plantUmlString.value);
  let link = document.getElementById("downloadPuml")
  link.setAttribute("href", url)
  link.setAttribute("download", modelStore.project.name + ".puml")
}

function saveAsPngFromSvg() {
  let imgsrc = 'data:image/svg+xml;base64,' + btoa(svgData.value);
  let imageWrapper = document.getElementById("downloadImage")
  imageWrapper.setAttribute("src", imgsrc)
  let canvas = document.getElementById("downloadCanvas")
  let context = canvas.getContext("2d")
  let image = new Image;
  image.src = imgsrc;
  image.onload = function () {
    canvas.setAttribute("width", image.width)
    canvas.setAttribute("height", image.height)
    context.drawImage(image, 0, 0);
    let imageData = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream")
    let link = document.getElementById("downloadPngSvg")
    link.setAttribute("href", imageData)
    link.setAttribute("download", modelStore.project.name + ".png")
    link.click()
  }
}

</script>