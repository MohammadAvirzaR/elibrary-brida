import type { RouteLocationNormalized } from 'vue-router'

export interface UserRole {
  id: number
  username: string
  email: string
  role: string
  institution?: string
}

export const ROLES = {
  SUPER_ADMIN: 'super_admin',
  ADMIN: 'admin',
  CONTRIBUTOR: 'contributor',
  REVIEWER: 'reviewer',
  GUEST: 'guest'
} as const

export type RoleType = typeof ROLES[keyof typeof ROLES]

/**
 * Get current user from localStorage
 */
export function getCurrentUser(): UserRole | null {
  const userStr = localStorage.getItem('user')
  if (!userStr) return null

  try {
    return JSON.parse(userStr)
  } catch (error) {
    console.error('Failed to parse user data:', error)
    return null
  }
}

/**
 * Check if user has specific role
 */
export function hasRole(requiredRole: RoleType | RoleType[]): boolean {
  const user = getCurrentUser()
  if (!user) return false

  const roles = Array.isArray(requiredRole) ? requiredRole : [requiredRole]
  return roles.includes(user.role as RoleType)
}

/**
 * Check if user has any of the roles
 */
export function hasAnyRole(roles: RoleType[]): boolean {
  const user = getCurrentUser()
  if (!user) return false

  return roles.includes(user.role as RoleType)
}

/**
 * Check if user is super admin
 */
export function isSuperAdmin(): boolean {
  return hasRole(ROLES.SUPER_ADMIN)
}

/**
 * Check if user is admin (super_admin or admin)
 */
export function isAdmin(): boolean {
  return hasAnyRole([ROLES.SUPER_ADMIN, ROLES.ADMIN])
}

/**
 * Check if user can manage users
 */
export function canManageUsers(): boolean {
  return hasAnyRole([ROLES.SUPER_ADMIN, ROLES.ADMIN])
}

/**
 * Check if user can manage documents
 */
export function canManageDocuments(): boolean {
  return hasAnyRole([ROLES.SUPER_ADMIN, ROLES.ADMIN, ROLES.REVIEWER])
}

/**
 * Check if user can upload documents
 */
export function canUploadDocuments(): boolean {
  return hasAnyRole([ROLES.SUPER_ADMIN, ROLES.ADMIN, ROLES.CONTRIBUTOR])
}

/**
 * Check if user can review documents
 */
export function canReviewDocuments(): boolean {
  return hasAnyRole([ROLES.SUPER_ADMIN, ROLES.ADMIN, ROLES.REVIEWER])
}

/**
 * Check if user can view documents
 */
export function canViewDocuments(): boolean {
  // All authenticated users can view documents
  return getCurrentUser() !== null
}

/**
 * Route guard for role-based access control
 */
export function roleGuard(
  to: RouteLocationNormalized,
  from: RouteLocationNormalized,
  requiredRoles?: RoleType[]
): boolean | { name: string; query?: Record<string, string> } {
  const user = getCurrentUser()

  // If no user, redirect to login
  if (!user) {
    return {
      name: 'login',
      query: { redirect: to.fullPath }
    }
  }

  // If no specific roles required, just check authentication
  if (!requiredRoles || requiredRoles.length === 0) {
    return true
  }

  // Check if user has required role
  if (hasAnyRole(requiredRoles)) {
    return true
  }

  // User doesn't have required role
  console.warn(`Access denied. Required roles: ${requiredRoles.join(', ')}, User role: ${user.role}`)
  return {
    name: 'dashboard'
  }
}
