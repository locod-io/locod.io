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
  <div>
    <div>
      <div class="flex p-2">
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
        <div class="w-[14rem]"></div>
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
                    class="p-button-sm p-button-outlined"/>
          </a>
        </div>
        <div class="mr-2">
          <a id="downloadSvgSvg" download="file.svg">
            <Button @click="saveAsSvgSvg()"
                    icon="pi pi-download"
                    label="save as .svg"
                    class="p-button-sm p-button-outlined"/>
          </a>
        </div>
        <div>
          <a id="downloadPuml" download="file.puml">
            <Button @click="saveAsPuml()"
                    icon="pi pi-download"
                    label="save as .puml"
                    class="p-button-sm p-button-outlined"/>
          </a>
        </div>

      </div>
    </div>

    <Splitter style="background-color: #EEEEEE;">
      <!-- configuration -->
      <SplitterPanel :size="20">
        <navigation></navigation>
      </SplitterPanel>
      <!-- detail -->
      <SplitterPanel :size="80">
        <detail-wrapper :estate-height="197" style="max-width:1000px;">
          <div v-html="svgData" class="w-full " :style="'zoom: '+zoomLevel+'%'"/>
        </detail-wrapper>
      </SplitterPanel>
    </Splitter>

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

const modelStore = useModelStore();
const schemaStore = useSchemaStore();
const svgData = ref<string>('');
const schemaString = ref<string>('');
const zoomLevel = ref<number>(80);
const plantUmlString = ref<string>('');

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


    let schema = '#lineWidth:1\r\n#zoom:1\r\n#leading: 1.75 \r\n#fontSize: 10\r\n#.box: dashed\r\n';
    plantUmlString.value = '@startuml\r\n\r\nskinparam shadowing false\r\n';
    for (const item of schemaStore.configuration) {
      schema += renderItemString(item) + '\r\n';
    }
    schema += renderEnumRelations();
    schema += renderRelations();
    schemaString.value = schema;
    svgData.value = noml.renderSvg(schema)

    plantUmlString.value += '\r\n@enduml';

  } else {
    schemaString.value = "";
    svgData.value = "";
    plantUmlString.value = "";
  }
}

function renderItemString(item: navigationItem): string {
  let string = '';
  if (item.isSelected) {
    if (item.subjectType === 'model') {
      if (item.isFull || item.isRegular) {
        plantUmlString.value += '\r\npackage "' + item.subject.namespace + '"{ \r\n\tclass ' + item.subject.name + ' {';
        string = '[' + item.name + '|';
        for (let i = 0; i < item.subject.fields.length; i++) {
          string += '- ' + item.subject.fields[i].name;
          plantUmlString.value += '\r\n\t\t' + item.subject.fields[i].name;
          if (item.isFull) {
            string += ' (' + item.subject.fields[i].type + ')';
            plantUmlString.value += ': ' + item.subject.fields[i].type;
          }
          if (i !== (item.subject.fields.length - 1)) string += ';'
        }
        if (item.isFull) {
          string += '|'
          for (let i = 0; i < item.subject.relations.length; i++) {
            string += '- ' + item.subject.relations[i].targetDomainModel.name + '';
            plantUmlString.value += "\r\n\t\t{method} " + item.subject.relations[i].targetDomainModel.name;
            if (item.subject.relations[i].type === 'One-To-Many_Bidirectional') {
              string += 's ';
              plantUmlString.value += 's ';
            }
            if (item.subject.relations[i].type === 'Many-To-Many_Bidirectional') {
              string += 's ';
              plantUmlString.value += 's ';
            }
            if (item.subject.relations[i].type === 'Many-To-Many_Unidirectional') {
              string += 's ';
              plantUmlString.value += 's ';
            }
            if (i !== (item.subject.relations.length - 1)) string += ';'
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
  return string;
}

function renderEnumRelations(): string {
  let string = '';
  let relations = [];
  for (const item of schemaStore.configuration) {
    if (item.isSelected) {
      if (item.subjectType == 'model') {
        for (const field of item.subject.fields) {
          if (field.type === 'enum') {
            let relation = {
              name: item.subject.name + '-' + field.enum.name,
              source: item.subject.name,
              target: field.enum.name
            };
            relations[relation.name] = relation;
          }
        }
      } else if (item.subjectType == 'enum') {
        let relation = {
          name: item.subject.domainModel.name + '-' + item.name,
          source: item.subject.domainModel.name,
          target: item.name
        };
        relations[relation.name] = relation;
      }
    }
  }
  // -- render the relations
  for (const relationKey in relations) {
    const relation = relations[relationKey]
    string += '[<box>' + relation.target + '] --> [' + relation.source + ']\r\n';
    plantUmlString.value += '\r\n' + relation.target + '...> ' + relation.source + '\r\n'
  }
  return string;
}

function renderRelations(): string {
  let string = '';
  let relations = [];
  for (const item of schemaStore.configuration) {
    if (item.isSelected) {
      if (item.subjectType == 'model') {
        for (const relationModel of item.subject.relations) {
          let key = '';
          if (relationModel.type === 'One-To-One_Bidirectional' || relationModel.type === 'Many-To-Many_Bidirectional') {
            let names = [];
            names.push(item.name);
            names.push(relationModel.targetDomainModel.name);
            names.sort();
            key = names[0] + '-' + names[1];
          } else {
            key = item.name + '-' + relationModel.targetDomainModel.name;
          }
          let relation = {
            name: key,
            reverseName: relationModel.targetDomainModel.name + '-' + item.name,
            source: item.name,
            target: relationModel.targetDomainModel.name,
            type: relationModel.type
          };
          relations[relation.name] = relation;
        }
      }
    }
  }

  // -- render the relations
  for (const relationKey in relations) {
    const relation = relations[relationKey]
    switch (relation.type) {
      case 'One-To-Many_Bidirectional':
        string += '[' + relation.source + '] 1 o-> ..* [' + relation.target + ']\r\n';
        plantUmlString.value += '\r\n' + relation.source + ' "1" *--> "..*" ' + relation.target + '\r\n';
        break;
      case 'One-To-Many_Self-referencing':
        string += '[' + relation.source + '] 1 o-> ..* [' + relation.target + ']\r\n';
        plantUmlString.value += '\r\n' + relation.source + ' "1" *--> "..*" ' + relation.target + '\r\n';
        break;
      case 'One-To-One_Bidirectional':
        string += '[' + relation.source + '] 1 <-> 1 [' + relation.target + ']\r\n';
        plantUmlString.value += '\r\n' + relation.source + ' "1" <--> "1" ' + relation.target + '\r\n';
        break;
      case 'One-To-One_Unidirectional':
        string += '[' + relation.source + '] 1 <-> 1 [' + relation.target + ']\r\n';
        plantUmlString.value += '\r\n' + relation.source + ' "1" <--> "1" ' + relation.target + '\r\n';
        break;
      case 'One-To-One_Self-referencing':
        string += '[' + relation.source + '] 1 <-> 1 [' + relation.target + ']\r\n';
        plantUmlString.value += '\r\n' + relation.source + ' "1" <--> "1" ' + relation.target + '\r\n';
        break;
      case 'Many-To-Many_Bidirectional':
        string += '[' + relation.source + '] ..* o-o ..* [' + relation.target + ']\r\n';
        plantUmlString.value += '\r\n' + relation.source + ' "..*" *--* "..*" ' + relation.target + '\r\n';
        break;
      case 'Many-To-Many_Unidirectional':
        string += '[' + relation.source + '] ..* o-o ..* [' + relation.target + ']\r\n';
        plantUmlString.value += '\r\n' + relation.source + ' "..*" *--* "..*" ' + relation.target + '\r\n';
        break;
      case 'Many-To-Many_Self-referencing':
        string += '[' + relation.source + '] ..* o-o ..* [' + relation.target + ']\r\n';
        plantUmlString.value += '\r\n' + relation.source + ' "..*" *--* "..*" ' + relation.target + '\r\n';
        break;
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