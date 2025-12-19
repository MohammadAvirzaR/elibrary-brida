<template>
  <Transition name="notification">
    <div
      v-if="show"
      class="fixed top-4 right-4 z-[9999] max-w-md"
    >
      <div class="bg-white rounded-xl shadow-2xl border-l-4 border-blue-600 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                <i-lucide-shield-check class="w-6 h-6 text-white" />
              </div>
              <div>
                <h3 class="text-white font-bold text-lg">Role Updated!</h3>
                <p class="text-white/80 text-sm">Your account role has changed</p>
              </div>
            </div>
            <button
              @click="$emit('dismiss')"
              class="text-white/80 hover:text-white transition p-1 rounded-lg hover:bg-white/10"
            >
              <i-lucide-x class="w-5 h-5" />
            </button>
          </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-5">
          <div class="flex items-center gap-4 mb-4">
            <!-- Old Role -->
            <div class="flex-1 text-center">
              <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-lg mb-2">
                <span class="text-gray-500 text-sm font-medium">{{ oldRole }}</span>
              </div>
              <p class="text-xs text-gray-500">Previous Role</p>
            </div>

            <!-- Arrow -->
            <div class="flex-shrink-0">
              <i-lucide-arrow-right class="w-6 h-6 text-blue-600" />
            </div>

            <!-- New Role -->
            <div class="flex-1 text-center">
              <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg mb-2">
                <i-lucide-star class="w-4 h-4 text-white" />
                <span class="text-white text-sm font-bold">{{ newRole }}</span>
              </div>
              <p class="text-xs text-blue-600 font-semibold">New Role</p>
            </div>
          </div>

          <!-- Message -->
          <div class="bg-blue-50 rounded-lg p-4 mb-4">
            <p class="text-sm text-blue-900">
              <i-lucide-info class="w-4 h-4 inline mr-2" />
              {{ message }}
            </p>
          </div>

          <!-- Actions -->
          <div class="flex gap-3">
            <button
              @click="reloadPage"
              class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition font-semibold flex items-center justify-center gap-2"
            >
              <i-lucide-refresh-cw class="w-4 h-4" />
              Reload Page
            </button>
            <button
              @click="$emit('dismiss')"
              class="px-4 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold"
            >
              Dismiss
            </button>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="h-1 bg-gray-200">
          <div
            class="h-full bg-gradient-to-r from-blue-600 to-purple-600 transition-all ease-linear"
            :style="{ width: show ? '0%' : '100%', transitionDuration: '10000ms' }"
          ></div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
defineProps<{
  show: boolean
  oldRole: string
  newRole: string
  message: string
}>()

defineEmits<{
  dismiss: []
}>()

const reloadPage = () => {
  window.location.reload()
}
</script>

<style scoped>
.notification-enter-active {
  animation: slideInRight 0.5s ease-out;
}

.notification-leave-active {
  animation: slideOutRight 0.5s ease-in;
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes slideOutRight {
  from {
    transform: translateX(0);
    opacity: 1;
  }
  to {
    transform: translateX(100%);
    opacity: 0;
  }
}
</style>
