import { createRouter, createWebHistory } from 'vue-router'
import type { RoleType } from '@/middleware/roleGuard'
import { ROLES } from '@/middleware/roleGuard'

declare module 'vue-router' {
  interface RouteMeta {
    requiresAuth?: boolean
    roles?: RoleType[]
    layout?: string
    title?: string
  }
}

interface ImportMeta {
  env?: {
    BASE_URL?: string
  }
}

const router = createRouter({
  history: createWebHistory((import.meta as ImportMeta).env?.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('@/pages/public/HomePage.vue'),
      meta: {
        requiresAuth: false,
        title: 'E-Library BRIDA Sulawesi Tenggara'
      }
    },
    {
      path: '/catalog',
      name: 'catalog',
      component: () => import('@/pages/public/CatalogView.vue'),
      meta: {
        requiresAuth: false,
        title: 'Katalog Buku'
      }
    },
    {
      path: '/search',
      name: 'search',
      component: () => import('@/pages/public/SearchResultsView.vue'),
      meta: {
        requiresAuth: false,
        title: 'Hasil Pencarian'
      }
    },

    {
      path: '/faq',
      name: 'faq',
      component: () => import('@/pages/public/FaqView.vue'),
      meta: {
        requiresAuth: false,
        title: 'FAQ'
      }
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('@/pages/public/AboutView.vue'),
      meta: {
        requiresAuth: false,
        title: 'Tentang Kami'
      }
    },


    {
      path: '/login',
      name: 'login',
      component: () => import('@/pages/auth/LoginView.vue'),
      meta: {
        requiresAuth: false,
        layout: 'auth',
        title: 'Login'
      }
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/pages/auth/RegisterView.vue'),
      meta: {
        requiresAuth: false,
        layout: 'auth',
        title: 'Register'
      }
    },
    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: () => import('@/pages/auth/ForgotPassword.vue'),
      meta: {
        requiresAuth: false,
        layout: 'auth',
        title: 'Forgot Password'
      }
    },

    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('@/pages/dashboard/DashboardView.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.SUPER_ADMIN, ROLES.ADMIN],
        title: 'Dashboard'
      }
    },

    {
      path: '/roles',
      name: 'roles',
      component: () => import('@/pages/dashboard/RolesView.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.SUPER_ADMIN],
        title: 'Role Management'
      }
    },
    {
      path: '/role-management',
      name: 'role-management',
      component: () => import('@/pages/dashboard/RoleManagementView.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.SUPER_ADMIN],
        title: 'Role Management (Discord Style)'
      }
    },

    {
      path: '/users',
      name: 'users',
      component: () => import('@/pages/dashboard/UsersView.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.SUPER_ADMIN],
        title: 'User Management'
      }
    },

    {
      path: '/profile-management',
      name: 'profile-management',
      component: () => import('@/pages/dashboard/ProfileManagementView.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.SUPER_ADMIN, ROLES.ADMIN],
        title: 'Profile Management'
      }
    },

    {
      path: '/become-contributor',
      name: 'become-contributor',
      component: () => import('@/pages/dashboard/BecomeContributorView.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.GUEST],
        title: 'Become Contributor'
      }
    },

    {
      path: '/contributor-requests',
      name: 'contributor-requests',
      component: () => import('@/pages/dashboard/ContributorRequestsView.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.SUPER_ADMIN],
        title: 'Contributor Requests'
      }
    },

    {
      path: '/upload-document',
      name: 'upload-document',
      component: () => import('@/pages/contributor/ContributorDashboard.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.CONTRIBUTOR],
        title: 'Upload Document'
      }
    },

    {
      path: '/contributor-dashboard',
      name: 'contributor-dashboard',
      component: () => import('@/pages/contributor/ContributorDashboard.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.CONTRIBUTOR],
        title: 'Dashboard Kontributor'
      }
    },

    {
      path: '/my-dashboard',
      name: 'my-dashboard',
      component: () => import('@/pages/user/UserDashboard.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.GUEST, ROLES.CONTRIBUTOR, ROLES.REVIEWER],
        title: 'My Dashboard'
      }
    },

    {
      path: '/documents/:id',
      name: 'document-detail',
      component: () => import('@/pages/dashboard/DocumentDetailView.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.SUPER_ADMIN, ROLES.ADMIN, ROLES.REVIEWER, ROLES.CONTRIBUTOR],
        title: 'Detail Dokumen'
      }
    },

    {
      path: '/unauthorized',
      name: 'unauthorized',
      component: () => import('@/pages/UnauthorizedView.vue'),
      meta: {
        requiresAuth: true,
        title: 'Access Denied'
      }
    },
    {
      path: '/unauthorized-kontributor',
      name: 'unauthorized-kontributor',
      component: () => import('@/pages/UnauthorizedViewKontributor.vue'),
      meta: {
        requiresAuth: false,
        title: 'Akses Kontributor'
      }
    },

  ],

  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth',
      }
    } else {
      return { top: 0 }
    }
  }
})

router.beforeEach((to, from, next) => {
  document.title = `${to.meta.title || 'E-Library'} | BRIDA Sulawesi Tenggara`

  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const isAuthenticated = checkAuth()
  const user = getCurrentUser()

  if (requiresAuth && !isAuthenticated) {
    next({
      name: 'login',
      query: { redirect: to.fullPath }
    })
  } else if (to.name === 'login' && isAuthenticated) {
    const userRole = user?.role as RoleType

    if (userRole === ROLES.SUPER_ADMIN || userRole === ROLES.ADMIN) {
      next({ name: 'dashboard' })
    } else {
      next({ name: 'my-dashboard' })
    }
  } else if (requiresAuth && isAuthenticated) {
    const requiredRoles = to.meta.roles

    if (requiredRoles && requiredRoles.length > 0) {
      const userRole = user?.role as RoleType

      if (!requiredRoles.includes(userRole)) {
        console.warn(`Akses ditolak. Role dibutuhkan: ${requiredRoles.join(', ')}, Role user: ${userRole}`)
        next({ name: 'unauthorized' })
        return
      }
    }

    next()
  } else {
    next()
  }
})

function getCurrentUser() {
  const userStr = localStorage.getItem('user')
  if (!userStr) return null

  try {
    return JSON.parse(userStr)
  } catch {
    return null
  }
}

function checkAuth(): boolean {
  const token = localStorage.getItem('auth_token')

  if (token) {
    try {
      return true
    } catch {
      localStorage.removeItem('auth_token')
      return false
    }
  }

  return false
}

export default router
