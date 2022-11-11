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
  <div class="overflow-auto" style="position: absolute;left: 20px;right: 0;top: 49px;bottom: 30px">
    <div class="flex flex-row mt-8 mb-8">
      <div class="basis-1/2">
        <div class="text-sm">Model Driven Development - Template Based Code Generation</div>
        <div class="mt-8 border-b-[1px] border-t-[1px] p-8 border-blue-300 ">
          <p class="italic">
            <i>"Give me six hours to chop down a tree and
              I will spend the first four sharpening the axe."</i>
          </p>
          <p class="text-right text-xs mt-1">Abraham Lincoln</p>
        </div>
        <p class="mt-8 text-xs"><strong>v{{ version }}</strong></p>
        <div class="mt-2">
          locod.io is a free and open-source web application for data modeling and code generation.
          With its template based approach, locod.io can generate code for any kind of languages.
        </div>
        <div class="mt-4">
          <strong class="text-xs">Open Source</strong>
          <p class="text-xs">locod.io is an open source template-based code generation technology.
            As such, you can use it freely, fork it and even contribute back.
            <a href="https://github.com/locod-io/locod.io/blob/main/LICENSE" target="_blank">locod.io is licensed under
              the MIT License.</a>
          </p>
          <p class="mt-2">
            <a href="https://github.com/locod-io" target="_blank" class="text-blue-500">
              <span class="mt-1"><i class="pi pi-github"></i></span> https://github.com/locod-io
            </a>
          </p>
        </div>
      </div>
      <div class="basis-1/2 ml-8">
        <div>Built upon the Shoulders of Giants</div>

        <div class="flex flex-row">
          <!-- tooling -->
          <div class="basis-1/3">
            <div class="mt-8 ml-14"><a href="https://getcomposer.org/" target="_blank"><img
                src="https://getcomposer.org/img/logo-composer-transparent3.png" width="80"></a></div>
            <div class="mt-8 ml-14"><a href="https://nodejs.org/en/" target="_blank"><img
                src="https://upload.wikimedia.org/wikipedia/commons/d/d9/Node.js_logo.svg" width="80"></a></div>
            <div class="mt-8 ml-14"><a href="https://www.npmjs.com/" target="_blank"><img
                src="https://upload.wikimedia.org/wikipedia/commons/d/db/Npm-logo.svg" width="80"></a></div>
            <div class="mt-10 ml-14"><a href="https://git-scm.com/" target="_blank"><img
                src="https://git-scm.com/images/logos/downloads/Git-Logo-2Color.svg" width="80"></a></div>
            <div class="flex">
              <div>
                <div class="mt-10 ml-8"><a href="https://postcss.org/" target="_blank"><img
                    src="https://postcss.org/assets/postcss.83d93145.svg" width="35"></a></div>
              </div>
              <div>
                <div class="mt-8 ml-6"><a href="https://webpack.js.org/" target="_blank"><img
                    src="https://raw.githubusercontent.com/webpack/media/master/logo/logo-on-white-bg.png" width="120"></a>
                </div>
              </div>
            </div>
          </div>
          <!-- backend -->
          <div class="basis-1/3">
            <div class="mt-8">
              <div class="ml-8"><a href="https://www.mysql.com/" target="_blank"><img
                  src="https://www.mysql.com/common/logos/logo-mysql-170x115.png" width="100"></a></div>
              <div class="mt-6 ml-8"><a href="https://www.php.net/" target="_blank"><img
                  src="https://www.php.net/images/logos/new-php-logo.svg" width="100"/></a></div>
              <div class="mt-4 ml-2"><a href="https://www.symfony.com/" target="_blank"><img
                  src="https://symfony.com/logos/symfony_black_02.svg" width="200"></a></div>
              <div class="flex">
                <div class="mt-4 ml-6 "><a href="https://www.doctrine-project.org/" target="_blank"><img
                    src="https://www.doctrine-project.org/logos/doctrine-logo.svg?1a5b7c" width="50"></a></div>
                <div class="mt-4 ml-8"><a href="https://twig.symfony.com/" target="_blank"><img
                    src="https://twig.symfony.com/images/logo.png" width="80"></a></div>
              </div>
              <div class="mt-8 ml-4"><a href="https://tailwindcss.com/" target="_blank"><img
                  src="https://tailwindcss.com/_next/static/media/tailwindcss-logotype.ed60a6f85c663923c4d6ee9d85f359cd.svg"
                  width="150"></a></div>
            </div>
          </div>
          <!-- tooling -->
          <div class="basis-1/3">
            <div class="mt-8"><a href="https://www.typescriptlang.org/" target="_blank"><img
                src="../assets/ts-lettermark-blue.svg" width="120"></a></div>
            <div class="mt-6 ml-10"><a href="https://vitejs.dev/" target="_blank"><img
                src="https://vitejs.dev/logo-with-shadow.png" width="40"></a></div>
            <div class="mt-6 ml-6"><a href="https://vuejs.org/" target="_blank"><img
                src="https://upload.wikimedia.org/wikipedia/commons/9/95/Vue.js_Logo_2.svg" width="80"></a></div>
            <div class="mt-4 ml-12"><a href="https://pinia.vuejs.org/" target="_blank"><img
                src="https://pinia.vuejs.org/logo.svg" width="30"></a></div>
            <div class="mt-8 ml-4"><a href="https://www.primefaces.org/primevue/" target="_blank"><img
                src="https://www.primefaces.org/primevue/demo/images/primevue-logo-dark.svg" width="100"></a></div>
            <div class="mt-6 ml-4"><a href="https://axios-http.com/" target="_blank"><img
                src="https://axios-http.com/assets/logo.svg" width="100"></a></div>
            <div class="mt-4 ml-4"><a href="https://vuelidate-next.netlify.app/" target="_blank"><img
                src="https://vuelidate-next.netlify.app/logotype.png" width="100"></a></div>
          </div>
        </div>
        <div class="mt-8 text-xs">
          <div>
            <link-button link="https://phpunit.de/"/>
          </div>
          <div>
            <link-button link="https://github.com/stof/StofDoctrineExtensionsBundle"/>
          </div>
          <div>
            <link-button link="https://github.com/DamienHarper/auditor-bundle"/>
          </div>
          <div>
            <link-button link="https://router.vuejs.org/"/>
          </div>
          <div>
            <link-button link="https://github.com/primefaces/primeicons"/>
          </div>
          <div>
            <link-button link="https://www.nomnoml.com/"/>
          </div>
          <div>
            <link-button link="https://github.com/SortableJS/vue.draggable.next"/>
          </div>
          <div>
            <link-button link="https://antoniandre.github.io/simple-syntax-highlighter/"/>
          </div>
          <div>
            <link-button link="https://github.com/CarterLi/vue3-ace-editor"/>
          </div>
          <div>
            <link-button link="https://github.com/zxcvbn-ts/zxcvbn"/>
          </div>
          <div>
            <link-button link="https://blog.logrocket.com/author/mateuszrybczonek/"/>
          </div>
          <div>
            <link-button link="https://github.com/bjeavons/zxcvbn-php"/>
          </div>
          <div>
            <link-button link="https://github.com/aacassandra/vue3-progressbar"/>
          </div>
          <div>
            <link-button link="https://tiptap.dev/"/>
          </div>
          <div>
            <link-button link="https://fontawesome.com/"/>
          </div>
          <div>
            <link-button link="http://samherbert.net/svg-loaders/"/>
          </div>
          <div>
            <link-button link="https://vue2-timeago.netlify.app/"/>
          </div>
        </div>
      </div>
    </div>

    <div class="text-xs text-gray-400 flex mt-14">
      <div>
        <a rel="license" target="_blank" href="http://creativecommons.org/licenses/by/4.0/" class="mx-auto">
          <img alt="Creative Commons License" style="border-width:0" class="mx-auto" width="50"
               src="https://i.creativecommons.org/l/by/4.0/88x31.png"/>
        </a>
      </div>
      <div class="ml-2">
        The interface of this application (except for the logo's displayed on this page) is licensed under a
        <a rel="license" href="http://creativecommons.org/licenses/by/4.0/" target="_blank">
          Creative Commons Attribution 4.0 International License
        </a>.
        <br>
        &nbsp;
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import LinkButton from "@/components/common/linkButton.vue";
const version = import.meta.env.VITE_VERSION as string;
</script>

<style scoped>

img {
  filter: grayscale(100%);
}

</style>
