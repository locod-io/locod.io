/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {createRouter, createWebHashHistory} from 'vue-router'

import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHashHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },

    // user routes --------------------------------------------------------
    {
      path: '/my-profile',
      name: 'myProfile',
      component: () => import('../views/user/MyProfileView.vue')
    },
    {
      path: '/change-my-password',
      name: 'changeMyPassword',
      component: () => import('../views/user/ChangePasswordView.vue')
    },
    {
      path: '/my-organisations',
      name: 'myOrganisations',
      component: () => import('../views/organisation/MyOrganisationsView.vue')
    },
    {
      path: '/my-master-templates',
      name: 'myMasterTemplates',
      component: () => import('../views/user/MyMasterTemplatesView.vue')
    },
    {
      path: '/browse-templates',
      name: 'browseTemplates',
      component: () => import('../views/BrowseTemplatesView.vue')
    },

    // model routes --------------------------------------------------------
    {
      path: '/model/o/:organisationId/p/:projectId',
      name: 'modelStart',
      component: () => import('../views/model/ModelStartView.vue'),
      props: (route) => {
        const organisationId = castIdParameter(route.params.organisationId.toString());
        const projectId = castIdParameter(route.params.projectId.toString());
        return {organisationId, projectId};
      },
      children: [
        {
          path: 'overview',
          name: 'overview',
          component: () => import('../views/model/OverviewView.vue')
        },
        {
          path: 'domain-model',
          name: 'domainModel',
          component: () => import('../views/model/DomainModelView.vue')
        },
        {
          path: 'enum',
          name: 'enum',
          component: () => import('../views/model/EnumView.vue')
        },
        {
          path: 'query',
          name: 'query',
          component: () => import('../views/model/QueryView.vue')
        },
        {
          path: 'command',
          name: 'command',
          component: () => import('../views/model/CommandView.vue')
        },
        {
          path: 'template',
          name: 'templates',
          component: () => import('../views/model/TemplatesView.vue')
        },
        {
          path: 'configuration',
          name: 'configuration',
          component: () => import('../views/model/ConfigurationView.vue')
        },
        {
          path: 'documentation',
          name: 'documentation',
          component: () => import('../views/model/DocumentationView.vue')
        },
      ],
    },

    // about page -------------------------------------------------------
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue')
    }
  ]
})

function castIdParameter(routeParamsId: string) {
  const id = Number.parseInt(routeParamsId, 10);
  if (Number.isNaN(id)) {
    return 0;
  }
  return id;
}

export default router
