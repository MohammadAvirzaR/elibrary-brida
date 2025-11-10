import { createRouter, createWebHistory } from 'vue-router'
import type { RoleType } from '@/middleware/roleGuard'
import { ROLES } from '@/middleware/roleGuard'

// Extend route meta to include roles
declare module 'vue-router' {
  interface RouteMeta {
    requiresAuth?: boolean
    roles?: RoleType[]
    layout?: string
    title?: string
  }
}

const router = createRouter({
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  history: createWebHistory((import.meta as any).env?.BASE_URL),
  routes: [
    // ========== PUBLIC ROUTES ==========
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
      path: '/detail/:id',
      name: 'detail',
      component: () => import('@/pages/public/DetailView.vue'),
      meta: {
        requiresAuth: false,
        title: 'Detail Buku'
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
      path: '/upload',
      name: 'upload',
      component: () => import('@/pages/public/UploadMandiri.vue'),
      meta: {
        requiresAuth: false,
        title: 'Unggah Mandiri'
      }
    },

    // ========== AUTH ROUTES ==========
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
      path: '/welcome',
      name: 'welcome',
      component: () => import('@/pages/WelcomeView.vue'),
      meta: {
        requiresAuth: true,
        title: 'Welcome'
      }
    },

    // ========== DASHBOARD ROUTES (Protected) ==========
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('@/pages/dashboard/DashboardView.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.SUPER_ADMIN, ROLES.ADMIN], // Only super_admin and admin can access
        title: 'Dashboard'
      }
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('@/pages/dashboard/ProfileView.vue'),
      meta: {
        requiresAuth: true,
        title: 'Profile'
      }
    },
    {
      path: '/notifications',
      name: 'notifications',
      component: () => import('@/pages/dashboard/NotificationView.vue'),
      meta: {
        requiresAuth: true,
        title: 'Notifications'
      }
    },
    {
      path: '/settings',
      name: 'settings',
      component: () => import('@/pages/dashboard/SettingView.vue'),
      meta: {
        requiresAuth: true,
        title: 'Settings'
      }
    },

    // ========== ROLES ROUTES (Protected) ==========
    {
      path: '/roles',
      name: 'roles',
      component: () => import('@/pages/dashboard/RolesView.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.SUPER_ADMIN], // Only super_admin can manage roles
        title: 'Role Management'
      }
    },

    // ========== USER ROUTES (Protected) ==========
    {
      path: '/users',
      name: 'users',
      component: () => import('@/pages/dashboard/UsersView.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.SUPER_ADMIN, ROLES.ADMIN], // super_admin and admin can manage users
        title: 'User Management'
      }
    },

    // ========== USER DASHBOARD (For regular users) ==========
    {
      path: '/my-dashboard',
      name: 'my-dashboard',
      component: () => import('@/pages/user/UserDashboard.vue'),
      meta: {
        requiresAuth: true,
        roles: [ROLES.GUEST, ROLES.CONTRIBUTOR, ROLES.REVIEWER], // Regular users
        title: 'My Dashboard'
      }
    },

    // ========== 404 NOT FOUND ==========
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
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('@/pages/NotFoundView.vue'),
      meta: {
        title: '404 - Page Not Found'
      }
    }
  ],

  // Scroll behavior - auto scroll to top saat pindah page
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

// ========== NAVIGATION GUARD ==========
// Proteksi route yang perlu authentication dan role-based access control
router.beforeEach((to, from, next) => {
  // Update document title
  document.title = `${to.meta.title || 'E-Library'} | BRIDA Sulawesi Tenggara`

  // Check if route requires authentication
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)

  // Check if user is logged in
  const isAuthenticated = checkAuth()

  // Get current user
  const user = getCurrentUser()

  if (requiresAuth && !isAuthenticated) {
    // Redirect to login if not authenticated
    // Save attempted route for redirect after login
    next({
      name: 'login',
      query: { redirect: to.fullPath }
    })
  } else if (to.name === 'login' && isAuthenticated) {
    // Redirect to welcome if already logged in
    next({ name: 'welcome' })
  } else if (requiresAuth && isAuthenticated) {
    // Check role-based access if route has role requirements
    const requiredRoles = to.meta.roles

    if (requiredRoles && requiredRoles.length > 0) {
      // Check if user has required role
      const userRole = user?.role as RoleType

      if (!requiredRoles.includes(userRole)) {
        // User doesn't have required role
        console.warn(`Access denied. Required roles: ${requiredRoles.join(', ')}, User role: ${userRole}`)

        // Redirect to unauthorized page
        next({ name: 'unauthorized' })
        return
      }
    }

    // Allow navigation
    next()
  } else {
    // Allow navigation for public routes
    next()
  }
})

// ========== HELPER FUNCTIONS ==========

/**
 * Get current user from localStorage
 */
function getCurrentUser() {
  const userStr = localStorage.getItem('user')
  if (!userStr) return null

  try {
    return JSON.parse(userStr)
  } catch {
    return null
  }
}

// ========== AUTH CHECK FUNCTION ==========
/**
 * Check if user is authenticated
 * TODO: Replace with your actual auth logic
 */
function checkAuth(): boolean {
  // Simple check using localStorage
  const token = localStorage.getItem('auth_token')

  // Optional: Check token expiration
  if (token) {
    try {
      // If you have JWT, decode and check expiration
      // const payload = JSON.parse(atob(token.split('.')[1]))
      // return payload.exp * 1000 > Date.now()
      return true
    } catch {
      // Invalid token
      localStorage.removeItem('auth_token')
      return false
    }
  }

  return false

  // Alternative: Use Pinia store
  // import { useAuthStore } from '@/stores/auth'
  // const authStore = useAuthStore()
  // return authStore.isAuthenticated
}

export default router
