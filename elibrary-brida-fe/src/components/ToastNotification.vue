<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-[9999] space-y-3 max-w-sm w-full pointer-events-none">
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="bg-white rounded-lg shadow-lg border border-gray-200 p-4 flex items-start gap-3 pointer-events-auto"
          @click="removeToast(toast.id)"
        >
          <!-- Icon -->
          <div class="flex-shrink-0">
            <div
              v-if="toast.type === 'success'"
              class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center"
            >
              <i-lucide-check class="w-4 h-4 text-green-600" />
            </div>
            <div
              v-else-if="toast.type === 'error'"
              class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center"
            >
              <i-lucide-x class="w-4 h-4 text-red-600" />
            </div>
            <div
              v-else-if="toast.type === 'warning'"
              class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center"
            >
              <i-lucide-alert-triangle class="w-4 h-4 text-amber-600" />
            </div>
            <div
              v-else
              class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center"
            >
              <i-lucide-info class="w-4 h-4 text-blue-600" />
            </div>
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-900">{{ toast.title }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ toast.message }}</p>
          </div>

          <!-- Close Button -->
          <button
            @click.stop="removeToast(toast.id)"
            class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition"
          >
            <i-lucide-x class="w-4 h-4" />
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref } from 'vue'

interface Toast {
  id: number
  type: 'success' | 'error' | 'warning' | 'info'
  title: string
  message: string
}

const toasts = ref<Toast[]>([])
let idCounter = 0

const addToast = (type: Toast['type'], title: string, message: string, duration = 5000) => {
  const id = ++idCounter
  toasts.value.push({ id, type, title, message })

  setTimeout(() => {
    removeToast(id)
  }, duration)
}

const removeToast = (id: number) => {
  const index = toasts.value.findIndex(t => t.id === id)
  if (index > -1) {
    toasts.value.splice(index, 1)
  }
}

defineExpose({
  addToast,
  success: (title: string, message: string, duration?: number) => addToast('success', title, message, duration),
  error: (title: string, message: string, duration?: number) => addToast('error', title, message, duration),
  warning: (title: string, message: string, duration?: number) => addToast('warning', title, message, duration),
  info: (title: string, message: string, duration?: number) => addToast('info', title, message, duration),
})
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%) scale(0.9);
}

.toast-move {
  transition: transform 0.3s ease;
}
</style>
