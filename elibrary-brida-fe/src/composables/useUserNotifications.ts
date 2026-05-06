import { ref } from 'vue'
import api from '@/services/api'
import { useToast } from '@/composables/useToast'

interface UserNotification {
  id: number
  document_id: number | null
  message: string
  sent_at: string | null
  status: 'read' | 'unread'
}

interface UnreadResponse {
  success: boolean
  data: UserNotification[]
}

export function useUserNotifications() {
  const { toast } = useToast()
  const isChecking = ref(false)
  let intervalId: number | null = null

  const checkUnreadNotifications = async () => {
    if (isChecking.value) return
    const token = localStorage.getItem('auth_token')
    if (!token) return

    isChecking.value = true
    try {
      const response = await api.notifications.getUnread() as UnreadResponse
      if (!response.success || response.data.length === 0) return

      for (const notification of response.data) {
        toast.info('Pemberitahuan', notification.message, 7000)
        await api.notifications.markAsRead(notification.id)
      }
    } catch (error) {
      console.error('Failed to load user notifications:', error)
    } finally {
      isChecking.value = false
    }
  }

  const startNotificationPolling = () => {
    void checkUnreadNotifications()
    intervalId = window.setInterval(() => {
      void checkUnreadNotifications()
    }, 10000)
  }

  const stopNotificationPolling = () => {
    if (intervalId) {
      clearInterval(intervalId)
      intervalId = null
    }
  }

  return {
    startNotificationPolling,
    stopNotificationPolling,
  }
}
