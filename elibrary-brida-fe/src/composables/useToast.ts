import { ref } from 'vue'

interface ToastInstance {
  success: (title: string, message: string, duration?: number) => void
  error: (title: string, message: string, duration?: number) => void
  warning: (title: string, message: string, duration?: number) => void
  info: (title: string, message: string, duration?: number) => void
}

const toastInstance = ref<ToastInstance | null>(null)

export function useToast() {
  const setToastInstance = (instance: ToastInstance) => {
    toastInstance.value = instance
  }

  const toast = {
    success: (title: string, message: string, duration?: number) => {
      toastInstance.value?.success(title, message, duration)
    },
    error: (title: string, message: string, duration?: number) => {
      toastInstance.value?.error(title, message, duration)
    },
    warning: (title: string, message: string, duration?: number) => {
      toastInstance.value?.warning(title, message, duration)
    },
    info: (title: string, message: string, duration?: number) => {
      toastInstance.value?.info(title, message, duration)
    },
  }

  return {
    toast,
    setToastInstance,
  }
}
