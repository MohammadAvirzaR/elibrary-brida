<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading State -->
    <div v-if="isLoading" class="flex items-center justify-center min-h-screen">
      <div class="text-center">
        <i-lucide-loader-2 class="w-12 h-12 text-blue-600 animate-spin mx-auto mb-4" />
        <p class="text-gray-600">Memuat detail dokumen...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex items-center justify-center min-h-screen">
      <div class="text-center max-w-md">
        <i-lucide-alert-circle class="w-16 h-16 text-red-500 mx-auto mb-4" />
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Dokumen Tidak Ditemukan</h2>
        <p class="text-gray-600 mb-6">{{ error }}</p>
        <button
          @click="router.back()"
          class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
        >
          Kembali
        </button>
      </div>
    </div>

    <!-- Document Detail -->
    <div v-else-if="document" class="container mx-auto px-3 sm:px-4 py-4 sm:py-6 md:py-8 max-w-7xl">
      <!-- Header -->
      <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <button
          @click="router.back()"
          class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition"
        >
          <i-lucide-arrow-left class="w-4 h-4 sm:w-5 sm:h-5" />
          <span class="text-sm sm:text-base font-medium">Kembali</span>
        </button>

        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
          <!-- Status Badge -->
          <span
            :class="[
              'px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-semibold',
              getStatusClass(document.status)
            ]"
          >
            {{ formatStatus(document.status) }}
          </span>

          <!-- Action Buttons for Admin/Reviewer -->
          <div v-if="canReview && document.status === 'pending'" class="flex items-center gap-2 flex-1 sm:flex-initial">
            <button
              @click="approveDocument"
              :disabled="isProcessing"
              class="flex-1 sm:flex-initial flex items-center justify-center gap-1.5 px-3 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition disabled:opacity-50"
            >
              <i-lucide-check class="w-3 h-3 sm:w-4 sm:h-4" />
              <span class="hidden sm:inline">Approve</span>
              <span class="sm:hidden">✓</span>
            </button>
            <button
              @click="showRejectModal = true"
              :disabled="isProcessing"
              class="flex-1 sm:flex-initial flex items-center justify-center gap-1.5 px-3 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition disabled:opacity-50"
            >
              <i-lucide-x class="w-3 h-3 sm:w-4 sm:h-4" />
              <span class="hidden sm:inline">Reject</span>
              <span class="sm:hidden">✗</span>
            </button>
          </div>

          <!-- Download Button -->
          <button
            @click="downloadDocument"
            class="flex-1 sm:flex-initial flex items-center justify-center gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
          >
            <i-lucide-download class="w-3 h-3 sm:w-4 sm:h-4" />
            Download
          </button>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
        <!-- Left: Document Preview -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
          <!-- PDF Preview -->
          <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-3 sm:py-4">
              <h2 class="text-base sm:text-lg md:text-xl font-bold text-white flex items-center gap-2">
                <i-lucide-file-text class="w-5 h-5 sm:w-6 sm:h-6" />
                Preview Dokumen
              </h2>
            </div>
            <div class="p-3 sm:p-4 md:p-6">
              <!-- PDF Loading State -->
              <div v-if="isPdfLoading" class="flex items-center justify-center h-96 sm:h-[600px] md:h-[800px] bg-gray-100 rounded-lg">
                <div class="text-center">
                  <i-lucide-loader-2 class="w-12 h-12 text-blue-600 animate-spin mx-auto mb-4" />
                  <p class="text-gray-600 font-medium">Memuat preview dokumen...</p>
                  <p class="text-gray-500 text-sm mt-2">Mohon tunggu sebentar</p>
                </div>
              </div>
              <!-- PDF Preview -->
              <div v-else-if="pdfUrl" class="w-full h-96 sm:h-[600px] md:h-[800px] bg-gray-100 rounded-lg overflow-hidden">
                <iframe
                  :src="pdfUrl"
                  class="w-full h-full border-0"
                  title="Document Preview"
                />
              </div>
              <!-- No Preview Available -->
              <div v-else class="flex items-center justify-center h-64 sm:h-80 md:h-96 bg-gray-100 rounded-lg">
                <div class="text-center">
                  <i-lucide-file class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                  <p class="text-gray-600">Preview tidak tersedia</p>
                  <button
                    @click="downloadDocument"
                    class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                  >
                    Download untuk melihat
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Attachments -->
          <div v-if="document.attachments && document.attachments.length > 0" class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
              <h3 class="text-base sm:text-lg font-bold text-gray-900 flex items-center gap-2">
                <i-lucide-paperclip class="w-4 h-4 sm:w-5 sm:h-5" />
                File Lampiran ({{ document.attachments.length }})
              </h3>
            </div>
            <div class="p-3 sm:p-4 md:p-6">
              <div class="space-y-2 sm:space-y-3">
                <div
                  v-for="(attachment, index) in document.attachments"
                  :key="index"
                  class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                >
                  <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                    <div class="p-1.5 sm:p-2 bg-blue-100 rounded-lg flex-shrink-0">
                      <i-lucide-file class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" />
                    </div>
                    <div class="min-w-0">
                      <p class="text-sm sm:text-base font-medium text-gray-900 truncate">{{ attachment.filename || `Attachment ${index + 1}` }}</p>
                      <p class="text-xs sm:text-sm text-gray-500">{{ formatFileSize(attachment.file_size) }}</p>
                    </div>
                  </div>
                  <button
                    @click="downloadAttachment(attachment)"
                    class="p-1.5 sm:p-2 hover:bg-blue-100 rounded-lg transition flex-shrink-0"
                  >
                    <i-lucide-download class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" />
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Abstract -->
          <div v-if="document.abstract_id || document.abstract_en" class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
              <h3 class="text-base sm:text-lg font-bold text-gray-900">Abstrak</h3>
            </div>
            <div class="p-4 sm:p-6 space-y-4">
              <div v-if="document.abstract_id">
                <h4 class="text-sm sm:text-base font-semibold text-gray-700 mb-2">Bahasa Indonesia</h4>
                <p class="text-sm sm:text-base text-gray-600 leading-relaxed whitespace-pre-wrap">{{ document.abstract_id }}</p>
              </div>
              <div v-if="document.abstract_en" class="pt-4 border-t border-gray-200">
                <h4 class="text-sm sm:text-base font-semibold text-gray-700 mb-2">English</h4>
                <p class="text-sm sm:text-base text-gray-600 leading-relaxed whitespace-pre-wrap">{{ document.abstract_en }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Document Details -->
        <div class="space-y-4 sm:space-y-6">
          <!-- Basic Information -->
          <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200">
            <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-blue-600 to-blue-700">
              <h3 class="text-base sm:text-lg font-bold text-white">Informasi Dokumen</h3>
            </div>
            <div class="p-4 sm:p-6 space-y-3 sm:space-y-4">
              <!-- Title -->
              <div>
                <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 mb-2">{{ document.title }}</h1>
              </div>

              <!-- Authors -->
              <div v-if="document.authors && document.authors.length > 0">
                <label class="text-xs sm:text-sm font-semibold text-gray-600 block mb-2">Penulis</label>
                <div class="space-y-2">
                  <div
                    v-for="(author, index) in document.authors"
                    :key="index"
                    class="flex items-start gap-2"
                  >
                    <i-lucide-user class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-400 mt-1 flex-shrink-0" />
                    <div class="min-w-0">
                      <p class="text-sm sm:text-base font-medium text-gray-900">
                        {{ author.first_name }} {{ author.last_name }}
                      </p>
                      <p v-if="author.email" class="text-xs sm:text-sm text-gray-500 truncate">{{ author.email }}</p>
                      <p v-if="author.institution" class="text-xs sm:text-sm text-gray-500">{{ author.institution }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Supervisors -->
              <div v-if="document.supervisors && document.supervisors.length > 0" class="pt-4 border-t border-gray-200">
                <label class="text-sm font-semibold text-gray-600 block mb-2">Pembimbing</label>
                <div class="space-y-2">
                  <div
                    v-for="(supervisor, index) in document.supervisors"
                    :key="index"
                    class="flex items-center gap-2"
                  >
                    <i-lucide-graduation-cap class="w-4 h-4 text-gray-400 flex-shrink-0" />
                    <p class="text-gray-900">{{ supervisor.name }}</p>
                  </div>
                </div>
              </div>

              <!-- Year Published -->
              <div v-if="document.year_published" class="pt-4 border-t border-gray-200">
                <label class="text-sm font-semibold text-gray-600 block mb-1">Tahun Terbit</label>
                <p class="text-gray-900">{{ document.year_published }}</p>
              </div>

              <!-- Language -->
              <div v-if="document.language" class="pt-4 border-t border-gray-200">
                <label class="text-sm font-semibold text-gray-600 block mb-1">Bahasa</label>
                <p class="text-gray-900">{{ formatLanguage(document.language) }}</p>
              </div>

              <!-- Document Type -->
              <div v-if="document.type" class="pt-4 border-t border-gray-200">
                <label class="text-sm font-semibold text-gray-600 block mb-1">Jenis Dokumen</label>
                <p class="text-gray-900">{{ document.type.name || document.type }}</p>
              </div>

              <!-- Keywords -->
              <div v-if="document.keywords" class="pt-4 border-t border-gray-200">
                <label class="text-sm font-semibold text-gray-600 block mb-2">Kata Kunci</label>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="(keyword, index) in document.keywords.split(',')"
                    :key="index"
                    class="px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded-full"
                  >
                    {{ keyword.trim() }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Additional Information -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-bold text-gray-900">Informasi Tambahan</h3>
            </div>
            <div class="p-6 space-y-4">
              <!-- Uploader -->
              <div v-if="document.user">
                <label class="text-sm font-semibold text-gray-600 block mb-1">Diunggah Oleh</label>
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold">
                    {{ (document.user.name || document.user.full_name || document.user.username || 'U').charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <p class="font-medium text-gray-900">{{ document.user.name || document.user.full_name || document.user.username }}</p>
                    <p class="text-sm text-gray-500">{{ document.user.email }}</p>
                  </div>
                </div>
              </div>

              <!-- Upload Date -->
              <div class="pt-4 border-t border-gray-200">
                <label class="text-sm font-semibold text-gray-600 block mb-1">Tanggal Upload</label>
                <p class="text-gray-900">{{ formatDate(document.created_at) }}</p>
              </div>

              <!-- Last Updated -->
              <div v-if="document.updated_at" class="pt-4 border-t border-gray-200">
                <label class="text-sm font-semibold text-gray-600 block mb-1">Terakhir Diupdate</label>
                <p class="text-gray-900">{{ formatDate(document.updated_at) }}</p>
              </div>

              <!-- Access Right -->
              <div v-if="document.access_right" class="pt-4 border-t border-gray-200">
                <label class="text-sm font-semibold text-gray-600 block mb-1">Hak Akses</label>
                <span
                  :class="[
                    'inline-flex items-center gap-1.5 px-3 py-1 text-sm font-medium rounded-full',
                    document.access_right === 'public' ? 'bg-green-100 text-green-700' :
                    document.access_right === 'internal' ? 'bg-yellow-100 text-yellow-700' :
                    'bg-red-100 text-red-700'
                  ]"
                >
                  <i-lucide-lock v-if="document.access_right !== 'public'" class="w-3 h-3" />
                  <i-lucide-globe v-else class="w-3 h-3" />
                  {{ formatAccessRight(document.access_right) }}
                </span>
              </div>

              <!-- Funding -->
              <div v-if="document.funding_program" class="pt-4 border-t border-gray-200">
                <label class="text-sm font-semibold text-gray-600 block mb-1">Program Pendanaan</label>
                <p class="text-gray-900">{{ document.funding_program }}</p>
              </div>

              <!-- Research Location -->
              <div v-if="document.research_location" class="pt-4 border-t border-gray-200">
                <label class="text-sm font-semibold text-gray-600 block mb-1">Lokasi Penelitian</label>
                <p class="text-gray-900">{{ document.research_location }}</p>
              </div>
            </div>
          </div>

          <!-- Admin Notes (for rejected documents) -->
          <div v-if="document.admin_notes && document.status === 'rejected'" class="bg-red-50 border border-red-200 rounded-xl p-6">
            <div class="flex items-start gap-3">
              <i-lucide-alert-circle class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
              <div>
                <h4 class="font-semibold text-red-900 mb-2">Alasan Penolakan</h4>
                <p class="text-red-700 text-sm whitespace-pre-wrap">{{ document.admin_notes }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reject Modal -->
    <Teleport to="body">
      <div
        v-if="showRejectModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        @click.self="showRejectModal = false"
      >
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Tolak Dokumen</h3>
            <button
              @click="showRejectModal = false"
              class="p-2 hover:bg-gray-100 rounded-lg transition"
            >
              <i-lucide-x class="w-5 h-5 text-gray-500" />
            </button>
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Alasan Penolakan <span class="text-red-500">*</span>
            </label>
            <textarea
              v-model="rejectReason"
              rows="4"
              placeholder="Masukkan alasan penolakan dokumen..."
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
            />
            <p v-if="rejectError" class="text-sm text-red-600 mt-1">{{ rejectError }}</p>
          </div>

          <div class="flex items-center gap-3">
            <button
              @click="showRejectModal = false"
              class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium"
            >
              Batal
            </button>
            <button
              @click="rejectDocument"
              :disabled="isProcessing"
              class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium disabled:opacity-50"
            >
              {{ isProcessing ? 'Memproses...' : 'Tolak Dokumen' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'
import { useToast } from '@/composables/useToast'

interface Author {
  id?: number
  first_name: string
  last_name?: string
  email?: string
  institution?: string
}

interface Supervisor {
  id?: number
  name: string
}

interface Attachment {
  id: number
  filename?: string
  file_name?: string
  file_path: string
  file_size?: number
  file_type?: string
}

interface DocumentType {
  id: number
  name: string
}

interface User {
  id: number
  name?: string
  full_name?: string
  username?: string
  email: string
  role?: {
    id: number
    name: string
  }
}

interface Document {
  id: number
  title: string
  abstract_id?: string
  abstract_en?: string
  year_published?: number
  language?: string
  keywords?: string
  status: string
  access_right?: string
  funding_program?: string
  research_location?: string
  admin_notes?: string
  created_at: string
  updated_at?: string
  file_path?: string
  type?: DocumentType
  user?: User
  authors?: Author[]
  supervisors?: Supervisor[]
  attachments?: Attachment[]
}

interface ApiResponse {
  success: boolean
  data: Document
  message?: string
}

const router = useRouter()
const route = useRoute()
const { toast } = useToast()

const document = ref<Document | null>(null)
const isLoading = ref(true)
const error = ref('')
const isProcessing = ref(false)
const showRejectModal = ref(false)
const rejectReason = ref('')
const rejectError = ref('')

const isPdfLoading = ref(false)

const pdfUrl = computed(() => {
  if (!document.value?.id) return null
  const baseUrl = 'http://127.0.0.1:8000'
  const token = localStorage.getItem('auth_token')

  // Gunakan token di URL - satu-satunya cara reliable untuk iframe
  if (token) {
    return `${baseUrl}/api/documents/${document.value.id}/file?token=${token}`
  }

  // Guest: approved documents
  if (document.value.status === 'approved') {
    return `${baseUrl}/api/documents/${document.value.id}/file`
  }

  return null
})

const userRole = ref('')
const canReview = computed(() => {
  return ['admin', 'super_admin', 'reviewer'].includes(userRole.value)
})

onMounted(async () => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    try {
      const user = JSON.parse(storedUser)
      userRole.value = user.role || ''
    } catch {
      userRole.value = ''
    }
  }

  await loadDocument()
})

// No cleanup needed

const loadDocument = async () => {
  try {
    isLoading.value = true
    const documentId = route.params.id as string

    const response = await api.documents.getById(parseInt(documentId)) as ApiResponse

    if (response.success && response.data) {
      document.value = response.data
    } else {
      error.value = 'Dokumen tidak ditemukan'
    }
  } catch (err) {
    console.error('Error loading document:', err)
    error.value = 'Gagal memuat detail dokumen'
  } finally {
    isLoading.value = false
  }
}

const approveDocument = async () => {
  if (!document.value || !confirm('Apakah Anda yakin ingin menyetujui dokumen ini?')) return

  try {
    isProcessing.value = true
    const documentId = document.value.id

    await api.documents.update(documentId, { status: 'approved' })

    toast.success('Dokumen Disetujui', 'Dokumen berhasil disetujui dan dipublikasikan')

    await loadDocument()
  } catch (err) {
    console.error('Error approving document:', err)
    toast.error('Gagal Menyetujui', 'Terjadi kesalahan saat menyetujui dokumen')
  } finally {
    isProcessing.value = false
  }
}

const rejectDocument = async () => {
  rejectError.value = ''

  if (!rejectReason.value.trim()) {
    rejectError.value = 'Alasan penolakan harus diisi'
    return
  }

  if (!document.value) return

  try {
    isProcessing.value = true
    const documentId = document.value.id

    await api.documents.update(documentId, {
      status: 'rejected',
      admin_notes: rejectReason.value
    })

    toast.warning('Dokumen Ditolak', 'Dokumen telah ditolak dengan alasan yang diberikan')

    showRejectModal.value = false
    rejectReason.value = ''


    await loadDocument()
  } catch (err) {
    console.error('Error rejecting document:', err)
    toast.error('Gagal Menolak', 'Terjadi kesalahan saat menolak dokumen')
  } finally {
    isProcessing.value = false
  }
}

const downloadDocument = async () => {
  if (!document.value?.id) return

  const baseUrl = 'http://127.0.0.1:8000'
  const token = localStorage.getItem('auth_token')

  try {
    const headers: HeadersInit = {
      'Accept': 'application/pdf'
    }

    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }

    const response = await fetch(`${baseUrl}/api/documents/${document.value.id}/file`, {
      headers
    })

    if (response.ok) {
      const blob = await response.blob()
      const url = URL.createObjectURL(blob)
      const a = window.document.createElement('a')
      a.href = url
      a.download = `${document.value.title}.pdf`
      window.document.body.appendChild(a)
      a.click()
      window.document.body.removeChild(a)
      URL.revokeObjectURL(url)

      toast.success('Download Berhasil', 'Dokumen berhasil diunduh')
    } else {
      toast.error('Download Gagal', 'Gagal mengunduh dokumen')
    }
  } catch (err) {
    console.error('Error downloading document:', err)
    toast.error('Download Gagal', 'Terjadi kesalahan saat mengunduh dokumen')
  }
}

const downloadAttachment = (attachment: Attachment) => {
  if (!document.value?.id || !attachment.id) return
  const baseUrl = 'http://127.0.0.1:8000'
  const token = localStorage.getItem('auth_token')

  let url = `${baseUrl}/api/documents/${document.value.id}/attachments/${attachment.id}/file`
  if (token) {
    url += `?token=${token}`
  }

  window.open(url, '_blank')
}

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    'pending': 'bg-amber-100 text-amber-800 border border-amber-200',
    'approved': 'bg-green-100 text-green-800 border border-green-200',
    'rejected': 'bg-red-100 text-red-800 border border-red-200',
    'submitted': 'bg-blue-100 text-blue-800 border border-blue-200'
  }
  return classes[status] || 'bg-gray-100 text-gray-800 border border-gray-200'
}

const formatStatus = (status: string) => {
  const statuses: Record<string, string> = {
    'pending': 'Menunggu Review',
    'approved': 'Disetujui',
    'rejected': 'Ditolak',
    'submitted': 'Diajukan'
  }
  return statuses[status] || status
}

const formatLanguage = (lang: string) => {
  const languages: Record<string, string> = {
    'id': 'Bahasa Indonesia',
    'en': 'English',
    'other': 'Lainnya'
  }
  return languages[lang] || lang
}

const formatAccessRight = (access: string) => {
  const rights: Record<string, string> = {
    'public': 'Publik',
    'internal': 'Internal',
    'embargo': 'Embargo'
  }
  return rights[access] || access
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatFileSize = (bytes?: number) => {
  if (!bytes) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
