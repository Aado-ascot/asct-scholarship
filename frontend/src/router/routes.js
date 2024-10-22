const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/IndexPage.vue') }
    ]
  },
  {
    path: '/user/',
    component: () => import('layouts/LoggedInLayout.vue'),
    children: [
      { 
        path: 'dashboard',
        name: 'userDashboard',
        component: () => import('pages/User/Dashboard.vue') 
      },
      { 
        path: 'scholarships',
        name: 'applyScholarship',
        component: () => import('pages/User/Scholarship.vue') 
      },
      { 
        path: 'documents',
        name: 'attachedDocuments',
        component: () => import('pages/User/Documents.vue') 
      },
    ]
  },
  {
    path: '/admin/',
    component: () => import('layouts/LoggedInLayout.vue'),
    children: [
      { 
        path: 'dashboard',
        name: 'adminDashboard',
        component: () => import('pages/Admin/Dashboard.vue') 
      },
      { 
        path: 'userManagement',
        name: 'adminUserManagement',
        component: () => import('pages/Admin/UsersPage.vue') 
      },
      { 
        path: 'scholarshipManagement',
        name: 'adminScholarManagement',
        component: () => import('pages/Admin/ScholarshipPage.vue') 
      },
    ]
  },
  

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
