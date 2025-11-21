<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-md border-b border-neutral-200 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between h-16">
          <!-- Logo & Brand -->
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
              <i-lucide-book-open class="w-6 h-6 text-white" />
            </div>
            <div>
              <h1 class="text-lg font-bold text-neutral-900">E-Library BRIDA</h1>
              <p class="text-xs text-neutral-500">Dashboard Kontributor</p>
            </div>
          </div>

          <!-- Navigation Items -->
          <div class="flex items-center space-x-4">
            <!-- Tombol Kembali ke Landing Page -->
            <button
              @click="goToLandingPage"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors"
            >
              <i-lucide-home class="w-4 h-4 mr-2" />
              Beranda
            </button>

            <!-- Upload Button -->
            <button
              @click="openUploadModal"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all shadow-sm"
            >
              <i-lucide-upload class="w-4 h-4 mr-2" />
              Unggah Dokumen
            </button>

            <!-- User Profile Dropdown -->
            <div class="relative">
              <button
                @click="toggleProfileMenu"
                class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-neutral-100 transition-colors"
              >
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                  <span class="text-sm font-semibold text-white">{{ userInitials }}</span>
                </div>
                <i-lucide-chevron-down class="w-4 h-4 text-neutral-600" />
              </button>

              <!-- Dropdown Menu -->
              <div
                v-if="showProfileMenu"
                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-neutral-200 py-2"
              >
                <div class="px-4 py-3 border-b border-neutral-200">
                  <p class="text-sm font-semibold text-neutral-900">{{ userName }}</p>
                  <p class="text-xs text-neutral-500">{{ userEmail }}</p>
                </div>
                <button
                  @click="logout"
                  class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center"
                >
                  <i-lucide-log-out class="w-4 h-4 mr-2" />
                  Keluar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Welcome Header -->
    <div class="bg-white shadow-sm border-b border-neutral-200">
      <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-neutral-900">
              Selamat Datang, {{ userName }}
            </h1>
            <p class="text-sm text-neutral-600 mt-1">Kelola dokumen yang Anda upload</p>
          </div>
          <div class="text-right">
            <p class="text-sm text-neutral-500">Status Akun</p>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-1">
              <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
              Kontributor
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8">
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-neutral-600">Total Dokumen</p>
              <p class="text-3xl font-bold text-neutral-900 mt-2">{{ stats.totalDocuments }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <i-lucide-file-text class="w-6 h-6 text-blue-600" />
            </div>
          </div>
          <p class="text-xs text-neutral-500 mt-4">
            <span class="text-green-600 font-semibold">+{{ stats.thisMonth }}</span> bulan ini
          </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-neutral-600">Menunggu Review</p>
              <p class="text-3xl font-bold text-amber-600 mt-2">{{ stats.pending }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
              <i-lucide-clock class="w-6 h-6 text-amber-600" />
            </div>
          </div>
          <p class="text-xs text-neutral-500 mt-4">Dalam proses verifikasi</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-neutral-600">Disetujui</p>
              <p class="text-3xl font-bold text-green-600 mt-2">{{ stats.approved }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <i-lucide-check-circle class="w-6 h-6 text-green-600" />
            </div>
          </div>
          <p class="text-xs text-neutral-500 mt-4">Sudah dipublikasikan</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-neutral-600">Ditolak</p>
              <p class="text-3xl font-bold text-red-600 mt-2">{{ stats.rejected }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
              <i-lucide-x-circle class="w-6 h-6 text-red-600" />
            </div>
          </div>
          <p class="text-xs text-neutral-500 mt-4">Perlu perbaikan</p>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upload Section -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Quick Upload Card -->
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-8 text-white">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <h2 class="text-2xl font-bold mb-2">Upload Dokumen Baru</h2>
                <p class="text-blue-100 mb-6">
                  Bagikan pengetahuan Anda dengan mengunggah dokumen
                </p>
                <button
                  @click="showUploadModal = true"
                  class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-lg font-semibold inline-flex items-center gap-2 transition-colors shadow-md"
                >
                  <i-lucide-upload class="w-5 h-5" />
                  Mulai Upload
                </button>
              </div>
              <div class="hidden md:block">
                <i-lucide-cloud-upload class="w-24 h-24 text-blue-400 opacity-50" />
              </div>
            </div>
          </div>

          <!-- My Documents Table -->
          <div class="bg-white rounded-xl shadow-sm border border-neutral-200">
            <div class="p-6 border-b border-neutral-200">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-neutral-900">Dokumen Saya</h3>
                <div class="flex items-center gap-3">
                  <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari dokumen..."
                    class="px-4 py-2 border border-neutral-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                  <select
                    v-model="filterStatus"
                    class="px-4 py-2 border border-neutral-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu Review</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-neutral-50 border-b border-neutral-200">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                      Judul Dokumen
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                      Kategori
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                      Tanggal Upload
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                      Aksi
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200">
                  <tr
                    v-for="doc in filteredDocuments"
                    :key="doc.id"
                    class="hover:bg-neutral-50 transition-colors"
                  >
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                          <i-lucide-file-text class="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                          <p class="text-sm font-medium text-neutral-900">{{ doc.title }}</p>
                          <p class="text-xs text-neutral-500">{{ doc.author }}</p>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-800">
                        {{ doc.category }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <span
                        :class="getStatusClass(doc.status)"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      >
                        <span :class="getStatusDotClass(doc.status)" class="w-1.5 h-1.5 rounded-full mr-1.5"></span>
                        {{ getStatusText(doc.status) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-neutral-600">
                      {{ formatDate(doc.uploadDate) }}
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-2">
                        <button
                          @click="viewDocument(doc)"
                          class="text-blue-600 hover:text-blue-800 p-1"
                          title="Lihat"
                        >
                          <i-lucide-eye class="w-4 h-4" />
                        </button>
                        <button
                          @click="editDocument(doc)"
                          class="text-amber-600 hover:text-amber-800 p-1"
                          title="Edit"
                        >
                          <i-lucide-edit class="w-4 h-4" />
                        </button>
                        <button
                          @click="deleteDocument(doc)"
                          class="text-red-600 hover:text-red-800 p-1"
                          title="Hapus"
                        >
                          <i-lucide-trash-2 class="w-4 h-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="filteredDocuments.length === 0">
                    <td colspan="5" class="px-6 py-12 text-center">
                      <div class="flex flex-col items-center justify-center text-neutral-400">
                        <i-lucide-inbox class="w-16 h-16 mb-4" />
                        <p class="text-lg font-medium">Belum ada dokumen</p>
                        <p class="text-sm mt-1">Mulai dengan mengunggah dokumen pertama Anda</p>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Quick Tips -->
          <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">
            <h3 class="text-lg font-bold text-neutral-900 mb-4 flex items-center gap-2">
              <i-lucide-lightbulb class="w-5 h-5 text-amber-500" />
              Tips Upload
            </h3>
            <ul class="space-y-3 text-sm text-neutral-700">
              <li class="flex items-start gap-2">
                <i-lucide-check class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                <span>Pastikan file dalam format PDF atau DOCX</span>
              </li>
              <li class="flex items-start gap-2">
                <i-lucide-check class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                <span>Ukuran maksimal file adalah 10MB</span>
              </li>
              <li class="flex items-start gap-2">
                <i-lucide-check class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                <span>Gunakan judul yang deskriptif dan jelas</span>
              </li>
              <li class="flex items-start gap-2">
                <i-lucide-check class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                <span>Isi metadata dengan lengkap untuk memudahkan pencarian</span>
              </li>
            </ul>
          </div>

          <!-- Recent Activity -->
          <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">
            <h3 class="text-lg font-bold text-neutral-900 mb-4 flex items-center gap-2">
              <i-lucide-activity class="w-5 h-5 text-blue-500" />
              Aktivitas Terbaru
            </h3>
            <div class="space-y-4">
              <div
                v-for="activity in recentActivities"
                :key="activity.id"
                class="flex items-start gap-3 pb-4 border-b border-neutral-100 last:border-0 last:pb-0"
              >
                <div :class="getActivityIconClass(activity.type)" class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0">
                  <component :is="getActivityIcon(activity.type)" class="w-4 h-4" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm text-neutral-900 font-medium">{{ activity.title }}</p>
                  <p class="text-xs text-neutral-500 mt-1">{{ activity.time }}</p>
                </div>
              </div>
              <div v-if="recentActivities.length === 0" class="text-center py-4">
                <p class="text-sm text-neutral-400">Belum ada aktivitas</p>
              </div>
            </div>
          </div>

          <!-- Help Card -->
          <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <i-lucide-help-circle class="w-10 h-10 mb-3 text-purple-200" />
            <h3 class="text-lg font-bold mb-2">Butuh Bantuan?</h3>
            <p class="text-sm text-purple-100 mb-4">
              Pelajari cara menggunakan sistem dengan panduan lengkap kami
            </p>
            <router-link
              to="/faq"
              class="inline-flex items-center gap-2 bg-white text-purple-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-purple-50 transition-colors"
            >
              Lihat Panduan
              <i-lucide-arrow-right class="w-4 h-4" />
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Upload Modal -->
    <UploadDocumentModal
      v-if="showUploadModal"
      @close="showUploadModal = false"
      @uploaded="handleDocumentUploaded"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import UploadDocumentModal from '@/components/UploadDocumentModal.vue'

const router = useRouter()

// User info
const userName = ref('')
const userEmail = ref('')

// UI State for navbar
const showProfileMenu = ref(false)
const showUploadModal = ref(false)

// Computed user initials
const userInitials = computed(() => {
  if (!userName.value) return 'U'
  return userName.value
    .split(' ')
    .map(n => n[0])
    .join('')
    .substring(0, 2)
    .toUpperCase()
})

interface UploadedDocument {
  id: number
  title: string
  author: string
  category: string
  status: 'pending' | 'approved' | 'rejected'
  uploadDate: string
}

interface ActivityLog {
  id: number
  type: string
  title: string
  time: string
}

interface ApiDocumentResponse {
  id: number
  title: string
  author: string
  category_name?: string
  status?: string
  created_at: string
  [key: string]: unknown
}

const stats = ref({
  totalDocuments: 0,
  thisMonth: 0,
  pending: 0,
  approved: 0,
  rejected: 0
})

const documents = ref<UploadedDocument[]>([])
const searchQuery = ref('')
const filterStatus = ref('')
const recentActivities = ref<ActivityLog[]>([])

// Filtered documents
const filteredDocuments = computed(() => {
  let filtered = documents.value

  if (searchQuery.value) {
    filtered = filtered.filter(doc =>
      doc.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      doc.author.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  if (filterStatus.value) {
    filtered = filtered.filter(doc => doc.status === filterStatus.value)
  }

  return filtered
})

// Load user data
onMounted(async () => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    const user = JSON.parse(userStr)
    userName.value = user.name || user.username || 'User'
    userEmail.value = user.email || ''
  }

  await loadDocuments()
  loadStats()
  loadRecentActivities()
})

const loadDocuments = async () => {
  try {
    const response = await api.documents.getAll() as { success: boolean; data: ApiDocumentResponse[] }
    if (response.success && response.data) {
      documents.value = response.data.map((doc) => ({
        id: doc.id,
        title: doc.title,
        author: doc.author || userName.value,
        category: doc.category_name || 'Umum',
        status: (doc.status || 'pending') as 'pending' | 'approved' | 'rejected',
        uploadDate: doc.created_at
      }))

      // Update stats setelah dokumen dimuat
      loadStats()
      loadRecentActivities()
    }
  } catch (error) {
    console.error('Gagal memuat dokumen:', error)
    documents.value = []
  }
}

const loadStats = () => {
  const thisMonthStart = new Date(new Date().getFullYear(), new Date().getMonth(), 1)

  stats.value = {
    totalDocuments: documents.value.length,
    thisMonth: documents.value.filter(d => new Date(d.uploadDate) >= thisMonthStart).length,
    pending: documents.value.filter(d => d.status === 'pending').length,
    approved: documents.value.filter(d => d.status === 'approved').length,
    rejected: documents.value.filter(d => d.status === 'rejected').length
  }
}

const loadRecentActivities = async () => {
  const latestDocs = documents.value
    .sort((a, b) => new Date(b.uploadDate).getTime() - new Date(a.uploadDate).getTime())
    .slice(0, 5)

  recentActivities.value = latestDocs.map((doc, index) => ({
    id: index + 1,
    type: doc.status === 'approved' ? 'approved' : doc.status === 'rejected' ? 'rejected' : 'upload',
    title: doc.status === 'approved' ? `Dokumen "${doc.title}" disetujui` :
           doc.status === 'rejected' ? `Dokumen "${doc.title}" ditolak` :
           `Mengunggah: ${doc.title}`,
    time: formatTimeAgo(doc.uploadDate)
  }))
}

const formatTimeAgo = (date: string): string => {
  const now = new Date()
  const past = new Date(date)
  const diffMs = now.getTime() - past.getTime()
  const diffMins = Math.floor(diffMs / 60000)
  const diffHours = Math.floor(diffMins / 60)
  const diffDays = Math.floor(diffHours / 24)

  if (diffMins < 60) return `${diffMins} menit yang lalu`
  if (diffHours < 24) return `${diffHours} jam yang lalu`
  if (diffDays < 7) return `${diffDays} hari yang lalu`
  return formatDate(date)
}

// Status helpers
const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-amber-100 text-amber-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-neutral-100 text-neutral-800'
}

const getStatusDotClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-amber-500',
    approved: 'bg-green-500',
    rejected: 'bg-red-500'
  }
  return classes[status] || 'bg-neutral-500'
}

const getStatusText = (status: string) => {
  const texts: Record<string, string> = {
    pending: 'Menunggu Review',
    approved: 'Disetujui',
    rejected: 'Ditolak'
  }
  return texts[status] || status
}

// Activity helpers
const getActivityIconClass = (type: string) => {
  const classes: Record<string, string> = {
    upload: 'bg-blue-100 text-blue-600',
    approved: 'bg-green-100 text-green-600',
    rejected: 'bg-red-100 text-red-600',
    comment: 'bg-purple-100 text-purple-600'
  }
  return classes[type] || 'bg-neutral-100 text-neutral-600'
}

const getActivityIcon = (type: string) => {
  const icons: Record<string, string> = {
    upload: 'i-lucide-upload',
    approved: 'i-lucide-check-circle',
    rejected: 'i-lucide-x-circle',
    comment: 'i-lucide-message-circle'
  }
  return icons[type] || 'i-lucide-bell'
}

// Format date
const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Navbar functions
const toggleProfileMenu = () => {
  showProfileMenu.value = !showProfileMenu.value
}

// Navigation functions
const goToLandingPage = () => {
  router.push('/')
}

const openUploadModal = () => {
  showUploadModal.value = true
}

const logout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  localStorage.removeItem('last_known_role')
  router.push('/login')
}

// Close profile menu when clicking outside
const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement
  if (!target.closest('.relative')) {
    showProfileMenu.value = false
  }
}

onMounted(async () => {
  // Load user data
  const userStr = localStorage.getItem('user')
  if (userStr) {
    try {
      const user = JSON.parse(userStr)
      userName.value = user.name || 'User'
      userEmail.value = user.email || ''
    } catch {
      userName.value = 'User'
      userEmail.value = ''
    }
  }

  // Load dashboard data
  loadStats()
  loadRecentActivities()

  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

const viewDocument = (doc: UploadedDocument) => {
  router.push(`/detail/${doc.id}`)
}

const editDocument = (doc: UploadedDocument) => {
  console.log('Edit dokumen:', doc)
}

const deleteDocument = async (doc: UploadedDocument) => {
  if (!confirm(`Hapus dokumen "${doc.title}"?`)) return

  try {
    await api.documents.delete(doc.id)
    documents.value = documents.value.filter(d => d.id !== doc.id)
    await loadStats()
  } catch (error) {
    console.error('Gagal menghapus dokumen:', error)
    alert('Gagal menghapus dokumen. Silakan coba lagi.')
  }
}

const handleDocumentUploaded = async () => {
  showUploadModal.value = false
  await loadDocuments()
  await loadStats()
  loadRecentActivities()
}
</script>
