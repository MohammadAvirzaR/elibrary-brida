import { ref, onMounted, onUnmounted } from 'vue'

export interface RoleChangeNotification {
  show: boolean
  oldRole: string
  newRole: string
  message: string
}

export function useRoleChangeDetector() {
  const notification = ref<RoleChangeNotification>({
    show: false,
    oldRole: '',
    newRole: '',
    message: ''
  })

  let checkInterval: number | null = null

  const LAST_KNOWN_ROLE_KEY = 'last_known_role'

  const getLastKnownRole = (): string | null => {
    return localStorage.getItem(LAST_KNOWN_ROLE_KEY)
  }

  const setLastKnownRole = (role: string) => {
    localStorage.setItem(LAST_KNOWN_ROLE_KEY, role)
  }

  const getCurrentRole = (): string | null => {
    const userStr = localStorage.getItem('user')
    if (!userStr) return null

    try {
      const user = JSON.parse(userStr)
      return user.role
    } catch (error) {
      console.error('Error parsing user data:', error)
      return null
    }
  }

  const checkRoleChange = () => {
    const currentRole = getCurrentRole()
    if (!currentRole) return

    const lastKnownRole = getLastKnownRole()

    // First time user login or no last known role
    if (!lastKnownRole) {
      // Save current role as last known
      setLastKnownRole(currentRole)
      return
    }

    // Detect role change
    if (currentRole !== lastKnownRole) {
      showNotification(lastKnownRole, currentRole)
      // Update last known role after showing notification
      setLastKnownRole(currentRole)
    }
  }

  const showNotification = (oldRole: string, newRole: string) => {
    const roleLabels: Record<string, string> = {
      'super_admin': 'Super Admin',
      'admin': 'Admin',
      'contributor': 'Contributor',
      'reviewer': 'Reviewer',
      'guest': 'Guest'
    }

    notification.value = {
      show: true,
      oldRole: roleLabels[oldRole] || oldRole,
      newRole: roleLabels[newRole] || newRole,
      message: `Akun Anda telah diubah menjadi ${roleLabels[newRole] || newRole}`
    }

    // Auto hide after 15 seconds
    setTimeout(() => {
      notification.value.show = false
    }, 15000)
  }

  const dismissNotification = () => {
    notification.value.show = false
  }

  const initializeRoleTracking = () => {
    const currentRole = getCurrentRole()
    if (!currentRole) return

    const lastKnownRole = getLastKnownRole()

    // If no last known role, this is first time - just save it
    if (!lastKnownRole) {
      setLastKnownRole(currentRole)
      return
    }

    // Check if role changed since last session
    if (currentRole !== lastKnownRole) {
      showNotification(lastKnownRole, currentRole)
      setLastKnownRole(currentRole)
    }
  }

  const startWatching = () => {
    // Check immediately on mount
    initializeRoleTracking()

    // Then check periodically (every 10 seconds) for in-session changes
    checkInterval = window.setInterval(checkRoleChange, 10000)
  }

  const stopWatching = () => {
    if (checkInterval) {
      clearInterval(checkInterval)
      checkInterval = null
    }
  }

  onMounted(() => {
    startWatching()
  })

  onUnmounted(() => {
    stopWatching()
  })

  return {
    notification,
    dismissNotification,
    startWatching,
    stopWatching
  }
}
