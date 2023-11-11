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
    {
      path: '/my-roadmap',
      name: 'myMegaRoadMap',
      component: () => import('../_lodocio/views/MyRoadmapView.vue')
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
          path: 'settings',
          name: 'settings',
          component: () => import('../views/organisation/ProjectSettingsView.vue')
        },
        {
          path: 'overview',
          name: 'overview',
          component: () => import('../views/model/OverviewView.vue')
        },
        {
          path: 'module',
          name: 'module',
          component: () => import('../views/model/ModuleView.vue')
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

    // documentation routes ------------------------------------------------
    {
      path: '/doc/o/:organisationId/p/:docProjectId',
      name: 'locodioStart',
      component: () => import('../_lodocio/views/DashboardView.vue'),
      props: (route) => {
        const organisationId = castIdParameter(route.params.organisationId.toString());
        const docProjectId = castIdParameter(route.params.docProjectId.toString());
        return {organisationId, docProjectId};
      },
      children: [
        {
          path: 'wiki',
          name: 'project-wiki',
          component: () => import('../_lodocio/views/project/WikiView.vue')
        },
        {
          path: 'roadmap',
          name: 'project-roadmap',
          component: () => import('../_lodocio/views/project/RoadmapView.vue')
        },
        {
          path: 'full-roadmap',
          name: 'organisation-roadmap',
          component: () => import('../_lodocio/views/OrganisationRoadmapView.vue')
        },
        {
          path: 'releases',
          name: 'project-releases',
          component: () => import('../_lodocio/views/project/ReleasesView.vue')
        },
        {
          path: 'trackers',
          name: 'project-trackers',
          component: () => import('../_lodocio/views/project/TrackersView.vue')
        },
        {
          path: 'issues',
          name: 'project-issues',
          component: () => import('../_lodocio/views/project/IssuesView.vue')
        },
        {
          path: 'documents',
          name: 'project-documents',
          component: () => import('../_lodocio/views/project/DocumentsView.vue')
        },
      ],
    },

    // tracker routes ------------------------------------------------------
    {
      path: '/doc/o/:organisationId/p/:docProjectId/t/:trackerId',
      name: 'docStart',
      component: () => import('../_lodocio/views/TrackerView.vue'),
      props: (route) => {
        const organisationId = castIdParameter(route.params.organisationId.toString());
        const docProjectId = castIdParameter(route.params.docProjectId.toString());
        const trackerId = castIdParameter(route.params.trackerId.toString());
        return {organisationId, docProjectId, trackerId};
      },
      children: [
        {
          path: 'configuration',
          name: 'tracker-configuration',
          component: () => import('../_lodocio/views/tracker/ConfigurationView.vue')
        },
        {
          path: 'document',
          name: 'tracker-document-view',
          component: () => import('../_lodocio/views/tracker/DocumentView.vue')
        },
      ],
    },


    // about page ----------------------------------------------------------
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
