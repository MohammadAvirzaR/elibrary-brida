<template>
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
        <!-- Register Card -->
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
            <h1 class="text-xl font-bold text-gray-900 mb-1">Daftar Akun Baru</h1>
            <p class="text-sm font-semibold text-gray-900">
              Badan Riset dan Inovasi Daerah<br />Sulawesi Tenggara
            </p>
          </div>

          <!-- Register Form -->
          <form @submit.prevent="handleRegister" class="space-y-4">
            <!-- Nama Lengkap Input -->
            <div>
              <input
                v-model="formData.name"
                type="text"
                placeholder="Nama Lengkap"
                class="w-full px-4 py-3 rounded border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                required
              />
            </div>

            <!-- Email Input -->
            <div>
              <input
                v-model="formData.email"
                type="email"
                placeholder="Email"
                class="w-full px-4 py-3 rounded border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                required
              />
            </div>

            <!-- Unit/Instansi Input -->
            <div>
              <input
                v-model="formData.institution"
                type="text"
                placeholder="Unit/Instansi"
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
                minlength="6"
              />
            </div>

            <!-- Confirm Password Input -->
            <div>
              <input
                v-model="formData.password_confirmation"
                type="password"
                placeholder="Konfirmasi Password"
                class="w-full px-4 py-3 rounded border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                required
                minlength="6"
              />
            </div>

            <!-- Register Button -->
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded transition duration-300 ease-in-out transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
            >
              {{ isLoading ? 'Loading...' : 'Daftar' }}
            </button>

            <!-- Error Message -->
            <div v-if="errorMessage" class="p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
              {{ errorMessage }}
            </div>

            <!-- Success Message -->
            <div v-if="successMessage" class="p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm">
              {{ successMessage }}
            </div>
          </form>

          <!-- Bottom Links -->
          <div class="mt-6 text-center text-sm">
            <router-link
              to="/login"
              class="block text-gray-900 hover:text-gray-700 font-semibold transition"
            >
              Sudah punya akun? Login di sini
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import AuthLayout from '@/layout/AuthLayout.vue'
import api from '@/services/api'

const router = useRouter()

const formData = reactive({
  name: '',
  email: '',
  institution: '',
  password: '',
  password_confirmation: ''
})

const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const handleRegister = async () => {
  isLoading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  // Validasi password match
  if (formData.password !== formData.password_confirmation) {
    errorMessage.value = 'Password dan konfirmasi password tidak sama.'
    isLoading.value = false
    return
  }

  try {
    // Call API using api client
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const data: any = await api.auth.register({
      name: formData.name,
      email: formData.email,
      institution: formData.institution,
      password: formData.password,
      password_confirmation: formData.password_confirmation
    })

    // Success
    successMessage.value = 'Registrasi berhasil! Mengalihkan ke halaman utama...'

    // Store token if provided
    if (data.token) {
      localStorage.setItem('auth_token', data.token)
    }

    // Store user data if provided (with default role: guest)
    if (data.user) {
      localStorage.setItem('user', JSON.stringify({
        id: data.user.id,
        name: data.user.name || data.user.email,
        username: data.user.name || data.user.email,
        email: data.user.email,
        institution: data.user.institution,
        role: data.user.role || 'guest'
      }))
    }

    // Redirect to user dashboard after 1.5 seconds
    setTimeout(() => {
      // Dispatch event to notify other components
      window.dispatchEvent(new Event('auth-changed'))

      // New users with guest role go directly to user dashboard
      router.push('/my-dashboard')
    }, 1500)

  } catch (error: unknown) {
    if (error instanceof Error) {
      errorMessage.value = error.message
    } else {
      errorMessage.value = 'Registrasi gagal. Silakan coba lagi.'
    }
  } finally {
    isLoading.value = false
  }
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
