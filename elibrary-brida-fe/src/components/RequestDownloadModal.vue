<template>
  <Teleport to="body">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4">
      <div class="bg-white rounded-lg sm:rounded-xl shadow-2xl max-w-lg w-full max-h-[92vh] overflow-y-auto" @click.stop>

        <!-- Header -->
        <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 border-b border-neutral-200">
          <div>
            <h2 class="text-base sm:text-lg font-bold text-neutral-900">Permohonan Unduh Dokumen</h2>
            <p class="text-xs sm:text-sm text-neutral-500 mt-0.5 truncate max-w-[200px] sm:max-w-xs">{{ documentTitle }}</p>
          </div>
          <button @click="$emit('close')" class="text-neutral-400 hover:text-neutral-600 transition p-1">
            <i-lucide-x class="w-5 h-5" />
          </button>
        </div>

        <!-- Success State -->
        <div v-if="submitted" class="p-6 sm:p-8 text-center space-y-4">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
            <i-lucide-check-circle class="w-10 h-10 text-green-600" />
          </div>
          <h3 class="text-lg font-bold text-neutral-900">Permohonan Terkirim!</h3>
          <p class="text-sm text-neutral-600">
            Permintaan Anda telah diterima. Admin akan mengirimkan dokumen ke email
            <strong>{{ form.email }}</strong> setelah diverifikasi.
          </p>
          <button
            @click="$emit('close')"
            class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
          >
            Tutup
          </button>
        </div>

        <!-- Form -->
        <form v-else @submit.prevent="submitRequest" class="p-4 sm:p-6 space-y-4">
          <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
            <i-lucide-info class="w-4 h-4 inline mr-1.5 -mt-0.5" />
            Dokumen full text akan dikirimkan ke email Anda setelah admin memverifikasi permohonan ini.
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-neutral-700 mb-1.5">
                Nama Lengkap <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Nama Anda"
                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="errors.name ? 'border-red-400' : 'border-neutral-300'"
              />
              <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-neutral-700 mb-1.5">
                Email <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.email"
                type="email"
                placeholder="email@contoh.com"
                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="errors.email ? 'border-red-400' : 'border-neutral-300'"
              />
              <p v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email }}</p>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-neutral-700 mb-1.5">Institusi / Afiliasi</label>
            <input
              v-model="form.institution"
              type="text"
              placeholder="Universitas / Lembaga"
              class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-neutral-700 mb-1.5">Tujuan Penggunaan</label>
            <textarea
              v-model="form.purpose"
              rows="3"
              placeholder="Jelaskan tujuan penggunaan dokumen ini (opsional)"
              class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
            />
          </div>

          <!-- Agreement -->
          <div class="border border-neutral-200 rounded-lg p-4 bg-neutral-50">
            <label class="flex items-start gap-3 cursor-pointer">
              <input
                v-model="form.agreed_to_terms"
                type="checkbox"
                class="mt-0.5 w-4 h-4 text-blue-600 border-neutral-300 rounded flex-shrink-0"
              />
              <span class="text-sm text-neutral-600">
                Saya menyatakan bahwa saya <strong>tidak akan menyalahgunakan</strong> dokumen ini dan
                akan menggunakannya sesuai dengan aturan hukum yang berlaku di Indonesia, termasuk
                Undang-Undang Hak Cipta dan peraturan terkait karya ilmiah.
              </span>
            </label>
            <p v-if="errors.agreed_to_terms" class="mt-1.5 text-xs text-red-500 ml-7">{{ errors.agreed_to_terms }}</p>
          </div>

          <div class="flex flex-col-reverse sm:flex-row gap-3 pt-1">
            <button
              type="button"
              @click="$emit('close')"
              class="flex-1 px-4 py-2.5 border border-neutral-300 text-neutral-700 rounded-lg hover:bg-neutral-50 transition text-sm font-medium"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium disabled:opacity-50 flex items-center justify-center gap-2"
            >
              <i-lucide-loader-2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
              <i-lucide-send v-else class="w-4 h-4" />
              {{ isSubmitting ? 'Mengirim...' : 'Kirim Permohonan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue'
import api from '@/services/api'
import { useToast } from '@/composables/useToast'

const props = defineProps<{
  documentId: number
  documentTitle: string
}>()

const emit = defineEmits(['close'])
const { toast } = useToast()

const isSubmitting = ref(false)
const submitted = ref(false)

const form = reactive({
  name: '',
  email: '',
  institution: '',
  purpose: '',
  agreed_to_terms: false,
})

const errors = reactive({
  name: '',
  email: '',
  agreed_to_terms: '',
})

onMounted(() => {
  try {
    const userJson = localStorage.getItem('user')
    if (userJson) {
      const user = JSON.parse(userJson)
      form.name = user.name || ''
      form.email = user.email || ''
    }
  } catch {}
})

const submitRequest = async () => {
  errors.name = ''
  errors.email = ''
  errors.agreed_to_terms = ''

  let valid = true
  if (!form.name.trim()) { errors.name = 'Nama harus diisi'; valid = false }
  if (!form.email.trim()) { errors.email = 'Email harus diisi'; valid = false }
  if (!form.agreed_to_terms) { errors.agreed_to_terms = 'Anda harus menyetujui pernyataan ini'; valid = false }
  if (!valid) return

  isSubmitting.value = true
  try {
    await api.downloadRequests.submit({
      document_id: props.documentId,
      name: form.name.trim(),
      email: form.email.trim(),
      institution: form.institution.trim() || undefined,
      purpose: form.purpose.trim() || undefined,
      agreed_to_terms: true,
    })
    submitted.value = true
  } catch (err) {
    toast.error('Gagal Mengirim', 'Terjadi kesalahan. Coba lagi.')
  } finally {
    isSubmitting.value = false
  }
}
</script>
