import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
      meta: { requiresGuest: true }
    },
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { requiresAuth: true }
    },
    {
      path: '/petani',
      name: 'petani',
      component: () => import('../views/PetaniView.vue'),
      meta: { requiresAuth: true, roles: ['admin', 'lapangan', 'superadmin'] }
    },
    {
      path: '/surat-pernyataan',
      name: 'surat-pernyataan',
      component: () => import('../views/SuratPernyataanView.vue'),
      meta: { requiresAuth: true, roles: ['admin', 'lapangan', 'superadmin'] }
    },
    {
      path: '/penggilingan',
      name: 'penggilingan',
      component: () => import('../views/PenggilinganView.vue'),
      meta: { requiresAuth: true, roles: ['admin', 'penggilingan', 'superadmin'] }
    },
    {
      path: '/sub-admins',
      name: 'sub-admins',
      component: () => import('../views/SubAdminView.vue'),
      meta: { requiresAuth: true, roles: ['penggilingan'] }
    },
    {
      path: '/users',
      name: 'users',
      component: () => import('../views/UserManagementView.vue'),
      meta: { requiresAuth: true, roles: ['superadmin'] }
    },
    {
      path: '/wilayah',
      name: 'wilayah',
      component: () => import('../views/WilayahView.vue'),
      meta: { requiresAuth: true, roles: ['superadmin'] }
    },
    {
      path: '/activity-log',
      name: 'activity-log',
      component: () => import('../views/ActivityLogView.vue'),
      meta: { requiresAuth: true, roles: ['superadmin'] }
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue'),
      meta: { requiresAuth: true }
    },
  ],
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  authStore.initAuth()

  const isAuthenticated = authStore.isAuthenticated

  // If route requires authentication and user is not authenticated
  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'login' })
  }
  // If route is for guests only (like login) and user is authenticated
  else if (to.meta.requiresGuest && isAuthenticated) {
    next({ name: 'home' })
  }
  // Check role-based access
  else if (to.meta.roles && !to.meta.roles.includes(authStore.user?.role)) {
    alert('Anda tidak memiliki akses ke halaman ini')
    next({ name: 'home' })
  }
  else {
    next()
  }
})

export default router
