<template>
  <AuthLayout>
    <div class="min-h-screen flex items-center justify-center p-3 sm:p-4 relative">
      <!-- Back Button -->
      <button
        @click="router.push('/login')"
        class="fixed left-3 sm:left-8 top-3 sm:top-8 w-10 h-10 sm:w-12 sm:h-12 bg-black rounded-full flex items-center justify-center hover:bg-gray-800 transition shadow-lg z-10"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <div class="w-full max-w-md">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-5 sm:p-8 border border-gray-200">
          <!-- Logo dan Title -->
          <div class="text-center mb-6 sm:mb-8">
            <div class="flex justify-center mb-3 sm:mb-4">
              <img
                src="@/assets/brin-logo-trans.png"
                alt="Digital Library Logo"
                class="h-16 sm:h-20 md:h-24 object-contain"
                @error="handleImageError"
              />
            </div>
            <h1 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">Lengkapi Data Diri</h1>
            <p class="text-xs sm:text-sm text-gray-500">
              Satu langkah lagi untuk menyelesaikan pendaftaran
            </p>
          </div>

          <!-- Info email dari Google -->
          <div class="mb-4 flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            <div class="min-w-0">
              <p class="text-xs text-gray-500">Login dengan Google</p>
              <p class="text-sm font-medium text-gray-800 truncate">{{ googleEmail }}</p>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="handleSubmit" class="space-y-3 sm:space-y-4">
            <!-- Nama Lengkap -->
            <div>
              <input
                v-model="formData.name"
                type="text"
                placeholder="Nama Lengkap"
                class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base rounded border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                required
                :disabled="isLoading"
              />
            </div>

            <!-- Unit/Instansi -->
            <div>
              <input
                v-model="formData.institution"
                type="text"
                placeholder="Unit/Instansi (Opsional)"
                class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base rounded border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                :disabled="isLoading"
              />
            </div>

            <!-- Password -->
            <div class="relative">
              <input
                v-model="formData.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Password (min. 6 karakter)"
                class="w-full px-3 sm:px-4 py-2 sm:py-3 pr-12 text-sm sm:text-base rounded border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                required
                minlength="6"
                :disabled="isLoading"
              />
              <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
            </div>

            <!-- Konfirmasi Password -->
            <div class="relative">
              <input
                v-model="formData.password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                placeholder="Konfirmasi Password"
                :class="[
                  'w-full px-3 sm:px-4 py-2 sm:py-3 pr-12 text-sm sm:text-base rounded border text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 transition',
                  passwordMismatch ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-transparent'
                ]"
                required
                :disabled="isLoading"
              />
              <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
              <p v-if="passwordMismatch" class="mt-1 text-xs text-red-600">Password tidak cocok</p>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="isLoading || !formData.name.trim() || passwordMismatch || formData.password.length < 6"
              class="w-full py-2 sm:py-3 text-sm sm:text-base bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded transition duration-300 ease-in-out transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none inline-flex items-center justify-center gap-2"
            >
              <i-lucide-loader-2 v-if="isLoading" class="w-4 h-4 animate-spin" />
              <span>{{ isLoading ? 'Memproses...' : 'Selesaikan Pendaftaran' }}</span>
            </button>

            <!-- Error Message -->
            <div v-if="errorMessage" class="p-2.5 sm:p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-xs sm:text-sm">
              {{ errorMessage }}
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import AuthLayout from '@/layout/AuthLayout.vue'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const route  = useRoute()
const { toast } = useToast()

const isLoading            = ref(false)
const errorMessage         = ref('')
const googleEmail          = ref('')
const ref_key              = ref('')
const showPassword         = ref(false)
const showConfirmPassword  = ref(false)

const formData = reactive({
  name:                  '',
  institution:           '',
  password:              '',
  password_confirmation: '',
})

const passwordMismatch = computed(() =>
  formData.password_confirmation.length > 0 &&
  formData.password !== formData.password_confirmation
)

onMounted(() => {
  const refParam   = route.query.ref as string
  const nameParam  = route.query.name as string
  const emailParam = route.query.email as string

  // Jika tidak ada ref, kembalikan ke login
  if (!refParam) {
    router.push('/login')
    return
  }

  ref_key.value      = refParam
  googleEmail.value  = emailParam ? decodeURIComponent(emailParam) : ''
  formData.name      = nameParam  ? decodeURIComponent(nameParam)  : ''
})

const handleSubmit = async () => {
  if (!formData.name.trim()) return
  if (formData.password.length < 6) {
    errorMessage.value = 'Password minimal 6 karakter'
    return
  }
  if (formData.password !== formData.password_confirmation) {
    errorMessage.value = 'Password tidak cocok'
    return
  }

  isLoading.value    = true
  errorMessage.value = ''

  const apiUrl = (import.meta as unknown as { env: { VITE_API_BASE_URL?: string } }).env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api'

  try {
    const response = await fetch(`${apiUrl}/register/google-complete`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept':       'application/json',
      },
      body: JSON.stringify({
        ref:                   ref_key.value,
        name:                  formData.name.trim(),
        institution:           formData.institution.trim() || null,
        password:              formData.password,
        password_confirmation: formData.password_confirmation,
      }),
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Pendaftaran gagal')
    }

    // Simpan token & user seperti login biasa
    localStorage.setItem('auth_token', data.token)
    localStorage.setItem('user', JSON.stringify({
      id:          data.user.id,
      name:        data.user.name,
      username:    data.user.username ?? data.user.name,
      email:       data.user.email,
      institution: data.user.institution ?? '',
      role:        data.user.role,
    }))
    localStorage.setItem('last_known_role', data.user.role)

    window.dispatchEvent(new Event('auth-changed'))
    toast.success('Pendaftaran Berhasil', `Selamat datang, ${data.user.name}!`)

    const role = data.user.role?.toLowerCase()
    if (role === 'super_admin' || role === 'admin' || role === 'reviewer') {
      router.push('/dashboard')
    } else {
      router.push('/my-dashboard')
    }
  } catch (error: unknown) {
    if (error instanceof Error) {
      errorMessage.value = error.message
    } else {
      errorMessage.value = 'Terjadi kesalahan. Silakan coba lagi.'
    }
  } finally {
    isLoading.value = false
  }
}

const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  img.style.display = 'none'
}
</script>

<style scoped>
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
  -webkit-box-shadow: 0 0 0 1000px white inset;
  -webkit-text-fill-color: #1e1e1e;
}
</style>
