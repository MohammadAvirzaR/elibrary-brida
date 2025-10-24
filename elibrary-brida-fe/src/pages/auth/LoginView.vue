<template id="login-view">
  <AuthLayout>
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
      <div class="grid md:grid-cols-2">
        <!-- Left Section - Login Form -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-8 md:p-12 flex flex-col justify-center">
          <!-- Header -->
          <div class="bg-white rounded-lg p-6 mb-8">
            <h1 class="text-base font-bold text-neutral-950">E-Library</h1>
            <p class="text-base font-bold text-neutral-950">
              Badan Riset dan Inovasi Daerah<br />Sulawesi Tenggara
            </p>
          </div>

          <!-- Login Form -->
          <form @submit.prevent="handleLogin" class="space-y-4">
            <!-- Username Input -->
            <div>
              <input
                v-model="formData.username"
                type="text"
                placeholder="Username"
                class="w-full px-4 py-3 rounded bg-white border-none text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-blue-300 transition"
                required
              />
            </div>

            <!-- Password Input -->
            <div>
              <input
                v-model="formData.password"
                type="password"
                placeholder="Password"
                class="w-full px-4 py-3 rounded bg-white border-none text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-blue-300 transition"
                required
              />
            </div>

            <!-- Login Button -->
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full py-3 bg-blue-900 hover:bg-blue-950 text-white font-bold rounded  transition duration-300 ease-in-out transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
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
                <span class="px-4  from-blue-500 to-blue-600 text-neutral-800 font-bold">
                  OR
                </span>
              </div>
            </div>

            <!-- SSO Button -->
            <button
              type="button"
              @click="handleSSO"
              :disabled="isLoading"
              class="w-full py-3 bg-secondary hover:bg-secondary-dark text-white font-bold rounded transition duration-300 ease-in-out transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
            >
              SSO
            </button>
          </form>

          <!-- Bottom Links -->
          <div class="mt-6 space-y-2 text-sm">
            <router-link
              to="/forgot-password"
              class="block text-neutral-900 hover:text-neutral-800 font-semibold transition"
            >
              Forgot password?
            </router-link>
            <router-link
              to="/register"
              class="block text-neutral-900 hover:text-neutral-800 font-semibold transition"
            >
              Don't have any accounts?
            </router-link>
          </div>
        </div>

        <!-- Right Section - Branding -->
        <div class="hidden md:flex flex-col items-center justify-center p-12 bg-white">
          <div class="text-center">
            <!-- Logo -->
            <div class="mb-8 flex justify-center">
              <img
                    src="@/assets/brin-logo-trans.png"
                    alt="Digital Library Logo"
                    class=" h-120 object-contain"
                    @error="handleImageError"
                  />
            </div>

            <!-- Title -->

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

// const handleLogin = async () => {
//   isLoading.value = true
//   errorMessage.value = ''

//   try {
//     // TODO: Replace with your actual API call
//     // Example:
//     // const response = await fetch('/api/auth/login', {
//     //   method: 'POST',
//     //   headers: { 'Content-Type': 'application/json' },
//     //   body: JSON.stringify(formData)
//     // })
//     // const data = await response.json()

//     // Simulate API call
//     await new Promise(resolve => setTimeout(resolve, 1000))

//     // Mock success - Replace with actual logic
//     if (formData.username && formData.password) {
//       // Store token (example)
//       localStorage.setItem('auth_token', 'mock_token_12345')

//       // Optional: Store user data
//       localStorage.setItem('user', JSON.stringify({
//         username: formData.username,
//         name: 'User Name'
//       }))

//       // Get redirect path or default to dashboard
//       const redirectPath = (route.query.redirect as string) || '/dashboard'
//       router.push(redirectPath)
//     } else {
//       throw new Error('Invalid credentials')
//     }
//   } catch (error: any) {
//     errorMessage.value = error.message || 'Login failed. Please try again.'
//   } finally {
//     isLoading.value = false
//   }
// }

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
