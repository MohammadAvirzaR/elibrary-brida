<template>
  <Teleport to="body">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div
        class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto no-scrollbar"
        @click.stop
      >
        <!-- Header -->
        <div class="sticky top-0 bg-white border-b border-neutral-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
          <div>
            <h2 class="text-2xl font-bold text-neutral-900">Upload Dokumen Baru</h2>
            <p class="text-sm text-neutral-600 mt-1">Isi formulir di bawah untuk mengunggah dokumen</p>
          </div>
          <button
            @click="$emit('close')"
            class="text-neutral-400 hover:text-neutral-600 transition-colors"
          >
            <i-lucide-x class="w-6 h-6" />
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
          <!-- File Upload -->
          <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">
              File Dokumen <span class="text-red-500">*</span>
            </label>
            <div
              @dragover.prevent
              @drop.prevent="handleDrop"
              class="border-2 border-dashed border-neutral-300 rounded-lg p-8 text-center hover:border-blue-500 transition-colors cursor-pointer"
              :class="{ 'border-blue-500 bg-blue-50': isDragging }"
            >
              <input
                ref="fileInput"
                type="file"
                accept=".pdf,.doc,.docx"
                @change="handleFileSelect"
                class="hidden"
              />
              <div v-if="!form.file">
                <i-lucide-cloud-upload class="w-16 h-16 text-neutral-400 mx-auto mb-4" />
                <p class="text-neutral-700 font-medium mb-1">
                  Klik untuk upload atau drag & drop
                </p>
                <p class="text-sm text-neutral-500">
                  PDF, DOC, atau DOCX (Maks. 10MB)
                </p>
                <button
                  type="button"
                  @click="fileInput?.click()"
                  class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                  Pilih File
                </button>
              </div>
              <div v-else class="flex items-center justify-between bg-neutral-50 p-4 rounded-lg">
                <div class="flex items-center gap-3">
                  <i-lucide-file-text class="w-8 h-8 text-blue-600" />
                  <div class="text-left">
                    <p class="font-medium text-neutral-900">{{ form.file.name }}</p>
                    <p class="text-sm text-neutral-500">{{ formatFileSize(form.file.size) }}</p>
                  </div>
                </div>
                <button
                  type="button"
                  @click="removeFile"
                  class="text-red-600 hover:text-red-800"
                >
                  <i-lucide-trash-2 class="w-5 h-5" />
                </button>
              </div>
            </div>
            <p v-if="errors.file" class="text-red-500 text-sm mt-1">{{ errors.file }}</p>
          </div>

          <!-- Title -->
          <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">
              Judul Dokumen <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.title"
              type="text"
              placeholder="Masukkan judul dokumen"
              class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="{ 'border-red-500': errors.title }"
            />
            <p v-if="errors.title" class="text-red-500 text-sm mt-1">{{ errors.title }}</p>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">
              Deskripsi <span class="text-red-500">*</span>
            </label>
            <textarea
              v-model="form.description"
              rows="4"
              placeholder="Jelaskan isi dokumen secara singkat"
              class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
              :class="{ 'border-red-500': errors.description }"
            ></textarea>
            <p v-if="errors.description" class="text-red-500 text-sm mt-1">{{ errors.description }}</p>
          </div>

          <!-- Category & Type -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-neutral-700 mb-2">
                Kategori <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.category"
                class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': errors.category }"
              >
                <option value="">Pilih Kategori</option>
                <option value="penelitian">Penelitian</option>
                <option value="laporan">Laporan</option>
                <option value="artikel">Artikel</option>
                <option value="jurnal">Jurnal</option>
                <option value="skripsi">Skripsi/Tesis</option>
                <option value="buku">Buku</option>
                <option value="lainnya">Lainnya</option>
              </select>
              <p v-if="errors.category" class="text-red-500 text-sm mt-1">{{ errors.category }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-neutral-700 mb-2">
                Tahun Terbit
              </label>
              <input
                v-model="form.year"
                type="number"
                min="1900"
                :max="new Date().getFullYear()"
                placeholder="2024"
                class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>

          <!-- Author & Publisher -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-neutral-700 mb-2">
                Penulis <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.author"
                type="text"
                placeholder="Nama penulis"
                class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': errors.author }"
              />
              <p v-if="errors.author" class="text-red-500 text-sm mt-1">{{ errors.author }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-neutral-700 mb-2">
                Penerbit
              </label>
              <input
                v-model="form.publisher"
                type="text"
                placeholder="Nama penerbit"
                class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>

          <!-- Keywords -->
          <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">
              Kata Kunci
            </label>
            <input
              v-model="form.keywords"
              type="text"
              placeholder="Pisahkan dengan koma (contoh: penelitian, ilmiah, teknologi)"
              class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <p class="text-xs text-neutral-500 mt-1">Kata kunci membantu orang lain menemukan dokumen Anda</p>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t border-neutral-200">
            <button
              type="button"
              @click="$emit('close')"
              class="px-6 py-2.5 border border-neutral-300 text-neutral-700 rounded-lg hover:bg-neutral-50 transition-colors font-medium"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2"
            >
              <i-lucide-loader-2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
              <i-lucide-upload v-else class="w-4 h-4" />
              {{ isSubmitting ? 'Mengunggah...' : 'Upload Dokumen' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import api from '@/services/api'
import { useToast } from '@/composables/useToast'

const emit = defineEmits(['close', 'uploaded'])
const { toast } = useToast()

const fileInput = ref<HTMLInputElement>()
const isDragging = ref(false)
const isSubmitting = ref(false)

const form = reactive({
  file: null as File | null,
  title: '',
  description: '',
  category: '',
  year: new Date().getFullYear(),
  author: '',
  publisher: '',
  keywords: ''
})

const errors = reactive({
  file: '',
  title: '',
  description: '',
  category: '',
  author: ''
})

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    validateFile(target.files[0])
  }
}

const handleDrop = (event: DragEvent) => {
  isDragging.value = false
  if (event.dataTransfer?.files && event.dataTransfer.files[0]) {
    validateFile(event.dataTransfer.files[0])
  }
}

const validateFile = (file: File) => {
  errors.file = ''

  // Check file type
  const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
  if (!allowedTypes.includes(file.type)) {
    errors.file = 'Hanya file PDF, DOC, atau DOCX yang diperbolehkan'
    return
  }

  // Check file size (10MB)
  const maxSize = 10 * 1024 * 1024
  if (file.size > maxSize) {
    errors.file = 'Ukuran file maksimal 10MB'
    return
  }

  form.file = file

  // Auto-fill title from filename if empty
  if (!form.title) {
    form.title = file.name.replace(/\.[^/.]+$/, '')
  }
}

const removeFile = () => {
  form.file = null
  errors.file = ''
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const formatFileSize = (bytes: number) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}

const validateForm = () => {
  let isValid = true

  // Reset errors
  Object.keys(errors).forEach(key => {
    errors[key as keyof typeof errors] = ''
  })

  if (!form.file) {
    errors.file = 'File dokumen harus dipilih'
    isValid = false
  }

  if (!form.title.trim()) {
    errors.title = 'Judul dokumen harus diisi'
    isValid = false
  }

  if (!form.description.trim()) {
    errors.description = 'Deskripsi harus diisi'
    isValid = false
  }

  if (!form.category) {
    errors.category = 'Kategori harus dipilih'
    isValid = false
  }

  if (!form.author.trim()) {
    errors.author = 'Nama penulis harus diisi'
    isValid = false
  }

  return isValid
}

const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  isSubmitting.value = true

  try {
    const formData = new FormData()

    if (form.file) {
      formData.append('file', form.file)
    }
    formData.append('title', form.title)
    formData.append('description', form.description)
    formData.append('category', form.category)
    formData.append('year', form.year.toString())
    formData.append('author', form.author)
    formData.append('publisher', form.publisher)
    formData.append('keywords', form.keywords)
    formData.append('status', 'pending')

    const response = await api.documents.upload(formData) as { success: boolean; data: { id: number; title: string; status: string; created_at: string; [key: string]: unknown } }

    if (response.success && response.data) {
      const newDocument = {
        id: response.data.id,
        title: response.data.title,
        author: form.author,
        category: form.category,
        status: response.data.status,
        uploadDate: response.data.created_at,
        description: form.description,
        year: form.year,
        publisher: form.publisher,
        keywords: form.keywords
      }

      toast.success('Upload Tersimpan', 'Dokumen berhasil diunggah dan menunggu persetujuan admin')

      emit('uploaded', newDocument)
      emit('close')

      form.file = null
      form.title = ''
      form.author = ''
      form.year = new Date().getFullYear()
      form.publisher = ''
      form.description = ''
      form.category = ''
      form.keywords = ''
    }
  } catch (error) {
    console.error('Upload failed:', error)

    let errorMessage = ''

    if (error instanceof Error) {
      if (error.message.includes('403') || error.message.includes('Forbidden')) {
        errorMessage = 'Anda tidak memiliki izin untuk upload dokumen. Silakan ajukan permohonan sebagai kontributor terlebih dahulu.'
      } else if (error.message.includes('401') || error.message.includes('Unauthorized')) {
        errorMessage = 'Sesi Anda telah berakhir. Silakan login kembali.'
      } else if (error.message.includes('422') || error.message.includes('validation')) {
        errorMessage = 'Data yang Anda masukkan tidak valid. Periksa kembali semua field.'
      } else if (error.message.includes('413') || error.message.includes('too large')) {
        errorMessage = 'Ukuran file terlalu besar. Maksimal 10MB.'
      } else {
        errorMessage = error.message
      }
    } else {
      errorMessage = 'Silakan coba lagi atau hubungi administrator.'
    }

    toast.error('Gagal Mengunggah', errorMessage)
  } finally {
    isSubmitting.value = false
  }
}
</script>
