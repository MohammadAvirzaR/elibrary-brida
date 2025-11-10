<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 flex items-center justify-center p-4">
    <div class="text-center max-w-2xl">
      <!-- Success Icon -->
      <div class="mb-8 flex justify-center">
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center">
          <i-lucide-check-circle class="w-16 h-16 text-green-600" />
        </div>
      </div>

      <!-- Welcome Message -->
      <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
        Selamat Datang! 🎉
      </h1>

      <p class="text-xl text-gray-600 mb-2">
        Selamat, Anda telah berhasil login
      </p>

      <!-- User Info -->
      <div v-if="user" class="bg-white rounded-xl shadow-sm p-6 mb-8 inline-block">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
            <span class="text-white font-bold text-2xl">{{ getInitials(user.username) }}</span>
          </div>
          <div class="text-left">
            <p class="text-lg font-semibold text-gray-900">{{ user.username }}</p>
            <p class="text-sm text-gray-600">{{ user.email }}</p>
            <span class="inline-block mt-1 px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
              {{ user.role }}
            </span>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
        <button
          @click="goToDashboard"
          class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-300 ease-in-out transform hover:scale-105 shadow-lg"
        >
          Ke Dashboard
        </button>
        <button
          @click="goToHome"
          class="px-8 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-lg transition duration-300 ease-in-out transform hover:scale-105 shadow-lg border border-gray-300"
        >
          Kembali ke Beranda
        </button>
      </div>

      <!-- Logout Button -->
      <div class="flex justify-center">
        <button
          @click="handleLogout"
          class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition duration-300 ease-in-out shadow-md"
        >
          <i-lucide-log-out class="w-4 h-4" />
          Logout
        </button>
      </div>

      <!-- Additional Info -->
      <div class="mt-8 text-sm text-gray-500">
        <p>Anda akan dialihkan ke dashboard dalam {{ countdown }} detik...</p>
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
const countdown = ref(5)
let countdownInterval: ReturnType<typeof setInterval> | null = null

onMounted(() => {
  // Get user data from localStorage
  const userData = localStorage.getItem('user')
  if (userData) {
    try {
      user.value = JSON.parse(userData)
    } catch (error) {
      console.error('Failed to parse user data:', error)
    }
  }

  // Start countdown
  countdownInterval = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      if (countdownInterval) clearInterval(countdownInterval)
      goToDashboard()
    }
  }, 1000)
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

const goToDashboard = () => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
    countdownInterval = null
  }

  // Redirect based on user role
  const userRole = user.value?.role?.toLowerCase()

  if (userRole === 'super_admin' || userRole === 'admin') {
    // Admin users go to admin dashboard
    router.push('/dashboard')
  } else {
    // Regular users (guest, contributor, reviewer) go to user dashboard
    router.push('/my-dashboard')
  }
}

const goToHome = () => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
    countdownInterval = null
  }
  router.push('/')
}

const handleLogout = () => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
    countdownInterval = null
  }

  // Clear authentication data
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')

  // Redirect to login
  router.push('/login')
}
</script>
