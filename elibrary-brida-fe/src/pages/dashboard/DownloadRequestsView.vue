<template>
  <div class="min-h-screen bg-gray-50 p-4 sm:p-6 md:p-8">
    <div class="max-w-6xl mx-auto">

      <!-- Header -->
      <div class="flex items-center gap-4 mb-6">
        <button @click="router.back()" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
          <i-lucide-arrow-left class="w-5 h-5" />
          <span class="text-sm font-medium">Kembali</span>
        </button>
        <div>
          <h1 class="text-xl font-bold text-gray-900">Permintaan Unduh Dokumen</h1>
          <p class="text-sm text-gray-500 mt-0.5">Kelola permintaan download dari pengguna</p>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="flex gap-2 mb-4">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          @click="activeTab = tab.value"
          class="px-4 py-2 rounded-lg text-sm font-medium transition"
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

      <!-- Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div v-if="isLoading" class="flex items-center justify-center py-16">
          <i-lucide-loader-2 class="w-8 h-8 text-blue-600 animate-spin" />
        </div>

        <div v-else-if="filteredRequests.length === 0" class="text-center py-16 text-gray-500">
          <i-lucide-inbox class="w-12 h-12 mx-auto mb-3 text-gray-300" />
          <p>Tidak ada permintaan {{ activeTab === 'pending' ? 'yang menunggu' : '' }}</p>
        </div>

        <table v-else class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left px-4 py-3 font-semibold text-gray-700">Pemohon</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-700">Dokumen</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-700">Tujuan</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-700">Status</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-700">Tanggal</th>
              <th class="text-right px-4 py-3 font-semibold text-gray-700">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="req in filteredRequests" :key="req.id" class="hover:bg-gray-50 transition">
              <td class="px-4 py-3">
                <p class="font-medium text-gray-900">{{ req.requester_name }}</p>
                <p class="text-xs text-gray-500">{{ req.requester_email }}</p>
                <p v-if="req.institution" class="text-xs text-gray-400">{{ req.institution }}</p>
              </td>
              <td class="px-4 py-3 max-w-xs">
                <p class="truncate text-gray-900 font-medium">{{ req.title }}</p>
              </td>
              <td class="px-4 py-3 max-w-xs">
                <p class="text-gray-600 text-xs line-clamp-2">{{ req.purpose || '-' }}</p>
              </td>
              <td class="px-4 py-3">
                <span
                  class="px-2 py-1 rounded-full text-xs font-semibold"
                  :class="{
                    'bg-yellow-100 text-yellow-700': req.status === 'pending',
                    'bg-green-100 text-green-700': req.status === 'sent',
                    'bg-red-100 text-red-700': req.status === 'rejected',
                  }"
                >{{ statusLabel(req.status) }}</span>
              </td>
              <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">{{ req.created_at }}</td>
              <td class="px-4 py-3 text-right">
                <div v-if="req.status === 'pending'" class="flex justify-end gap-2">
                  <button
                    @click="sendDocument(req)"
                    :disabled="processingId === req.id"
                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium flex items-center gap-1.5 disabled:opacity-50"
                  >
                    <i-lucide-send class="w-3 h-3" />
                    Kirim
                  </button>
                  <button
                    @click="rejectRequest(req.id)"
                    :disabled="processingId === req.id"
                    class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-medium disabled:opacity-50"
                  >
                    Tolak
                  </button>
                </div>
                <span v-else-if="req.status === 'sent'" class="text-xs text-gray-400">
                  Terkirim {{ req.sent_at }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
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
const isLoading = ref(true)
const activeTab = ref('pending')
const processingId = ref<number | null>(null)
const showSendModal = ref(false)
const confirmTarget = ref<DownloadRequest | null>(null)
const adminNotes = ref('')
const isSending = ref(false)

const tabs = [
  { label: 'Menunggu', value: 'pending' },
  { label: 'Terkirim', value: 'sent' },
  { label: 'Ditolak', value: 'rejected' },
  { label: 'Semua', value: 'all' },
]

const pendingCount = computed(() => requests.value.filter(r => r.status === 'pending').length)

const filteredRequests = computed(() => {
  if (activeTab.value === 'all') return requests.value
  return requests.value.filter(r => r.status === activeTab.value)
})

const statusLabel = (status: string) => ({
  pending: 'Menunggu',
  sent: 'Terkirim',
  rejected: 'Ditolak',
}[status] || status)

const loadRequests = async () => {
  isLoading.value = true
  try {
    const res = await api.downloadRequests.getAll() as { success: boolean; data: DownloadRequest[] }
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
    await api.downloadRequests.reject(id)
    toast.success('Ditolak', 'Permintaan berhasil ditolak')
    await loadRequests()
  } catch {
    toast.error('Gagal', 'Terjadi kesalahan')
  } finally {
    processingId.value = null
  }
}

onMounted(loadRequests)
</script>
