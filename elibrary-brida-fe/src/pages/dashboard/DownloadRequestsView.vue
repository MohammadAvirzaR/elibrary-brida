<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar (placeholder for consistency with DashboardView) -->
    
    <!-- Main Content -->
    <div class="min-h-screen">
      <!-- Header -->
      <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-4">
          <div class="flex items-center gap-4">
            <button @click="router.back()" class="p-2 hover:bg-gray-100 rounded-lg transition text-gray-600">
              <i-lucide-arrow-left class="w-5 h-5" />
            </button>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Permintaan Unduh Dokumen</h1>
              <p class="text-sm text-gray-500 mt-0.5">
                {{ isOwnerMode ? 'Kelola permintaan unduh pada dokumen Anda' : 'Kelola permintaan download dari pengguna' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Content -->
      <main class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-8">
        <!-- Filter Tabs -->
        <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
          <button
            v-for="tab in tabs"
            :key="tab.value"
            @click="activeTab = tab.value"
            class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap"
            :class="activeTab === tab.value
              ? 'bg-blue-600 text-white'
              : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'"
          >
            {{ tab.label }}
            <span
              v-if="tab.value === 'pending' && pendingCount > 0"
              class="ml-1.5 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full"
            >{{ pendingCount }}</span>
          </button>
        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
          <!-- Search Header -->
          <div class="flex items-center gap-2 md:gap-4 p-3 md:p-5 border-b bg-gray-50">
            <button class="p-2 hover:bg-gray-200 rounded-lg transition hidden">
              <i-lucide-filter class="w-4 h-4 md:w-5 md:h-5 text-gray-600" />
            </button>
            <div class="relative flex-1 max-w-md">
              <i-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari pemohon, dokumen, atau email..."
                class="w-full pl-9 md:pl-10 pr-4 py-2 md:py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
              />
            </div>
            <div v-if="filteredRequests.length > 0" class="text-xs sm:text-sm text-gray-600">
              {{ filteredRequests.length }} item
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="isLoading" class="flex items-center justify-center py-16">
            <i-lucide-loader-2 class="w-8 h-8 text-blue-600 animate-spin" />
          </div>

          <!-- Empty State -->
          <div v-else-if="filteredRequests.length === 0" class="text-center py-16 text-gray-500">
            <i-lucide-inbox class="w-12 h-12 mx-auto mb-3 text-gray-300" />
            <p class="font-medium">Tidak ada permintaan {{ activeTab === 'pending' ? 'yang menunggu' : '' }}</p>
            <p class="text-sm text-gray-400 mt-1">Permintaan download dari pengguna akan muncul di sini</p>
          </div>

          <!-- Table -->
          <div v-else class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            <table class="w-full">
              <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pemohon</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dokumen</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Institusi</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                  <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 bg-white">
                <tr v-for="(req, index) in paginatedRequests" :key="req.id" class="hover:bg-blue-50 transition-colors">
                  <td class="px-6 py-4 text-sm font-medium text-gray-500">{{ (currentPage - 1) * rowsPerPage + index + 1 }}</td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold text-sm">
                        {{ req.requester_name.split(' ').map((n: string) => n[0]).join('').substring(0, 2) }}
                      </div>
                      <div>
                        <div class="text-sm font-semibold text-gray-900">{{ req.requester_name }}</div>
                        <div class="text-xs text-gray-500">{{ req.requester_email }}</div>
                        <div v-if="req.institution" class="text-xs text-gray-400">{{ req.institution }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 max-w-xs">
                    <p class="text-sm font-medium text-gray-900 line-clamp-2">{{ req.title }}</p>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-sm text-gray-600">{{ req.institution || '-' }}</p>
                  </td>
                  <td class="px-6 py-4">
                    <span
                      class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full"
                      :class="{
                        'bg-yellow-100 text-yellow-800 border border-yellow-200': req.status === 'pending',
                        'bg-green-100 text-green-800 border border-green-200': req.status === 'sent' || req.status === 'approved',
                        'bg-red-100 text-red-800 border border-red-200': req.status === 'rejected',
                      }"
                    >
                      <i-lucide-clock v-if="req.status === 'pending'" class="w-3 h-3" />
                      <i-lucide-check-circle v-else-if="req.status === 'sent' || req.status === 'approved'" class="w-3 h-3" />
                      <i-lucide-x-circle v-else class="w-3 h-3" />
                      {{ statusLabel(req.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">{{ req.created_at }}</td>
                  <td class="px-6 py-4 text-right">
                    <div v-if="req.status === 'pending'" class="flex justify-end gap-2">
                      <button
                        @click="isOwnerMode ? approveRequest(req.id) : sendDocument(req)"
                        :disabled="processingId === req.id"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-xs font-medium transition shadow-sm hover:shadow flex items-center gap-1.5 disabled:opacity-50"
                      >
                        <i-lucide-check class="w-3.5 h-3.5" />
                        {{ isOwnerMode ? 'Setujui' : 'Kirim' }}
                      </button>
                      <button
                        @click="rejectRequest(req.id)"
                        :disabled="processingId === req.id"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-xs font-medium transition shadow-sm hover:shadow flex items-center gap-1.5 disabled:opacity-50"
                      >
                        <i-lucide-x class="w-3.5 h-3.5" />
                        Tolak
                      </button>
                    </div>
                    <span v-else class="text-xs text-gray-400">{{ statusLabel(req.status) }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1 && filteredRequests.length > 0" class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 md:px-6 py-3 md:py-4 border-t bg-gray-50">
            <div class="text-xs sm:text-sm text-gray-600">
              {{ startIndex }}-{{ endIndex }} of {{ filteredRequests.length }}
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4">
              <div class="flex items-center gap-2">
                <span class="text-xs sm:text-sm text-gray-600">Rows:</span>
                <select
                  v-model="rowsPerPage"
                  @change="currentPage = 1"
                  class="border border-gray-300 rounded px-2 py-1 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option :value="5">5</option>
                  <option :value="10">10</option>
                  <option :value="20">20</option>
                </select>
              </div>
              <div class="flex items-center gap-2">
                <button
                  @click="prevPage"
                  :disabled="currentPage === 1"
                  class="p-1 hover:bg-gray-200 rounded disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                  <i-lucide-chevron-left class="w-4 h-4 sm:w-5 sm:h-5" />
                </button>
                <span class="text-xs sm:text-sm text-gray-600">{{ currentPage }}/{{ totalPages || 1 }}</span>
                <button
                  @click="nextPage"
                  :disabled="currentPage >= totalPages"
                  class="p-1 hover:bg-gray-200 rounded disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                  <i-lucide-chevron-right class="w-4 h-4 sm:w-5 sm:h-5" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Send Confirm Modal -->
    <div v-if="showSendModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 space-y-4">
        <h3 class="text-lg font-bold text-gray-900">Kirim Dokumen</h3>
        <p class="text-sm text-gray-600">
          Kirim dokumen <strong>{{ confirmTarget?.title }}</strong> ke
          <strong>{{ confirmTarget?.requester_email }}</strong>?
        </p>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan (opsional)</label>
          <textarea
            v-model="adminNotes"
            rows="3"
            placeholder="Catatan untuk pemohon..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
          />
        </div>
        <div class="flex gap-3">
          <button @click="showSendModal = false" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Batal</button>
          <button
            @click="confirmSend"
            :disabled="isSending"
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 disabled:opacity-50 flex items-center justify-center gap-2"
          >
            <i-lucide-loader-2 v-if="isSending" class="w-4 h-4 animate-spin" />
            {{ isSending ? 'Mengirim...' : 'Kirim Dokumen' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const { toast } = useToast()

interface DownloadRequest {
  id: number
  document_id: number
  title: string
  requester_name: string
  requester_email: string
  institution?: string
  purpose?: string
  status: string
  sent_at?: string
  created_at: string
  admin_notes?: string
}

const requests = ref<DownloadRequest[]>([])
const isOwnerMode = computed(() => {
  try {
    const user = JSON.parse(localStorage.getItem('user') || '{}')
    return String(user.role || '').toLowerCase() === 'kontributor'
  } catch {
    return false
  }
})
const isLoading = ref(true)
const activeTab = ref('pending')
const processingId = ref<number | null>(null)
const showSendModal = ref(false)
const confirmTarget = ref<DownloadRequest | null>(null)
const adminNotes = ref('')
const isSending = ref(false)
const searchQuery = ref('')
const currentPage = ref(1)
const rowsPerPage = ref(5)

const tabs = [
  { label: 'Menunggu', value: 'pending' },
  ...(isOwnerMode.value ? [] : [{ label: 'Terkirim', value: 'sent' }]),
  { label: 'Ditolak', value: 'rejected' },
  ...(isOwnerMode.value ? [] : [{ label: 'Semua', value: 'all' }]),
]

const pendingCount = computed(() => requests.value.filter(r => r.status === 'pending').length)

const statusFiltered = computed(() => {
  if (activeTab.value === 'all') return requests.value
  return requests.value.filter(r => r.status === activeTab.value)
})

const filteredRequests = computed(() => {
  if (!searchQuery.value) return statusFiltered.value
  const query = searchQuery.value.toLowerCase()
  return statusFiltered.value.filter(item =>
    item.requester_name.toLowerCase().includes(query) ||
    item.requester_email.toLowerCase().includes(query) ||
    item.title.toLowerCase().includes(query) ||
    (item.institution || '').toLowerCase().includes(query)
  )
})

const paginatedRequests = computed(() => {
  const start = (currentPage.value - 1) * rowsPerPage.value
  const end = start + rowsPerPage.value
  return filteredRequests.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(filteredRequests.value.length / rowsPerPage.value)
})

const startIndex = computed(() => {
  return (currentPage.value - 1) * rowsPerPage.value + 1
})

const endIndex = computed(() => {
  return Math.min(currentPage.value * rowsPerPage.value, filteredRequests.value.length)
})

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const statusLabel = (status: string) => ({
  pending: 'Menunggu',
  sent: 'Terkirim',
  approved: 'Disetujui',
  rejected: 'Ditolak',
}[status] || status)

const loadRequests = async () => {
  isLoading.value = true
  try {
    const res = (isOwnerMode.value
      ? await api.downloadRequests.ownerPending()
      : await api.downloadRequests.getAll()) as { success: boolean; data: DownloadRequest[] }
    requests.value = res.data || []
  } catch {
    toast.error('Gagal Memuat', 'Tidak dapat memuat daftar permintaan')
  } finally {
    isLoading.value = false
  }
}

const sendDocument = (req: DownloadRequest) => {
  confirmTarget.value = req
  adminNotes.value = ''
  showSendModal.value = true
}

const confirmSend = async () => {
  if (!confirmTarget.value) return
  isSending.value = true
  try {
    await api.downloadRequests.send(confirmTarget.value.id, adminNotes.value || undefined)
    toast.success('Berhasil', `Dokumen dikirim ke ${confirmTarget.value.requester_email}`)
    showSendModal.value = false
    await loadRequests()
  } catch {
    toast.error('Gagal', 'Terjadi kesalahan saat mengirim dokumen')
  } finally {
    isSending.value = false
  }
}

const rejectRequest = async (id: number) => {
  processingId.value = id
  try {
    if (isOwnerMode.value) {
      await api.downloadRequests.ownerReject(id)
    } else {
      await api.downloadRequests.reject(id)
    }
    toast.success('Ditolak', 'Permintaan berhasil ditolak')
    await loadRequests()
  } catch {
    toast.error('Gagal', 'Terjadi kesalahan')
  } finally {
    processingId.value = null
  }
}

const approveRequest = async (id: number) => {
  processingId.value = id
  try {
    await api.downloadRequests.ownerApprove(id)
    toast.success('Disetujui', 'Permintaan berhasil disetujui dan link download dikirim')
    await loadRequests()
  } catch {
    toast.error('Gagal', 'Terjadi kesalahan')
  } finally {
    processingId.value = null
  }
}

onMounted(loadRequests)
</script>
