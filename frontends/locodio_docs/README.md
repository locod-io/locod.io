# locod.io docs

`locodio_docs` is a [VuePress](https://v2.vuepress.vuejs.org/) based project.
Development and building is done with [ViteJS](https://vitejs.dev/).
This folder contains the source files for the `docs` sub-folder of the website.
This documentation section is made with [VuePress2](https://v2.vuepress.vuejs.org/).
Content is created with [Markdown](https://www.markdownguide.org/) files.

## Project setup: first time installation
```sh
npm install
```
### Compile and Hot-Reload for Development
```sh
npm run dev
```
A development server will be started. After that you can access this dev server 
typically on `http://localhost:8081/docs/`.

You can start editing the files in the `src` folder.

### Build & Publish the docs for Production

```sh
npm run build

npm run publish (only linux)
```

## Customize configuration

See [Vite Configuration Reference](https://vitejs.dev/config/).