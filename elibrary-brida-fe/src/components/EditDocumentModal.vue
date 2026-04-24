<template>
  <Teleport to="body">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-2 sm:p-4">
      <div
        class="bg-white rounded-xl md:rounded-2xl shadow-2xl max-w-2xl w-full max-h-[95vh] sm:max-h-[90vh] overflow-x-auto no-scrollbar"
        @click.stop
      >
        <!-- Header -->
        <div class="sticky top-0 bg-white border-b border-neutral-200 px-4 sm:px-6 py-3 sm:py-4 rounded-t-xl md:rounded-t-2xl">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-neutral-900">Edit Dokumen</h2>
              <p class="text-xs sm:text-sm text-neutral-600 mt-1">
                Perbarui informasi dokumen Anda
              </p>
            </div>
            <button
              @click="$emit('close')"
              class="text-neutral-400 hover:text-neutral-600 transition-colors p-1"
            >
              <i-lucide-x class="w-5 h-5 sm:w-6 sm:h-6" />
            </button>
          </div>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="p-4 sm:p-6 space-y-4 sm:space-y-5">

          <!-- Title -->
          <div>
            <label class="block text-xs sm:text-sm font-medium text-neutral-700 mb-1.5 sm:mb-2">
              Judul Dokumen <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.title"
              type="text"
              placeholder="Masukkan judul dokumen"
              class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="{ 'border-red-500': errors.title }"
            />
            <p v-if="errors.title" class="text-red-500 text-xs sm:text-sm mt-1">{{ errors.title }}</p>
          </div>

          <!-- Language & Document Type -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            <div>
              <label class="block text-xs sm:text-sm font-medium text-neutral-700 mb-1.5 sm:mb-2">
                Bahasa <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.language"
                class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': errors.language }"
              >
                <option :value="null" disabled>Pilih bahasa</option>
                <option value="id">Bahasa Indonesia</option>
                <option value="en">English</option>
              </select>
              <p v-if="errors.language" class="text-red-500 text-xs sm:text-sm mt-1">{{ errors.language }}</p>
            </div>

            <div>
              <label class="block text-xs sm:text-sm font-medium text-neutral-700 mb-1.5 sm:mb-2">
                Jenis Dokumen
              </label>
              <select
                v-model.number="form.documentType"
                class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': errors.documentType }"
              >
                <option :value="null">-- Tidak dipilih --</option>
                <option v-for="type in documentTypes" :key="type.id" :value="type.id">
                  {{ type.type_name }}
                </option>
              </select>
              <p v-if="errors.documentType" class="text-red-500 text-xs sm:text-sm mt-1">{{ errors.documentType }}</p>
            </div>
          </div>

          <!-- Author & Year -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            <div>
              <label class="block text-xs sm:text-sm font-medium text-neutral-700 mb-1.5 sm:mb-2">
                Penulis
              </label>
              <input
                :value="document.author || 'N/A'"
                type="text"
                disabled
                class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-neutral-300 rounded-lg bg-neutral-100 text-neutral-600 cursor-not-allowed"
              />
              <p class="text-xs text-neutral-500 mt-1">Tidak dapat diubah (dikelola secara terpisah)</p>
            </div>

            <div>
              <label class="block text-xs sm:text-sm font-medium text-neutral-700 mb-1.5 sm:mb-2">
                Tahun Terbit <span class="text-red-500">*</span>
              </label>
              <input
                v-model.number="form.publicationYear"
                type="number"
                min="1900"
                :max="new Date().getFullYear()"
                placeholder="2024"
                class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': errors.publicationYear }"
              />
              <p v-if="errors.publicationYear" class="text-red-500 text-xs sm:text-sm mt-1">{{ errors.publicationYear }}</p>
            </div>
          </div>

          <!-- Subject -->
          <div>
            <label class="block text-xs sm:text-sm font-medium text-neutral-700 mb-1.5 sm:mb-2">
              Subjek
            </label>
            <select
              v-model.number="form.subject"
              class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option :value="null">-- Tidak dipilih --</option>
              <option v-for="s in subjects" :key="s.id" :value="s.id">
                {{ s.subject_name }}
              </option>
            </select>
          </div>

          <!-- Abstract -->
          <div>
            <label class="block text-xs sm:text-sm font-medium text-neutral-700 mb-1.5 sm:mb-2">
              Ringkasan (Indonesia)
            </label>
            <textarea
              v-model="form.abstractId"
              placeholder="Tuliskan ringkasan dokumen dalam bahasa Indonesia"
              rows="4"
              class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
            ></textarea>
            <p class="text-xs text-neutral-500 mt-1">Maksimal 500 karakter</p>
          </div>

          <!-- Abstract English -->
          <div>
            <label class="block text-xs sm:text-sm font-medium text-neutral-700 mb-1.5 sm:mb-2">
              Ringkasan (Inggris)
            </label>
            <textarea
              v-model="form.abstractEn"
              placeholder="Tuliskan ringkasan dokumen dalam bahasa Inggris"
              rows="4"
              class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
            ></textarea>
            <p class="text-xs text-neutral-500 mt-1">Maksimal 500 karakter</p>
          </div>

          <!-- Navigation Buttons -->
          <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-2 sm:gap-3 pt-4 sm:pt-5 border-t border-neutral-200">
            <button
              type="button"
              @click="$emit('close')"
              class="px-4 sm:px-6 py-2 sm:py-2.5 text-sm sm:text-base border border-neutral-300 text-neutral-700 rounded-lg hover:bg-neutral-50 transition-colors font-medium"
            >
              Batal
            </button>

            <button
              type="submit"
              :disabled="isSubmitting"
              class="px-4 sm:px-6 py-2 sm:py-2.5 text-sm sm:text-base bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2"
            >
              <span v-if="!isSubmitting">
                <i-lucide-save class="w-4 h-4 inline mr-2" />
                Simpan Perubahan
              </span>
              <span v-else class="inline-flex items-center gap-2">
                <i-lucide-loader-2 class="w-4 h-4 animate-spin" />
                Menyimpan...
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import api from '@/services/api'
import { useToast } from '@/composables/useToast'

interface DocumentForEdit {
  id: number
  title: string
  author: string
  publisher?: string
  year_published?: number
  type_id?: number
  subject_id?: number
  language?: string
  keywords?: string
  abstract_id?: string
  abstract_en?: string
  category_name?: string
}

const props = defineProps<{
  document: DocumentForEdit
}>()

const emit = defineEmits(['close', 'updated'])
const { toast } = useToast()

const isSubmitting = ref(false)
const subjects = ref<Array<{ id: number; subject_name: string }>>([])
const documentTypes = ref<Array<{ id: number; type_name: string }>>([])

const form = reactive({
  title: props.document.title || '',
  language: props.document.language || 'id',
  publicationYear: props.document.year_published || new Date().getFullYear(),
  documentType: props.document.type_id || null,
  subject: props.document.subject_id || null,
  abstractId: props.document.abstract_id || '',
  abstractEn: props.document.abstract_en || ''
})

const errors = reactive({
  title: '',
  language: '',
  publicationYear: '',
  documentType: ''
})

onMounted(async () => {
  await Promise.all([
    loadSubjects(),
    loadDocumentTypes()
  ])
})

const loadSubjects = async () => {
  try {
    const filters = await api.filters.getAll() as any
    if (filters.subjects && Array.isArray(filters.subjects)) {
      subjects.value = filters.subjects
    }
  } catch (error) {
    console.error('Failed to load subjects:', error)
  }
}

const loadDocumentTypes = async () => {
  try {
    const filters = await api.filters.getAll() as any
    if (filters.types && Array.isArray(filters.types)) {
      documentTypes.value = filters.types
    }
  } catch (error) {
    console.error('Failed to load document types:', error)
  }
}

const validateForm = (): boolean => {
  let isValid = true

  errors.title = ''
  errors.language = ''
  errors.publicationYear = ''

  if (!form.title.trim()) {
    errors.title = 'Judul dokumen harus diisi'
    isValid = false
  }

  if (!form.language) {
    errors.language = 'Bahasa harus dipilih'
    isValid = false
  }

  if (!form.publicationYear || form.publicationYear < 1900 || form.publicationYear > new Date().getFullYear()) {
    errors.publicationYear = 'Tahun terbit tidak valid'
    isValid = false
  }

  return isValid
}

const handleSubmit = async () => {
  if (!validateForm()) {
    toast.error('Validasi Gagal', 'Silakan periksa kembali form Anda')
    return
  }

  isSubmitting.value = true

  try {
    const updateData = {
      title: form.title,
      language: form.language,
      year_published: form.publicationYear,
      type_id: form.documentType || null,
      subject_id: form.subject || null,
      abstract_id: form.abstractId || null,
      abstract_en: form.abstractEn || null
    }

    const response = await api.documents.update(props.document.id, updateData) as { success: boolean; message?: string }

    if (response.success) {
      toast.success('Berhasil', 'Dokumen berhasil diperbarui')
      emit('updated')
      emit('close')
    } else {
      throw new Error(response.message || 'Gagal memperbarui dokumen')
    }
  } catch (error) {
    let errorMessage = 'Gagal memperbarui dokumen. Silakan coba lagi.'
    if (error instanceof Error) {
      errorMessage = error.message
    }
    toast.error('Gagal Memperbarui', errorMessage)
    console.error('Error updating document:', error)
  } finally {
    isSubmitting.value = false
  }
}
</script>
