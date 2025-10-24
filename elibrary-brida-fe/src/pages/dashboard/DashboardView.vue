<template>
  <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center">
    <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md text-center">
      <h1 class="text-2xl font-bold text-gray-800 mb-4">Welcome to Dashboard</h1>

      <p class="text-gray-600 mb-6">
        You are logged in as <strong>{{ username }}</strong>
      </p>

      <button
        @click="logout"
        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-xl transition duration-300 ease-in-out transform hover:scale-[1.02] active:scale-[0.98]"
      >
        Logout
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const username = ref('')

// Ambil data user dari localStorage biar bisa ditampilkan
onMounted(() => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    username.value = JSON.parse(storedUser).username
  } else {
    // Kalau belum login, redirect ke /login
    router.push('/login')
  }
})

// Fungsi logout
const logout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  router.push('/login')
}
</script>
