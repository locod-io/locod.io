import {defaultTheme, defineUserConfig} from "vuepress";
import {navbarEn, sidebarEn} from "./configs";
import {searchPlugin} from "@vuepress/plugin-search";
import {iconifyPlugin} from "vuepress-plugin-iconify";

export default defineUserConfig({
  base: '/docs/',
  locales: {
    '/': {
      lang: 'en-US',
      title: 'docs',
      description: 'locod.io is a free and open-source web application for data-modeling and code generation. With its template based approach, locod.io can generate code for any kind of languages.',
    },
  },

  // configure default theme
  theme: defaultTheme({
    logo: 'https://www.locod.io/locodio.svg',
    logoDark: 'https://www.locod.io/locodio_w.svg',
    repo: 'https://github.com/locod-io',
    docsDir: 'docs',
    colorMode: 'light',
    backToHome:'Back to home.',
    lastUpdated:false,
    editLink:false,
    contributors:false,
    openInNewWindow:'true',

    // theme-level locales config
    locales: {
      '/': {
        colorMode: 'light',
        toggleColorMode:'false',
        sidebar: sidebarEn,
        navbar: navbarEn
      }
    }
  }),

  plugins: [
    searchPlugin({
      // options
    }),
    iconifyPlugin()
  ],


})