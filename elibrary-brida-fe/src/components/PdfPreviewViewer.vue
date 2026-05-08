<template>
  <div class="space-y-4">
    <div v-if="loading" class="flex items-center justify-center h-96 bg-gray-100 rounded-lg">
      <div class="text-center">
        <i-lucide-loader-2 class="w-10 h-10 text-blue-600 animate-spin mx-auto mb-3" />
        <p class="text-sm text-gray-600 font-medium">Memuat PDF preview...</p>
      </div>
    </div>

    <div v-else-if="errorMessage" class="flex items-center justify-center h-96 bg-gray-100 rounded-lg">
      <div class="text-center px-6">
        <i-lucide-file-warning class="w-10 h-10 text-amber-600 mx-auto mb-3" />
        <p class="text-sm text-gray-700 font-medium">{{ errorMessage }}</p>
      </div>
    </div>

    <div v-else class="space-y-4">
      <!-- <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-800">
        Preview terbatas halaman awal dokumen. Tombol download/print dinonaktifkan pada mode preview.
      </div> -->
      <div v-if="iframeSrc" class="h-[760px] rounded-lg border border-gray-200 bg-white overflow-hidden">
        <object
          :data="iframeSrc"
          type="application/pdf"
          class="w-full h-full bg-white"
        >
          <iframe
            :src="iframeSrc"
            class="w-full h-full bg-white"
            title="PDF Preview"
          />
        </object>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'

const props = defineProps<{
  documentId: number
}>()

const loading = ref(false)
const errorMessage = ref('')
const previewUrl = ref('')
const iframeSrc = ref('')

const getPreviewEndpoint = (documentId: number): string => {
  const apiBaseUrl = (import.meta as unknown as { env: { VITE_API_BASE_URL?: string } }).env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api'
  return `${apiBaseUrl}/content/${documentId}/preview`
}

const revokePreviewUrl = () => {
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value)
    previewUrl.value = ''
  }
  iframeSrc.value = ''
}

const loadPreviewPdf = async () => {
  loading.value = true
  errorMessage.value = ''
  revokePreviewUrl()

  try {
    const token = localStorage.getItem('auth_token')
    const headers: Record<string, string> = { Accept: 'application/pdf' }
    if (token) {
      headers.Authorization = `Bearer ${token}`
    }

    const response = await fetch(getPreviewEndpoint(props.documentId), { headers })
    if (!response.ok) {
      const payload = await response.json().catch(() => ({})) as { message?: string }
      throw new Error(payload.message || 'Preview tidak tersedia untuk dokumen ini.')
    }

    const blob = await response.blob()
    if (!blob.type.includes('pdf')) {
      throw new Error('Respons preview bukan file PDF.')
    }
    previewUrl.value = URL.createObjectURL(blob)
    iframeSrc.value = `${previewUrl.value}#toolbar=0&navpanes=0&scrollbar=0&view=FitH`
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Gagal memuat PDF preview.'
  } finally {
    loading.value = false
  }
}

onMounted(loadPreviewPdf)
onBeforeUnmount(revokePreviewUrl)

watch(() => props.documentId, loadPreviewPdf)
</script>
