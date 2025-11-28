<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="text-center max-w-lg">
      <!-- Icon -->
      <div class="mb-8 flex justify-center">
        <div class="w-32 h-32 bg-red-100 rounded-full flex items-center justify-center">
          <i-lucide-shield-alert class="w-20 h-20 text-red-600" />
        </div>
      </div>

      <!-- Error Message -->
      <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
        Access Denied
      </h1>

      <p class="text-xl text-gray-600 mb-2">
        Maaf, Anda belum memiliki akses ke halaman ini
      </p>

      <p class="text-sm text-gray-500 mb-8">
        Halaman yang Anda coba akses memerlukan role kontributor. Silakan registrasikan akun anda menjadi kontributor.
      </p>

      <!-- User Info -->
      <div v-if="user" class="bg-white rounded-xl shadow-sm p-6 mb-8 inline-block">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-full flex items-center justify-center">
            <span class="text-white font-bold text-lg">{{ getInitials(user.username) }}</span>
          </div>
          <div class="text-left">
            <p class="text-sm font-semibold text-gray-900">{{ user.username }}</p>
            <p class="text-xs text-gray-600">{{ user.email }}</p>
            <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">
              {{ user.role }}
            </span>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <button
          @click="goBack"
          class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition duration-300 ease-in-out shadow-lg"
        >
          Kembali
        </button>
        <button
          @click="goToHome"
          class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-300 ease-in-out shadow-lg"
        >
          Ke Beranda
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

interface User {
  id: number
  username: string
  email: string
  role: string
  institution?: string
}

const router = useRouter()
const user = ref<User | null>(null)

onMounted(() => {
  const userData = localStorage.getItem('user')
  if (userData) {
    try {
      user.value = JSON.parse(userData)
    } catch (error) {
      console.error('Failed to parse user data:', error)
    }
  }
})

const getInitials = (name: string) => {
  if (!name) return 'U'
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .substring(0, 2)
    .toUpperCase()
}

const goBack = () => {
  router.back()
}

const goToHome = () => {
  router.push('/')
}
</script>
