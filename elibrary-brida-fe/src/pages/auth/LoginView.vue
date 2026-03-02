<template id="login-view">
  <AuthLayout>
    <div class="min-h-screen flex items-center justify-center p-3 sm:p-4 relative">
      <!-- Back Button - Fixed to left side -->
      <button
        @click="router.push('/')"
        class="fixed left-3 sm:left-8 top-3 sm:top-8 w-10 h-10 sm:w-12 sm:h-12 bg-black rounded-full flex items-center justify-center hover:bg-gray-800 transition shadow-lg z-10"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <div class="w-full max-w-md">
        <!-- Login Card -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-5 sm:p-8 border border-gray-200">
          <!-- Logo and Title -->
          <div class="text-center mb-6 sm:mb-8">
            <div class="flex justify-center mb-3 sm:mb-4">
              <img
                src="@/assets/brin-logo-trans.png"
                alt="Digital Library Logo"
                class="h-16 sm:h-20 md:h-24 object-contain"
                @error="handleImageError"
              />
            </div>
            <h1 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">E-Library</h1>
            <p class="text-xs sm:text-sm font-semibold text-gray-900">
              Badan Riset dan Inovasi Daerah<br />Sulawesi Tenggara
            </p>
          </div>

          <!-- Login Form -->
          <form @submit.prevent="handleLogin" class="space-y-3 sm:space-y-4">
            <!-- Email Input -->
            <div>
              <input
                v-model="formData.username"
                type="email"
                placeholder="Email"
                :class="[
                  'w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base rounded border text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 transition',
                  !isEmailValid && formData.username ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-transparent'
                ]"
                required
              />
              <p v-if="emailErrorMessage" class="mt-1 text-xs sm:text-sm text-red-600">
                {{ emailErrorMessage }}
              </p>
            </div>

            <!-- Password Input -->
            <div class="relative">
  <input
    v-model="formData.password"
    :type="showPassword ? 'text' : 'password'"
    placeholder="Password"
    class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base rounded border border-gray-300 text-gray-900 placeholder-gray-400
           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
    required
  />

  <button
    type="button"
    @click="showPassword = !showPassword"
    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
    :disabled="isLoading"
  >
    <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
    </svg>

    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
    </svg>
  </button>
</div>


            <!-- Login Button -->
            <button
              type="submit"
              :disabled="isLoading || !isEmailValid"
              class="w-full py-2 sm:py-3 text-sm sm:text-base bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded transition duration-300 ease-in-out transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
            >
              {{ isLoading ? 'Loading...' : 'Log in' }}
            </button>

            <!-- Error Message -->
            <div v-if="errorMessage" class="p-2.5 sm:p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-xs sm:text-sm">
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

            <!-- Google Login Button -->
            <button
              type="button"
              @click="handleGoogleLogin"
              :disabled="isLoading"
              class="w-full py-2.5 border border-gray-300 rounded flex items-center justify-center gap-3 hover:bg-gray-50 transition font-medium text-sm text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
              Lanjutkan dengan Google
            </button>

            <!-- SSO Button -->
            <button
              type="button"
              @click="handleSSO"
              :disabled="isLoading"
              class="w-full py-2.5 bg-green-500 hover:bg-green-600 text-white font-semibold rounded transition duration-300 ease-in-out transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
            >
              SSO
            </button>
          </form>

          <!-- Bottom Links -->
          <div class="mt-6 space-y-2 text-center text-sm">
            <p class="text-gray-700">
  Lupa Password ?
  <router-link
    to="/forgot-password"
    class="text-blue-600 hover:text-blue-500 font-semibold transition"
  >
    Klik disini
  </router-link>
</p>

            <p class="text-gray-700">
  Tidak punya akun ?
  <router-link
    to="/register"
    class="text-blue-600 hover:text-blue-500 font-semibold transition"
  >
    Buat akun
  </router-link>
</p>

          </div>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import AuthLayout from '@/layout/AuthLayout.vue'
import api from '@/services/api'
import { emailAddress } from '@form-validation/validator-email-address'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const { toast } = useToast()

const formData = reactive({
  username: '',
  password: ''
})
const showPassword = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')

const isEmailValid = computed(() => {
  if (!formData.username) return true
  const result = emailAddress().validate({
    value: formData.username,
    options: {}
  })
  return result.valid
})

const emailErrorMessage = computed(() => {
  if (!formData.username || isEmailValid.value) return ''
  return 'Please enter a valid email address'
})

interface LoginResponse {
  token: string
  user: {
    id: number
    name: string
    email: string
    role: string
  }
}

const handleLogin = async () => {
  if (!isEmailValid.value) {
    errorMessage.value = 'Please enter a valid email address'
    return
  }

  isLoading.value = true
  errorMessage.value = ''

  try {
    const data = await api.auth.login({
      email: formData.username,
      password: formData.password
    }) as LoginResponse

    if (data.token) {
      localStorage.setItem('auth_token', data.token)
    }

    if (data.user) {
      localStorage.setItem('user', JSON.stringify({
        id: data.user.id,
        name: data.user.name || data.user.email,
        username: data.user.name || data.user.email,
        email: data.user.email,
        role: data.user.role || 'guest'
      }))
    }

    const userRole = data.user?.role?.toLowerCase()

    window.dispatchEvent(new Event('auth-changed'))
    localStorage.setItem('last_known_role', userRole)

    toast.success('Login Berhasil', `Selamat datang, ${data.user?.name || 'User'}!`)

    // Redirect based on role
    if (userRole === 'super_admin' || userRole === 'admin' || userRole === 'reviewer') {
      router.push('/dashboard')
    } else {
      router.push('/my-dashboard')
    }
  } catch (error: unknown) {
    if (error instanceof Error) {
      errorMessage.value = error.message
      toast.error('Login Gagal', error.message)
    } else {
      errorMessage.value = 'Login failed. Please try again.'
      toast.error('Login Gagal', 'Silakan coba lagi')
    }
  } finally {
    isLoading.value = false
  }
}

const handleGoogleLogin = () => {
  window.location.href = 'http://127.0.0.1:8000/auth/google'
}

const handleSSO = () => {
  console.log('SSO Login clicked')
}

const handleImageError = (event: Event) => {
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
