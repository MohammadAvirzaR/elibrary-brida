import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { ROLES, getCurrentUser, hasRole, hasAnyRole, type RoleType } from '@/middleware/roleGuard'
import api from '@/services/api'

export function useAuth() {
  const router = useRouter()
  const user = computed(() => getCurrentUser())

  const isAuthenticated = computed(() => {
    return !!localStorage.getItem('auth_token') && user.value !== null
  })

  const userRole = computed(() => user.value?.role as RoleType | null)

  // Role checks
  const isSuperAdmin = computed(() => hasRole(ROLES.SUPER_ADMIN))
  const isAdmin = computed(() => hasAnyRole([ROLES.SUPER_ADMIN, ROLES.ADMIN]))
  const isContributor = computed(() => hasRole(ROLES.CONTRIBUTOR))
  const isReviewer = computed(() => hasRole(ROLES.REVIEWER))
  const isGuest = computed(() => hasRole(ROLES.GUEST))

  // Permission checks
  const canManageUsers = computed(() => hasAnyRole([ROLES.SUPER_ADMIN, ROLES.ADMIN]))
  const canManageRoles = computed(() => hasRole(ROLES.SUPER_ADMIN))
  const canManageDocuments = computed(() => hasAnyRole([ROLES.SUPER_ADMIN, ROLES.ADMIN, ROLES.REVIEWER]))
  const canUploadDocuments = computed(() => hasAnyRole([ROLES.SUPER_ADMIN, ROLES.ADMIN, ROLES.CONTRIBUTOR]))
  const canReviewDocuments = computed(() => hasAnyRole([ROLES.SUPER_ADMIN, ROLES.ADMIN, ROLES.REVIEWER]))
  const canViewDashboard = computed(() => hasAnyRole([ROLES.SUPER_ADMIN, ROLES.ADMIN]))

  const logout = () => {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
    window.dispatchEvent(new Event('auth-changed'))
    router.push('/login')
  }

  const checkPermission = (requiredRoles: RoleType[]) => {
    return hasAnyRole(requiredRoles)
  }

  // Refresh user data from server
  const refreshUserData = async () => {
    const token = localStorage.getItem('auth_token')
    if (!token) return null

    try {
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const data: any = await api.auth.me()

      // Update user data in localStorage
      if (data.user) {
        localStorage.setItem('user', JSON.stringify({
          id: data.user.id,
          name: data.user.name || data.user.full_name,
          username: data.user.username || data.user.name || data.user.full_name,
          email: data.user.email,
          institution: data.user.institution || data.user.unit_name,
          role: data.user.role?.name || data.user.role || 'guest'
        }))
        // Dispatch event to notify components
        window.dispatchEvent(new Event('auth-changed'))
        return data.user
      }
    } catch (error) {
      console.error('Failed to refresh user data:', error)
    }
    return null
  }

  return {
    user,
    isAuthenticated,
    userRole,
    isSuperAdmin,
    isAdmin,
    isContributor,
    isReviewer,
    isGuest,
    canManageUsers,
    canManageRoles,
    canManageDocuments,
    canUploadDocuments,
    canReviewDocuments,
    canViewDashboard,
    logout,
    checkPermission,
    refreshUserData,
    ROLES
  }
}
