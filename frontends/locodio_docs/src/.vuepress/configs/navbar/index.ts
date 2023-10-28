import type { NavbarConfig } from '@vuepress/theme-default'

export const navbarEn: NavbarConfig = [
  {
    text: 'Home',
    link: 'https://locod.io/',
  },
  {
    text: 'Login',
    link: 'https://locod.io/login',
  },
  {
    text: 'Documentation',
    link: '/',
    children: [
      '/intro/README.md',
      '/application/README.md',
      '/templates/README.md',
      '/installation/README.md',
      '/about/README.md',
    ],
  },
]
