import type {SidebarConfig} from '@vuepress/theme-default'

export const sidebarEn: SidebarConfig = {
  '/': [
    {
      text: 'Introduction',
      children: [
        '/intro/README.md',
        '/intro/sharing_templates.md'
      ],
      collapsible:true
    },
    {
      text: 'App',
      children: [
        '/application/README.md',
        '/application/user-functions.md',
        '/application/project-overview.md',
        '/application/domain-model.md',
        '/application/enum.md',
        '/application/query.md',
        '/application/command.md',
        '/application/template.md',
        '/application/master-templates.md',
      ],
      collapsible:true
    },
    {
      text: 'Templates',
      children: [
        '/templates/README.md',
        '/templates/model.md',
        '/templates/enum.md',
        '/templates/query.md',
        '/templates/command.md',
        '/templates/tips.md',
      ],
      collapsible:true
    },
    {
      text: 'Installation',
      children: [
        '/installation/README.md',
      ],
      collapsible:true
    },
    {
      text: 'About',
      children: [
        '/about/README.md',
        '/about/releases.md',
        '/about/privacy_policy.md',
        '/about/terms_of_service.md',
      ],
      collapsible:true
    },
  ],
}