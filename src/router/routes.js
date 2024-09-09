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
        path: 'transactions',
        name: 'userTransaction',
        component: () => import('pages/User/Transactions.vue') 
      },
      { 
        path: 'vouchers',
        name: 'userCheckVoucher',
        component: () => import('pages/User/CheckVoucher.vue') 
      },
      { 
        path: 'mangeusers',
        name: 'userManagement',
        component: () => import('pages/User/UserManagement.vue') 
      },
      { 
        path: 'sellers',
        name: 'sellerListPage',
        component: () => import('pages/User/sellerPage.vue') 
      },
      { 
        path: 'inventory',
        name: 'inventoryManagement',
        component: () => import('pages/User/inventoryPage.vue') 
      },
      { 
        path: 'stocks',
        name: 'stockManagement',
        component: () => import('pages/User/stockPage.vue') 
      },
      { 
        path: 'orders',
        name: 'orderManagement',
        component: () => import('pages/User/orderUnitPage.vue') 
      },
    ]
  },
  {
    path: '/mobile/user/',
    component: () => import('layouts/MobileLayout.vue'),
    children: [
      { 
        path: 'mobile-dashboard',
        name: 'mobileUserDashboard',
        component: () => import('pages/Mobile/M-DashboardPage.vue') 
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
