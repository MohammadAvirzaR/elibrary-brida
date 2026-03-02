<template>
  <AuthLayout>
    <div class="min-h-screen flex items-center justify-center">
      <div class="text-center space-y-3">
        <svg class="animate-spin w-10 h-10 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
        </svg>
        <p class="text-gray-600 text-sm">{{ statusMessage }}</p>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AuthLayout from '@/layout/AuthLayout.vue'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const { toast } = useToast()
const statusMessage = ref('Sedang memproses login Google...')

onMounted(() => {
  const params = new URLSearchParams(window.location.search)
  const token = params.get('token')
  const userEncoded = params.get('user')
  const error = params.get('error')

  if (error || !token || !userEncoded) {
    statusMessage.value = 'Login Google gagal. Mengalihkan...'
    toast.error('Login Gagal', 'Login dengan Google gagal. Silakan coba lagi.')
    router.push('/login?error=google_failed')
    return
  }

  try {
    const user = JSON.parse(atob(userEncoded))

    localStorage.setItem('auth_token', token)
    localStorage.setItem('user', JSON.stringify({
      id:          user.id,
      name:        user.name,
      username:    user.username ?? user.name,
      email:       user.email,
      institution: user.institution ?? '',
      role:        user.role
    }))

    const lastRole = localStorage.getItem('last_known_role')
    if (lastRole !== user.role) {
      localStorage.setItem('last_known_role', user.role)
    }

    window.dispatchEvent(new Event('auth-changed'))

    toast.success('Login Berhasil', `Selamat datang, ${user.name}!`)

    const role = user.role?.toLowerCase()
    if (role === 'super_admin' || role === 'admin' || role === 'reviewer') {
      router.push('/dashboard')
    } else {
      router.push('/my-dashboard')
    }
  } catch {
    statusMessage.value = 'Terjadi kesalahan. Mengalihkan...'
    toast.error('Login Gagal', 'Terjadi kesalahan saat memproses login.')
    router.push('/login')
  }
})
</script>
