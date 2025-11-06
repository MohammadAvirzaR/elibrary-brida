<template id="login-view">
  <AuthLayout>
    <div class="min-h-screen flex items-center justify-center p-4 relative">
      <!-- Back Button - Fixed to left side -->
      <button
        @click="router.push('/')"
        class="fixed left-8 top-8 w-12 h-12 bg-black rounded-full flex items-center justify-center hover:bg-gray-800 transition shadow-lg z-10"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <div class="w-full max-w-md">
        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
          <!-- Logo and Title -->
          <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
              <img
                src="@/assets/brin-logo-trans.png"
                alt="Digital Library Logo"
                class="h-24 object-contain"
                @error="handleImageError"
              />
            </div>
            <h1 class="text-xl font-bold text-gray-900 mb-1">E-Library</h1>
            <p class="text-sm font-semibold text-gray-900">
              Badan Riset dan Inovasi Daerah<br />Sulawesi Tenggara
            </p>
          </div>

          <!-- Login Form -->
          <form @submit.prevent="handleLogin" class="space-y-4">
            <!-- Email Input -->
            <div>
              <input
                v-model="formData.username"
                type="email"
                placeholder="Email"
                class="w-full px-4 py-3 rounded border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                required
              />
            </div>

            <!-- Password Input -->
            <div>
              <input
                v-model="formData.password"
                type="password"
                placeholder="Password"
                class="w-full px-4 py-3 rounded border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                required
              />
            </div>

            <!-- Login Button -->
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded transition duration-300 ease-in-out transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
            >
              {{ isLoading ? 'Loading...' : 'Log in' }}
            </button>

            <!-- Error Message -->
            <div v-if="errorMessage" class="p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
              {{ errorMessage }}
            </div>

            <!-- OR Divider -->
            <div class="relative my-6">
              <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white text-gray-600 font-semibold text-[11px]">
                  OR
                </span>
              </div>
            </div>

            <!-- SSO Button -->
            <button
              type="button"
              @click="handleSSO"
              :disabled="isLoading"
              class="w-full py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded transition duration-300 ease-in-out transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
            >
              SSO
            </button>
          </form>

          <!-- Bottom Links -->
          <div class="mt-6 space-y-2 text-center text-sm">
            <router-link
              to="/forgot-password"
              class="block text-gray-900 hover:text-gray-700 font-semibold transition"
            >
              Forgot password?
            </router-link>
            <router-link
              to="/register"
              class="block text-gray-900 hover:text-gray-700 font-semibold transition"
            >
              Don't have any accounts?
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import AuthLayout from '@/layout/AuthLayout.vue'

const router = useRouter()
const route = useRoute()

const formData = reactive({
  username: '',
  password: ''
})

const isLoading = ref(false)
const errorMessage = ref('')

const handleLogin = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    // Call Laravel API
    const response = await fetch('http://127.0.0.1:8000/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: formData.username, // API expects email field
        password: formData.password
      })
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Login gagal. Periksa email dan password Anda.')
    }

    // Store token from API response
    if (data.token) {
      localStorage.setItem('auth_token', data.token)
    }

    // Store user data
    if (data.user) {
      localStorage.setItem('user', JSON.stringify({
        id: data.user.id,
        username: data.user.name || data.user.email,
        email: data.user.email,
        role: data.user.role || 'user'
      }))
    }

    // Get redirect path or default to dashboard
    const redirectPath = (route.query.redirect as string) || '/dashboard'
    router.push(redirectPath)
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Login failed. Please try again.'
  } finally {
    isLoading.value = false
  }
}

const handleSSO = () => {
  // TODO: Implement SSO login logic
  console.log('SSO Login clicked')

  // Example: Redirect to SSO provider
  // window.location.href = 'https://your-sso-provider.com/auth?client_id=your_client_id&redirect_uri=' + encodeURIComponent(window.location.origin + '/auth/callback')
}

const handleImageError = (event: Event) => {
  // Fallback if logo image fails to load
  const img = event.target as HTMLImageElement
  img.style.display = 'none'
}
</script>

<style scoped>
/* Additional custom styling if needed */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
  -webkit-box-shadow: 0 0 0 1000px white inset;
  -webkit-text-fill-color: #1e1e1e;
}
</style>
