<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside
      :class="[
        'fixed left-0 top-0 h-screen bg-gradient-to-b from-blue-700 to-blue-900 text-white shadow-xl transition-all duration-300 ease-in-out z-20',
        isSidebarOpen ? 'w-60' : 'w-20'
      ]"
    >
      <!-- Toggle Button -->
      <div class="flex items-center justify-center p-6 border-b border-blue-600">
        <button
          @click="toggleSidebar"
          class="p-2 hover:bg-blue-600 rounded-lg transition"
          :title="isSidebarOpen ? 'Tutup Sidebar' : 'Buka Sidebar'"
        >
          <i-lucide-panel-left-close v-if="isSidebarOpen" class="w-6 h-6 text-white" />
          <i-lucide-panel-left-open v-else class="w-6 h-6 text-white" />
        </button>
      </div>

      <!-- Menu -->
      <nav class="mt-6">
        <router-link to="/my-dashboard" class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group">
          <i-lucide-layout-dashboard class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Dashboard
          </span>
        </router-link>

        <router-link to="/become-contributor" class="flex items-center gap-4 px-6 py-3 bg-blue-800 border-l-4 border-white group">
          <i-lucide-file-text class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Jadi Kontributor
          </span>
        </router-link>

        <button @click="logout" class="w-full flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition mt-4 group">
          <i-lucide-log-out class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Log Out
          </span>
        </button>
      </nav>
    </aside>

    <!-- Header -->
    <header
      :class="[
        'bg-white shadow-sm border-b border-gray-200 transition-all duration-300',
        isSidebarOpen ? 'ml-60' : 'ml-20'
      ]"
    >
      <div class="flex items-center justify-between px-8 py-4">
        <h1 class="text-2xl font-bold text-gray-900">Daftar Menjadi Kontributor</h1>
        <div class="flex items-center gap-4">
          <div class="text-right">
            <p class="font-bold text-gray-800">{{ username || 'User' }}</p>
            <p class="text-sm text-gray-700 capitalize">guest</p>
          </div>
          <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
            {{ username ? username.charAt(0).toUpperCase() : 'U' }}
          </div>
        </div>
      </div>
    </header>

    <!-- Content -->
    <main
      :class="[
        'transition-all duration-300 p-8',
        isSidebarOpen ? 'ml-60' : 'ml-20'
      ]"
    >
      <div class="max-w-2xl mx-auto">
        <!-- Status Card -->
        <div v-if="hasPendingRequest" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
          <div class="flex items-center gap-3">
            <i-lucide-clock class="w-6 h-6 text-yellow-600" />
            <div>
              <h3 class="font-bold text-yellow-900">Permintaan Menunggu Persetujuan</h3>
              <p class="text-sm text-yellow-700 mt-1">
                Permintaan Anda sudah kami terima. Tim admin akan meninjau dalam 1-3 hari kerja.
              </p>
            </div>
          </div>
        </div>

        <!-- Success Card -->
        <div v-if="showSuccess" class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
          <div class="flex items-center gap-3">
            <i-lucide-check-circle class="w-6 h-6 text-green-600" />
            <div>
              <h3 class="font-bold text-green-900">Permintaan Berhasil Dikirim!</h3>
              <p class="text-sm text-green-700 mt-1">
                Terima kasih telah mendaftar menjadi kontributor. Kami akan segera meninjau permintaan Anda.
              </p>
            </div>
          </div>
        </div>

        <!-- Error Card -->
        <div v-if="errorMessage" class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
          <div class="flex items-center gap-3">
            <i-lucide-alert-circle class="w-6 h-6 text-red-600" />
            <div>
              <h3 class="font-bold text-red-900">Terjadi Kesalahan</h3>
              <p class="text-sm text-red-700 mt-1">{{ errorMessage }}</p>
            </div>
          </div>
        </div>

        <!-- Information Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Mengapa Menjadi Kontributor?</h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="flex gap-4">
              <i-lucide-upload-cloud class="w-8 h-8 text-blue-600 flex-shrink-0" />
              <div>
                <h3 class="font-bold text-gray-900">Unggah Dokumen</h3>
                <p class="text-sm text-gray-600 mt-1">Berbagi pengetahuan dan dokumentasi dengan komunitas</p>
              </div>
            </div>

            <div class="flex gap-4">
              <i-lucide-users class="w-8 h-8 text-purple-600 flex-shrink-0" />
              <div>
                <h3 class="font-bold text-gray-900">Kolaborasi</h3>
                <p class="text-sm text-gray-600 mt-1">Bekerja sama dengan profesional lainnya</p>
              </div>
            </div>

            <div class="flex gap-4">
              <i-lucide-award class="w-8 h-8 text-green-600 flex-shrink-0" />
              <div>
                <h3 class="font-bold text-gray-900">Pengakuan</h3>
                <p class="text-sm text-gray-600 mt-1">Dapatkan kredit dan pengakuan atas kontribusi Anda</p>
              </div>
            </div>

            <div class="flex gap-4">
              <i-lucide-trending-up class="w-8 h-8 text-orange-600 flex-shrink-0" />
              <div>
                <h3 class="font-bold text-gray-900">Perkembangan</h3>
                <p class="text-sm text-gray-600 mt-1">Tingkatkan visibilitas dan jangkauan karya Anda</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
          <h2 class="text-xl font-bold text-gray-900 mb-6">Ajukan Permintaan Kontributor</h2>

          <form @submit.prevent="submitRequest" class="space-y-6">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Nama Anda
              </label>
              <input
                v-model="form.name"
                type="text"
                disabled
                class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-gray-50 text-gray-600"
              />
              <p class="text-xs text-gray-500 mt-1">Nama dari profil Anda</p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Email Anda
              </label>
              <input
                v-model="form.email"
                type="email"
                disabled
                class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-gray-50 text-gray-600"
              />
              <p class="text-xs text-gray-500 mt-1">Email dari profil Anda</p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Alasan Ingin Menjadi Kontributor <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="form.message"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                rows="6"
                placeholder="Ceritakan alasan Anda ingin menjadi kontributor, spesialisasi Anda, tipe dokumen yang ingin dibagikan, dll..."
                :disabled="isSubmitting"
                required
                minlength="10"
                maxlength="1000"
              />
              <div class="flex justify-between items-center mt-2">
                <p class="text-xs text-gray-500">Minimal 10 karakter, maksimal 1000 karakter</p>
                <p class="text-xs text-gray-400">{{ form.message.length }} / 1000</p>
              </div>
            </div>

            <div class="flex gap-4">
              <button
                v-if="!hasPendingRequest"
                type="submit"
                :disabled="isSubmitting || !form.message.trim() || form.message.length < 10"
                class="flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
              >
                <span v-if="isSubmitting" class="flex items-center justify-center gap-2">
                  <i-lucide-loader-2 class="w-5 h-5 animate-spin" />
                  Mengirim...
                </span>
                <span v-else>Ajukan Permintaan</span>
              </button>
              <router-link
                to="/my-dashboard"
                class="flex-1 px-6 py-3 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 text-center transition"
              >
                Kembali
              </router-link>
            </div>
          </form>

          <!-- Requirements -->
          <div class="mt-8 p-6 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="font-bold text-blue-900 mb-4">Persyaratan Menjadi Kontributor:</h3>
            <ul class="space-y-2 text-sm text-blue-800">
              <li class="flex gap-2">
                <i-lucide-check-circle2 class="w-5 h-5 flex-shrink-0" />
                Sudah terdaftar dan aktif sebagai anggota
              </li>
              <li class="flex gap-2">
                <i-lucide-check-circle2 class="w-5 h-5 flex-shrink-0" />
                Memiliki dokumen berkualitas yang ingin dibagikan
              </li>
              <li class="flex gap-2">
                <i-lucide-check-circle2 class="w-5 h-5 flex-shrink-0" />
                Bersedia mengikuti panduan kontribusi yang berlaku
              </li>
              <li class="flex gap-2">
                <i-lucide-check-circle2 class="w-5 h-5 flex-shrink-0" />
                Menyetujui bahwa dokumen yang dibagikan sesuai dengan hukum yang berlaku
              </li>
            </ul>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()

// States
const isSidebarOpen = ref(true)
const username = ref('')
const isSubmitting = ref(false)
const hasPendingRequest = ref(false)
const showSuccess = ref(false)
const errorMessage = ref('')

const form = ref({
  name: '',
  email: '',
  message: ''
})

// Methods
const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const logout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  localStorage.removeItem('last_known_role')
  router.push('/login')
}

const checkPendingRequest = async () => {
  try {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const response: any = await api.contributorRequests.checkPending()
    if (response.success && response.has_pending) {
      hasPendingRequest.value = true
    }
  } catch (error) {
    console.error('Failed to check pending request:', error)
  }
}

const submitRequest = async () => {
  if (!form.value.message.trim() || form.value.message.length < 10) {
    errorMessage.value = 'Alasan harus minimal 10 karakter'
    return
  }

  isSubmitting.value = true
  errorMessage.value = ''

  try {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const response: any = await api.contributorRequests.submit(form.value.message)

    if (response.success) {
      showSuccess.value = true
      form.value.message = ''
      hasPendingRequest.value = true

      // Auto-redirect after 3 seconds
      setTimeout(() => {
        router.push('/my-dashboard')
      }, 3000)
    }
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Gagal mengirim permintaan'
    console.error('Failed to submit request:', error)
  } finally {
    isSubmitting.value = false
  }
}

// Lifecycle
onMounted(() => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    try {
      const user = JSON.parse(userStr)
      username.value = user.name || 'User'
      form.value.name = user.name || 'User'
      form.value.email = user.email || ''
    } catch {
      username.value = 'User'
    }
  }

  checkPendingRequest()
})
</script>
