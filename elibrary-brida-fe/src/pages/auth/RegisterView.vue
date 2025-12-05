<template>
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
        <!-- Register Card -->
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
            <h1 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">Daftar Akun Baru</h1>
            <p class="text-xs sm:text-sm font-semibold text-gray-900">
              Badan Riset dan Inovasi Daerah<br />Sulawesi Tenggara
            </p>
          </div>

          <!-- Register Form -->
          <form @submit.prevent="handleRegister" class="space-y-3 sm:space-y-4">
            <!-- Nama Lengkap Input -->
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

            <!-- Email Input -->
            <div>
              <input
                v-model="formData.email"
                type="email"
                placeholder="Email"
                :class="[
                  'w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base rounded border text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 transition',
                  !isEmailValid && formData.email ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-transparent'
                ]"
                required
                :disabled="isLoading"
              />
              <p v-if="emailErrorMessage" class="mt-1 text-xs sm:text-sm text-red-600">
                {{ emailErrorMessage }}
              </p>
            </div>

            <!-- Unit/Instansi Input -->
            <div>
              <input
                v-model="formData.institution"
                type="text"
                placeholder="Unit/Instansi (Opsional)"
                class="w-full px-4 py-3 rounded border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                :disabled="isLoading"
              />
            </div>

            <!-- Password Input -->
            <div class="relative">
              <input
                v-model="formData.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Password (minimal 6 karakter)"
                class="w-full px-4 py-3 pr-12 rounded border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                required
                minlength="6"
                :disabled="isLoading"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                :disabled="isLoading"
              >
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
            </div>

            <!-- Confirm Password Input -->
            <div class="relative">
              <input
                v-model="formData.password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                placeholder="Konfirmasi Password"
                class="w-full px-4 py-3 pr-12 rounded border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                required
                minlength="6"
                :disabled="isLoading"
              />
              <button
                type="button"
                @click="showConfirmPassword = !showConfirmPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                :disabled="isLoading"
              >
                <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
            </div>

            <!-- Register Button -->
            <button
              type="submit"
              :disabled="isLoading || !isEmailValid"
              class="w-full py-2 sm:py-3 text-sm sm:text-base bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded transition duration-300 ease-in-out transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none inline-flex items-center justify-center gap-2"
            >
              <i-lucide-loader-2 v-if="isLoading" class="w-4 h-4 animate-spin" />
              <span>{{ isLoading ? 'Mengirim OTP...' : 'Daftar' }}</span>
            </button>

            <!-- Error Message -->
            <div v-if="errorMessage" class="p-2.5 sm:p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-xs sm:text-sm">
              {{ errorMessage }}
            </div>

            <!-- Info Message -->

          </form>

          <!-- OTP Modal -->
          <OtpVerificationModal
            v-if="showOtpModal"
            :email="formData.email"
            :expires-in="otpExpiresIn"
            :is-resending="isResending"
            @close="handleCloseOtp"
            @verify="handleVerifyOtp"
            @resend="handleResendOtp"
            ref="otpModal"
          />

          <!-- Bottom Links -->
          <div class="mt-6 text-center text-sm">
            <p class="text-gray-700">
  Sudah punya akun ?
  <router-link
    to="/login"
    class="text-blue-600 hover:text-blue-500 font-semibold transition"
  >
    Login di sini
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
import OtpVerificationModal from '@/components/OtpVerificationModal.vue'
import { emailAddress } from '@form-validation/validator-email-address'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const { toast } = useToast()

const formData = reactive({
  name: '',
  email: '',
  institution: '',
  password: '',
  password_confirmation: ''
})

const isLoading = ref(false)
const errorMessage = ref('')
const showOtpModal = ref(false)
const otpExpiresIn = ref(60) // 1 minute
const isResending = ref(false)
const otpModal = ref<InstanceType<typeof OtpVerificationModal>>()
const showPassword = ref(false)
const showConfirmPassword = ref(false)

interface OtpResponse {
  status: string
  message: string
  email: string
  expires_in: number
}

interface VerifyOtpResponse {
  status: string
  message: string
  user: {
    id: number
    name: string
    email: string
    institution?: string
    role: string
  }
  token: string
}

const isEmailValid = computed(() => {
  if (!formData.email) return true
  const result = emailAddress().validate({
    value: formData.email,
    options: {}
  })
  return result.valid
})

const emailErrorMessage = computed(() => {
  if (!formData.email || isEmailValid.value) return ''
  return 'Please enter a valid email address'
})

const handleRegister = async () => {
  if (!isEmailValid.value) {
    errorMessage.value = 'Please enter a valid email address'
    return
  }

  if (formData.password !== formData.password_confirmation) {
    errorMessage.value = 'Password dan konfirmasi password tidak sama.'
    return
  }

  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch('http://127.0.0.1:8000/api/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        name: formData.name,
        email: formData.email,
        institution: formData.institution,
        password: formData.password,
        password_confirmation: formData.password_confirmation
      })
    })

    const data: OtpResponse = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Gagal mengirim OTP')
    }

    toast.success('OTP Terkirim', 'Kode verifikasi telah dikirim ke email Anda')
    otpExpiresIn.value = data.expires_in || 60
    showOtpModal.value = true

  } catch (error: unknown) {
    if (error instanceof Error) {
      errorMessage.value = error.message
      toast.error('Gagal Mengirim OTP', error.message)
    } else {
      errorMessage.value = 'Gagal mengirim OTP. Silakan coba lagi.'
      toast.error('Gagal Mengirim OTP', 'Silakan coba lagi')
    }
  } finally {
    isLoading.value = false
  }
}

const handleVerifyOtp = async (otp: string) => {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/verify-otp', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: formData.email,
        otp: otp
      })
    })

    const data: VerifyOtpResponse = await response.json()

    if (!response.ok) {
      otpModal.value?.setError(data.message || 'Kode OTP tidak valid')
      return
    }

    toast.success('Verifikasi Berhasil', data.message || 'Registrasi berhasil!')

    // Save token and user data
    if (data.token) {
      localStorage.setItem('auth_token', data.token)
    }

    if (data.user) {
      localStorage.setItem('user', JSON.stringify({
        id: data.user.id,
        name: data.user.name || data.user.email,
        username: data.user.name || data.user.email,
        email: data.user.email,
        institution: data.user.institution,
        role: data.user.role || 'reviewer'
      }))
    }

    showOtpModal.value = false

    setTimeout(() => {
      window.dispatchEvent(new Event('auth-changed'))
      router.push('/my-dashboard')
    }, 1000)

  } catch (error: unknown) {
    if (error instanceof Error) {
      otpModal.value?.setError(error.message)
    } else {
      otpModal.value?.setError('Verifikasi gagal. Silakan coba lagi.')
    }
  }
}

const handleResendOtp = async () => {
  isResending.value = true

  try {
    const response = await fetch('http://127.0.0.1:8000/api/resend-otp', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: formData.email
      })
    })

    const data: OtpResponse = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Gagal mengirim ulang OTP')
    }

    toast.success('OTP Terkirim', 'Kode verifikasi baru telah dikirim ke email Anda')
    otpExpiresIn.value = data.expires_in || 60
    otpModal.value?.reset()

  } catch (error: unknown) {
    if (error instanceof Error) {
      toast.error('Gagal Mengirim OTP', error.message)
    } else {
      toast.error('Gagal Mengirim OTP', 'Silakan coba lagi')
    }
  } finally {
    isResending.value = false
  }
}

const handleCloseOtp = () => {
  showOtpModal.value = false
  otpModal.value?.reset()
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
