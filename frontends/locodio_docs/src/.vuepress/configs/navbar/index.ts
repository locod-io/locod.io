import type { NavbarConfig } from '@vuepress/theme-default'

export const navbarEn: NavbarConfig = [
  {
    text: 'Home',
    link: 'https://www.locod.io/',
  },
  {
    text: 'Sign Up',
    link: 'https://www.locod.io/sign-up',
  },
  {
    text: 'Login',
    link: 'https://www.locod.io/login',
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
