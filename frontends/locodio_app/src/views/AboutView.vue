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
  <general-top-bar type="about"/>
  <div class="overflow-auto p-4 bg-white pb-8 border-b-[1px] border-gray-300 font-sans dark:text-gray-700">
    <div class="flex flex-row mb-8">
      <div class="basis-1/2">
        <div class="text-sm font-bold">Model Driven Development - Template Based Code Generation</div>
        <div class="mt-8 border-b-[1px] border-t-[1px] p-8 border-gray-300 ">
          <p class="italic font-serif">
            <i>"Give me six hours to chop down a tree
              <br>and I will spend the first four sharpening the axe."</i>
          </p>
          <p class="text-right text-xs mt-1">Abraham Lincoln</p>
        </div>
        <p class="mt-8 text-xs"><strong>v{{ version }}</strong></p>
        <div class="mt-2">
          <b>locod.io</b> is an open-source web application for data modeling and code generation.
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

        <div class="mt-5">
          <div class="font-bold text-xs">Integrations with</div>
          <div class="mt-3">
            <a href="http://linear.app/" target="_blank">
              <svg xmlns="http://www.w3.org/2000/svg" width="100" height="25" viewBox="0 0 400 100" fill="#6F6F6F">
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M12.9266 16.3713c-.5283.5806-.4933 1.4714.0617 2.0265l68.5946 68.5946c.5551.555 1.4459.59 2.0265.0617 10.0579-9.1522 16.3713-22.3478 16.3713-37.0179C99.9807 22.402 77.5788 0 49.9445 0 35.2744 0 22.0788 6.31337 12.9266 16.3713ZM4.35334 29.3894c-.25348.5589-.12567 1.2142.30824 1.6481L68.9432 95.3191c.4339.4339 1.0892.5617 1.6481.3083 1.485-.6736 2.9312-1.4176 4.3344-2.2277.8341-.4815.9618-1.6195.2808-2.3005L8.88146 24.7742c-.68097-.681-1.81894-.5532-2.30045.2808-.81013 1.4032-1.55411 2.8494-2.22767 4.3344ZM.453579 47.796c-.300979-.301-.46112014-.7158-.4327856-1.1405.1327026-1.9891.3816396-3.9463.7400796-5.865.214926-1.1505 1.620727-1.5497 2.448307-.7222L59.9124 96.7715c.8275.8276.4283 2.2334-.7222 2.4483-1.9187.3585-3.8759.6074-5.865.7401-.4247.0283-.8395-.1318-1.1405-.4328L.453579 47.796ZM3.93331 61.7589c-1.0331-1.0331-2.70028-.1429-2.32193 1.2683C6.22104 80.2203 19.7604 93.7597 36.9535 98.3693c1.4112.3784 2.3014-1.2888 1.2683-2.3219L3.93331 61.7589ZM201.602 27.535c3.587 0 6.494-2.918 6.494-6.5175S205.189 14.5 201.602 14.5c-3.586 0-6.493 2.918-6.493 6.5175s2.907 6.5175 6.493 6.5175Zm-55.621 56.8396V14.5039h11.54v59.648h31.115v10.2227h-42.655Zm82.136-28.511v28.511h-11.166V34.8555h11.026v8.4876l.14-.0937c1.121-2.6573 2.928-4.8769 5.42-6.6589 2.491-1.8132 5.668-2.7198 9.531-2.7198 3.426 0 6.54.766 9.344 2.2978 2.803 1.5006 5.045 3.7045 6.727 6.6119 1.682 2.9074 2.523 6.4713 2.523 10.6916v30.9026h-11.166V55.0195c0-3.7514-.997-6.5963-2.99-8.5345-1.962-1.9695-4.594-2.9543-7.896-2.9543-2.118 0-4.049.4377-5.793 1.313-1.744.8754-3.13 2.2196-4.158 4.0328-1.028 1.8132-1.542 4.1422-1.542 6.9871Zm101.105 27.6669c2.554 1.0942 5.482 1.6413 8.783 1.6413 2.71 0 5.03-.3439 6.961-1.0317 1.932-.719 3.52-1.6725 4.766-2.8605 1.277-1.1879 2.289-2.4853 3.037-3.8921h.187v6.9871h10.699V50.2833c0-2.4072-.468-4.6111-1.402-6.6119-.934-2.0008-2.289-3.7358-4.065-5.2051-1.744-1.4694-3.862-2.5948-6.354-3.3763-2.491-.8129-5.295-1.2193-8.409-1.2193-4.267 0-7.958.7347-11.073 2.204-3.084 1.4381-5.497 3.3763-7.242 5.8148-1.744 2.4384-2.694 5.1895-2.85 8.2531h10.793c.124-1.438.623-2.7198 1.495-3.8452.872-1.1254 2.056-2.0008 3.551-2.626 1.495-.6565 3.223-.9848 5.186-.9848 1.962 0 3.628.3283 4.999.9848 1.401.6565 2.476 1.5475 3.223 2.6729.748 1.1254 1.122 2.4384 1.122 3.939v.3752c0 1.1254-.39 1.9538-1.168 2.4853-.748.5314-2.025.9222-3.831 1.1723-1.776.2501-4.205.5471-7.289.891-2.523.2813-4.952.7034-7.288 1.2661-2.336.5627-4.423 1.3912-6.261 2.4853-1.806 1.0942-3.239 2.5479-4.298 4.3611-1.059 1.8132-1.588 4.1422-1.588 6.987 0 3.2826.747 6.0336 2.242 8.2532 1.495 2.1884 3.52 3.8453 6.074 4.9707Zm18.081-8.3001c-1.807.9691-4.034 1.4537-6.681 1.4537-2.679 0-4.813-.5627-6.401-1.6881-1.589-1.1567-2.383-2.7355-2.383-4.7362 0-1.5631.436-2.8293 1.308-3.7984.904-.9691 2.087-1.735 3.551-2.2977 1.464-.5628 3.052-.9535 4.765-1.1724 1.246-.1875 2.461-.3751 3.645-.5627 1.183-.2188 2.289-.422 3.317-.6096 1.028-.2188 1.9-.4377 2.616-.6565.748-.2188 1.293-.4533 1.635-.7034v5.5334c0 1.9382-.451 3.7202-1.355 5.3458-.872 1.5944-2.211 2.8917-4.017 3.8921Zm26.094 9.1442V34.8555h10.745v8.1594h.141c.903-2.8136 2.32-4.955 4.251-6.4244 1.962-1.5005 4.532-2.2508 7.709-2.2508.779 0 1.48.0312 2.102.0938.655.0312 1.2.0625 1.636.0937v10.082c-.405-.0625-1.122-.1406-2.149-.2344-1.028-.0938-2.118-.1407-3.271-.1407-1.838 0-3.519.422-5.046 1.2661-1.526.8441-2.741 2.1415-3.644 3.8921-.872 1.7195-1.308 3.8922-1.308 6.5182v28.4641h-11.166Zm-177.401 0V34.8555h11.166v49.5191h-11.166Zm84.238-2.204c3.582 2.2196 7.834 3.3294 12.755 3.3294 3.8 0 7.257-.6878 10.372-2.0633 3.146-1.4068 5.762-3.3294 7.849-5.7678 2.087-2.4697 3.442-5.3146 4.065-8.5346h-10.512c-.468 1.4693-1.231 2.7667-2.29 3.8921-1.027 1.0942-2.32 1.9539-3.877 2.5792-1.558.6252-3.364.9378-5.42.9378-2.772 0-5.155-.6252-7.148-1.8757-1.962-1.2505-3.457-2.9855-4.485-5.2051-.933-2.043-1.443-4.3564-1.529-6.9402h35.915v-3.0012c0-3.8139-.561-7.284-1.682-10.4102-1.121-3.1575-2.71-5.8773-4.766-8.1594-2.055-2.3134-4.531-4.0953-7.428-5.3458-2.866-1.2505-6.058-1.8757-9.578-1.8757-4.578 0-8.627 1.1098-12.147 3.3294-3.52 2.2196-6.276 5.2833-8.27 9.191-1.993 3.9078-2.99 8.3782-2.99 13.4114 0 5.0019.966 9.4568 2.897 13.3645 1.931 3.8765 4.688 6.9246 8.269 9.1442Zm23.501-32.7783c-1.028-2.1258-2.492-3.767-4.392-4.9237-1.9-1.1567-4.142-1.7351-6.728-1.7351-2.554 0-4.781.5784-6.681 1.7351-1.868 1.1567-3.332 2.7979-4.391 4.9237-.756 1.5396-1.234 3.2903-1.434 5.2521h25.059c-.2-1.9618-.678-3.7125-1.433-5.2521Z"/>
              </svg>
            </a>
          </div>
        </div>

        <div class="mt-8">
          <div class="font-bold text-xs">Makes use of</div>
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
                    src="https://postcss.org/assets/postcss-83d93145.svg" width="35"></a></div>
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
              <div class="mt-8 ml-4"><a href="https://tailwindcss.com/" target="_blank">
                <svg viewBox="0 0 248 31" class="text-slate-900 w-auto h-5">
                  <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M25.517 0C18.712 0 14.46 3.382 12.758 10.146c2.552-3.382 5.529-4.65 8.931-3.805 1.941.482 3.329 1.882 4.864 3.432 2.502 2.524 5.398 5.445 11.722 5.445 6.804 0 11.057-3.382 12.758-10.145-2.551 3.382-5.528 4.65-8.93 3.804-1.942-.482-3.33-1.882-4.865-3.431C34.736 2.92 31.841 0 25.517 0zM12.758 15.218C5.954 15.218 1.701 18.6 0 25.364c2.552-3.382 5.529-4.65 8.93-3.805 1.942.482 3.33 1.882 4.865 3.432 2.502 2.524 5.397 5.445 11.722 5.445 6.804 0 11.057-3.381 12.758-10.145-2.552 3.382-5.529 4.65-8.931 3.805-1.941-.483-3.329-1.883-4.864-3.432-2.502-2.524-5.398-5.446-11.722-5.446z"
                        fill="#ccc"></path>
                  <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M76.546 12.825h-4.453v8.567c0 2.285 1.508 2.249 4.453 2.106v3.463c-5.962.714-8.332-.928-8.332-5.569v-8.567H64.91V9.112h3.304V4.318l3.879-1.143v5.937h4.453v3.713zM93.52 9.112h3.878v17.849h-3.878v-2.57c-1.365 1.891-3.484 3.034-6.285 3.034-4.884 0-8.942-4.105-8.942-9.389 0-5.318 4.058-9.388 8.942-9.388 2.801 0 4.92 1.142 6.285 2.999V9.112zm-5.674 14.636c3.232 0 5.674-2.392 5.674-5.712s-2.442-5.711-5.674-5.711-5.674 2.392-5.674 5.711c0 3.32 2.442 5.712 5.674 5.712zm16.016-17.313c-1.364 0-2.477-1.142-2.477-2.463a2.475 2.475 0 012.477-2.463 2.475 2.475 0 012.478 2.463c0 1.32-1.113 2.463-2.478 2.463zm-1.939 20.526V9.112h3.879v17.849h-3.879zm8.368 0V.9h3.878v26.06h-3.878zm29.053-17.849h4.094l-5.638 17.849h-3.807l-3.735-12.03-3.771 12.03h-3.806l-5.639-17.849h4.094l3.484 12.315 3.771-12.315h3.699l3.734 12.315 3.52-12.315zm8.906-2.677c-1.365 0-2.478-1.142-2.478-2.463a2.475 2.475 0 012.478-2.463 2.475 2.475 0 012.478 2.463c0 1.32-1.113 2.463-2.478 2.463zm-1.939 20.526V9.112h3.878v17.849h-3.878zm17.812-18.313c4.022 0 6.895 2.713 6.895 7.354V26.96h-3.878V16.394c0-2.713-1.58-4.14-4.022-4.14-2.55 0-4.561 1.499-4.561 5.14v9.567h-3.879V9.112h3.879v2.285c1.185-1.856 3.124-2.749 5.566-2.749zm25.282-6.675h3.879V26.96h-3.879v-2.57c-1.364 1.892-3.483 3.034-6.284 3.034-4.884 0-8.942-4.105-8.942-9.389 0-5.318 4.058-9.388 8.942-9.388 2.801 0 4.92 1.142 6.284 2.999V1.973zm-5.674 21.775c3.232 0 5.674-2.392 5.674-5.712s-2.442-5.711-5.674-5.711-5.674 2.392-5.674 5.711c0 3.32 2.442 5.712 5.674 5.712zm22.553 3.677c-5.423 0-9.481-4.105-9.481-9.389 0-5.318 4.058-9.388 9.481-9.388 3.519 0 6.572 1.82 8.008 4.605l-3.34 1.928c-.79-1.678-2.549-2.749-4.704-2.749-3.16 0-5.566 2.392-5.566 5.604 0 3.213 2.406 5.605 5.566 5.605 2.155 0 3.914-1.107 4.776-2.749l3.34 1.892c-1.508 2.82-4.561 4.64-8.08 4.64zm14.472-13.387c0 3.249 9.661 1.285 9.661 7.89 0 3.57-3.125 5.497-7.003 5.497-3.591 0-6.177-1.607-7.326-4.177l3.34-1.927c.574 1.606 2.011 2.57 3.986 2.57 1.724 0 3.052-.571 3.052-2 0-3.176-9.66-1.391-9.66-7.781 0-3.356 2.909-5.462 6.572-5.462 2.945 0 5.387 1.357 6.644 3.713l-3.268 1.82c-.647-1.392-1.904-2.035-3.376-2.035-1.401 0-2.622.607-2.622 1.892zm16.556 0c0 3.249 9.66 1.285 9.66 7.89 0 3.57-3.124 5.497-7.003 5.497-3.591 0-6.176-1.607-7.326-4.177l3.34-1.927c.575 1.606 2.011 2.57 3.986 2.57 1.724 0 3.053-.571 3.053-2 0-3.176-9.66-1.391-9.66-7.781 0-3.356 2.908-5.462 6.572-5.462 2.944 0 5.386 1.357 6.643 3.713l-3.268 1.82c-.646-1.392-1.903-2.035-3.375-2.035-1.401 0-2.622.607-2.622 1.892z"
                        fill="currentColor"></path>
                </svg>
              </a>
              </div>
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
            <div class="mt-8 ml-4"><a href="https://www.primevue.org/" target="_blank"><img
                src="https://primefaces.org/cdn/primevue/images/primevue-logo-dark.svg" width="100"></a></div>
            <div class="mt-6 ml-4"><a href="https://axios-http.com/" target="_blank"><img
                src="https://axios-http.com/assets/logo.svg" width="100"></a></div>
            <div class="mt-4 ml-4"><a href="https://vuelidate-next.netlify.app/" target="_blank"><img
                src="https://vuelidate-next.netlify.app/logotype.png" width="100"></a></div>
          </div>
        </div>

      </div>
    </div>

    <div class="flex flex-row">
      <div class="basis-1/3">
        <div class="text-xs">
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
            <link-button link="https://vue-showdown.js.org/"/>
          </div>
        </div>
      </div>
      <div class="basis-1/3">
        <div class="text-xs">
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
          <div>
            <link-button link="https://www.dropzone.dev/"/>
          </div>
          <div>
            <link-button link="https://vueflow.dev/"/>
          </div>
          <div>
            <link-button link="https://github.com/bubkoo/html-to-image"/>
          </div>
          <div>
            <link-button link="https://github.com/softonic/graphql-client"/>
          </div>
        </div>
      </div>
      <div class="basis-1/3"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import LinkButton from "@/components/common/linkButton.vue";
import ModelTopBar from "@/_common/topBar/modelTopBar.vue";
import GeneralTopBar from "@/_common/topBar/generalTopBar.vue";

const version = import.meta.env.VITE_VERSION as string;
</script>

<style scoped>

img {
  filter: grayscale(100%);
}

</style>
