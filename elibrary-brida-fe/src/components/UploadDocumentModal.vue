<template>
  <Teleport to="body">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div
        class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto no-scrollbar"
        @click.stop
      >
        <!-- Header -->
        <div class="sticky top-0 bg-white border-b border-neutral-200 px-6 py-4 rounded-t-2xl">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-2xl font-bold text-neutral-900">Upload Dokumen Baru</h2>
              <p class="text-sm text-neutral-600 mt-1">
                {{ getStepDescription() }}
              </p>
            </div>
            <button
              @click="$emit('close')"
              class="text-neutral-400 hover:text-neutral-600 transition-colors"
            >
              <i-lucide-x class="w-6 h-6" />
            </button>
          </div>

          <!-- Step Progress Indicator -->
          <div class="flex items-center justify-between">
            <div v-for="step in 5" :key="step" class="flex items-center flex-1">
              <div class="flex items-center">
                <div
                  class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-sm transition-colors"
                  :class="currentStep >= step ? 'bg-blue-600 text-white' : 'bg-neutral-200 text-neutral-600'"
                >
                  {{ step }}
                </div>
                <span class="ml-2 text-xs font-medium" :class="currentStep >= step ? 'text-blue-600' : 'text-neutral-500'">
                  {{ getStepName(step) }}
                </span>
              </div>
              <div v-if="step < 5" class="flex-1 h-1 mx-2" :class="currentStep > step ? 'bg-blue-600' : 'bg-neutral-200'"></div>
            </div>
          </div>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleStepSubmit" class="p-6 space-y-6">

          <!-- STEP 1: Metadata -->
          <div v-if="currentStep === 1" class="space-y-5">
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

            <!-- Language & Document Type -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">
                  Bahasa <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.language"
                  class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  :class="{ 'border-red-500': errors.language }"
                >
                  <option value="">Pilih Bahasa</option>
                  <option value="id">Bahasa Indonesia</option>
                  <option value="en">English</option>
                  <option value="other">Lainnya</option>
                </select>
                <p v-if="errors.language" class="text-red-500 text-sm mt-1">{{ errors.language }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">
                  Jenis Dokumen <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.documentType"
                  class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  :class="{ 'border-red-500': errors.documentType }"
                >
                  <option value="">Pilih Jenis</option>
                  <option value="penelitian">Penelitian</option>
                  <option value="laporan">Laporan</option>
                  <option value="artikel">Artikel</option>
                  <option value="jurnal">Jurnal</option>
                  <option value="skripsi">Skripsi/Tesis</option>
                  <option value="buku">Buku</option>
                  <option value="lainnya">Lainnya</option>
                </select>
                <p v-if="errors.documentType" class="text-red-500 text-sm mt-1">{{ errors.documentType }}</p>
              </div>
            </div>

            <!-- Author & Year -->
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
                  Tahun Terbit <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.publicationYear"
                  type="number"
                  min="1900"
                  :max="new Date().getFullYear()"
                  placeholder="2024"
                  class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  :class="{ 'border-red-500': errors.publicationYear }"
                />
                <p v-if="errors.publicationYear" class="text-red-500 text-sm mt-1">{{ errors.publicationYear }}</p>
              </div>
            </div>

            <!-- Keywords & Subject -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">
                  Kata Kunci <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.keywords"
                  type="text"
                  placeholder="Pisahkan dengan koma"
                  class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  :class="{ 'border-red-500': errors.keywords }"
                />
                <p v-if="errors.keywords" class="text-red-500 text-sm mt-1">{{ errors.keywords }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">
                  Subjek <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.subject"
                  type="text"
                  placeholder="Bidang ilmu/topik"
                  class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  :class="{ 'border-red-500': errors.subject }"
                />
                <p v-if="errors.subject" class="text-red-500 text-sm mt-1">{{ errors.subject }}</p>
              </div>
            </div>

            <!-- Optional Fields -->
            <div class="border-t border-neutral-200 pt-4 mt-4">
              <p class="text-sm font-medium text-neutral-700 mb-3">Informasi Opsional</p>

              <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-neutral-600 mb-2">Pembimbing</label>
                    <input
                      v-model="form.advisor"
                      type="text"
                      placeholder="Nama pembimbing"
                      class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-neutral-600 mb-2">Sumber Pendanaan</label>
                    <input
                      v-model="form.funding"
                      type="text"
                      placeholder="Nama program/lembaga"
                      class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-neutral-600 mb-2">Lokasi Penelitian</label>
                  <input
                    v-model="form.researchLocation"
                    type="text"
                    placeholder="Tempat penelitian dilakukan"
                    class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-neutral-600 mb-2">
                    Abstrak/Deskripsi (Maks. 300 kata)
                  </label>
                  <textarea
                    v-model="form.description"
                    rows="4"
                    placeholder="Jelaskan isi dokumen secara singkat"
                    class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                    maxlength="2000"
                  ></textarea>
                  <p class="text-xs text-neutral-500 mt-1">{{ form.description.split(' ').length }} / 300 kata</p>
                </div>
              </div>
            </div>
          </div>

          <!-- STEP 2: Metadata Summary -->
          <div v-if="currentStep === 2" class="space-y-5">
            <div class="bg-neutral-50 rounded-lg p-6 space-y-4">
              <h3 class="text-lg font-bold text-neutral-900 mb-4">Ringkasan Metadata</h3>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <p class="text-sm font-medium text-neutral-600">Judul</p>
                  <p class="text-sm text-neutral-900">{{ form.title }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-neutral-600">Bahasa</p>
                  <p class="text-sm text-neutral-900">{{ formatLanguage(form.language) }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-neutral-600">Penulis</p>
                  <p class="text-sm text-neutral-900">{{ form.author }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-neutral-600">Tahun Terbit</p>
                  <p class="text-sm text-neutral-900">{{ form.publicationYear }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-neutral-600">Jenis Dokumen</p>
                  <p class="text-sm text-neutral-900">{{ formatDocumentType(form.documentType) }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-neutral-600">Subjek</p>
                  <p class="text-sm text-neutral-900">{{ form.subject }}</p>
                </div>
                <div class="col-span-2">
                  <p class="text-sm font-medium text-neutral-600">Kata Kunci</p>
                  <p class="text-sm text-neutral-900">{{ form.keywords }}</p>
                </div>
              </div>

              <div v-if="form.advisor || form.funding || form.researchLocation" class="border-t border-neutral-200 pt-4 mt-4">
                <p class="text-sm font-medium text-neutral-700 mb-3">Informasi Tambahan</p>
                <div class="grid grid-cols-2 gap-4">
                  <div v-if="form.advisor">
                    <p class="text-sm font-medium text-neutral-600">Pembimbing</p>
                    <p class="text-sm text-neutral-900">{{ form.advisor }}</p>
                  </div>
                  <div v-if="form.funding">
                    <p class="text-sm font-medium text-neutral-600">Sumber Pendanaan</p>
                    <p class="text-sm text-neutral-900">{{ form.funding }}</p>
                  </div>
                  <div v-if="form.researchLocation" class="col-span-2">
                    <p class="text-sm font-medium text-neutral-600">Lokasi Penelitian</p>
                    <p class="text-sm text-neutral-900">{{ form.researchLocation }}</p>
                  </div>
                </div>
              </div>

              <div v-if="form.description" class="border-t border-neutral-200 pt-4 mt-4">
                <div class="flex items-center justify-between mb-2">
                  <p class="text-sm font-medium text-neutral-600">Abstrak</p>
                  <button
                    v-if="!form.translatedAbstract"
                    type="button"
                    @click="translateAbstract"
                    :disabled="isTranslating"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1"
                  >
                    <i-lucide-loader-2 v-if="isTranslating" class="w-3 h-3 animate-spin" />
                    <i-lucide-languages v-else class="w-3 h-3" />
                    {{ isTranslating ? 'Menerjemahkan...' : 'Terjemahkan ke English' }}
                  </button>
                </div>
                <p class="text-sm text-neutral-900 whitespace-pre-wrap">{{ form.description }}</p>

                <div v-if="form.translatedAbstract" class="mt-3 pt-3 border-t border-neutral-200">
                  <p class="text-sm font-medium text-neutral-600 mb-1">English Translation</p>
                  <p class="text-sm text-neutral-900 whitespace-pre-wrap">{{ form.translatedAbstract }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- STEP 3: Upload Data -->
          <div v-if="currentStep === 3" class="space-y-5">
            <!-- Main File Upload -->
            <div>
              <label class="block text-sm font-medium text-neutral-700 mb-2">
                File Dokumen Utama (PDF) <span class="text-red-500">*</span>
              </label>
            <div
              @dragover.prevent="isDragging = true"
              @dragleave.prevent="isDragging = false"
              @drop.prevent="handleDrop"
              @click="!form.file && fileInput?.click()"
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
                  @click.stop="fileInput?.click()"
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

            <!-- Attachments Upload -->
            <div>
              <label class="block text-sm font-medium text-neutral-700 mb-2">
                File Lampiran (Opsional)
              </label>
              <input
                ref="attachmentInput"
                type="file"
                accept=".pdf"
                multiple
                @change="handleAttachmentSelect"
                class="hidden"
              />
              <button
                type="button"
                @click="attachmentInput?.click()"
                class="w-full px-4 py-3 border-2 border-dashed border-neutral-300 rounded-lg hover:border-blue-500 transition-colors text-neutral-600 hover:text-blue-600 inline-flex items-center justify-center gap-2"
              >
                <i-lucide-paperclip class="w-4 h-4" />
                Tambah Lampiran (PDF)
              </button>

              <div v-if="form.attachments.length > 0" class="mt-3 space-y-2">
                <div v-for="(file, index) in form.attachments" :key="index" class="flex items-center justify-between bg-neutral-50 p-3 rounded-lg">
                  <div class="flex items-center gap-2">
                    <i-lucide-file class="w-5 h-5 text-neutral-600" />
                    <div>
                      <p class="text-sm font-medium text-neutral-900">{{ file.name }}</p>
                      <p class="text-xs text-neutral-500">{{ formatFileSize(file.size) }}</p>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="removeAttachment(index)"
                    class="text-red-600 hover:text-red-800"
                  >
                    <i-lucide-x class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- STEP 4: License & Declaration (DUMMY - Auto-pass for now) -->
          <div v-if="currentStep === 4" class="space-y-5">
            <div class="bg-blue-50 rounded-lg p-6 space-y-4">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-bold text-neutral-900">Lisensi & Pernyataan</h3>
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">
                  DUMMY MODE
                </span>
              </div>
              <p class="text-sm text-neutral-600 italic">
                ⚠️ Bagian ini saat ini dalam mode dummy. Validasi lisensi akan diaktifkan saat sistem sudah siap produksi.
              </p>

              <!-- COMMENTED OUT: Will be activated when license system is ready -->
              <div class="opacity-50 pointer-events-none space-y-3 border-2 border-dashed border-neutral-300 rounded-lg p-4">
                <p class="text-xs text-neutral-500 font-medium mb-3">Preview: Pernyataan yang akan diaktifkan nanti</p>

                <label class="flex items-start gap-3">
                  <input
                    type="checkbox"
                    disabled
                    checked
                    class="mt-1 w-4 h-4 text-blue-600 border-neutral-300 rounded"
                  />
                  <span class="text-sm text-neutral-600">
                    Saya memberikan izin kepada E-Library BRIDA untuk menyimpan, mengindeks, dan membagikan dokumen ini secara digital kepada pengguna yang berwenang sesuai dengan kebijakan repositori.
                  </span>
                </label>

                <label class="flex items-start gap-3">
                  <input
                    type="checkbox"
                    disabled
                    checked
                    class="mt-1 w-4 h-4 text-blue-600 border-neutral-300 rounded"
                  />
                  <span class="text-sm text-neutral-600">
                    Saya menyatakan bahwa dokumen ini adalah karya asli saya/kami atau saya/kami memiliki hak untuk mengunggahnya, dan tidak melanggar hak cipta pihak lain.
                  </span>
                </label>

                <label class="flex items-start gap-3">
                  <input
                    type="checkbox"
                    disabled
                    checked
                    class="mt-1 w-4 h-4 text-blue-600 border-neutral-300 rounded"
                  />
                  <span class="text-sm text-neutral-600">
                    Saya memahami bahwa dokumen yang diunggah akan melalui proses review oleh admin sebelum dipublikasikan, dan saya bersedia melakukan revisi jika diperlukan.
                  </span>
                </label>
              </div>

              <!-- DUMMY: Auto-accept notice -->
              <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4">
                <div class="flex items-start gap-3">
                  <i-lucide-check-circle class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" />
                  <div>
                    <p class="text-sm font-medium text-green-800">Otomatis Disetujui (Mode Development)</p>
                    <p class="text-xs text-green-700 mt-1">
                      Semua pernyataan lisensi dianggap sudah disetujui. Anda dapat langsung melanjutkan ke tahap upload.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Instructions for activation -->
              <details class="bg-neutral-50 rounded-lg p-4">
                <summary class="text-xs font-medium text-neutral-700 cursor-pointer hover:text-neutral-900">
                  📝 Instruksi Aktivasi (Untuk Developer)
                </summary>
                <div class="mt-3 text-xs text-neutral-600 space-y-2">
                  <p><strong>Untuk mengaktifkan validasi lisensi:</strong></p>
                  <ol class="list-decimal list-inside space-y-1 ml-2">
                    <li>Uncomment bagian form checkbox di atas (hapus opacity-50 & pointer-events-none)</li>
                    <li>Uncomment validasi di fungsi <code class="bg-neutral-200 px-1 rounded">validateStep4()</code></li>
                    <li>Hapus atau comment <code class="bg-neutral-200 px-1 rounded">return true</code> di awal validateStep4()</li>
                    <li>Hapus badge "DUMMY MODE" dan notice "Otomatis Disetujui"</li>
                  </ol>
                </div>
              </details>
            </div>
          </div>

          <!-- STEP 5: Upload Status -->
          <div v-if="currentStep === 5" class="space-y-5">
            <div class="text-center py-8">
              <div v-if="isSubmitting" class="space-y-4">
                <i-lucide-loader-2 class="w-16 h-16 text-blue-600 mx-auto animate-spin" />
                <h3 class="text-xl font-bold text-neutral-900">Mengunggah Dokumen...</h3>
                <p class="text-sm text-neutral-600">Mohon tunggu, proses upload sedang berlangsung</p>
              </div>

              <div v-else class="space-y-4">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                  <i-lucide-check-circle class="w-12 h-12 text-green-600" />
                </div>
                <h3 class="text-xl font-bold text-neutral-900">Upload Berhasil!</h3>
                <p class="text-sm text-neutral-600">Dokumen Anda telah berhasil diunggah dan menunggu persetujuan admin.</p>

                <div class="bg-neutral-50 rounded-lg p-4 max-w-md mx-auto text-left">
                  <p class="text-sm font-medium text-neutral-700 mb-2">Detail Dokumen:</p>
                  <div class="space-y-1 text-xs text-neutral-600">
                    <p><strong>Judul:</strong> {{ form.title }}</p>
                    <p><strong>Penulis:</strong> {{ form.author }}</p>
                    <p><strong>Status:</strong> <span class="text-yellow-600 font-medium">Pending Review</span></p>
                  </div>
                </div>

                <button
                  type="button"
                  @click="$emit('close')"
                  class="mt-6 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                >
                  Tutup
                </button>
              </div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div v-if="currentStep < 5" class="flex items-center justify-between gap-3 pt-4 border-t border-neutral-200">
            <button
              v-if="currentStep > 1"
              type="button"
              @click="previousStep"
              class="px-6 py-2.5 border border-neutral-300 text-neutral-700 rounded-lg hover:bg-neutral-50 transition-colors font-medium inline-flex items-center gap-2"
            >
              <i-lucide-chevron-left class="w-4 h-4" />
              Kembali
            </button>
            <button
              v-else
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
              {{ getButtonText() }}
              <i-lucide-chevron-right v-if="currentStep < 4" class="w-4 h-4" />
              <i-lucide-upload v-else-if="currentStep === 4" class="w-4 h-4" />
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
const attachmentInput = ref<HTMLInputElement>()
const isDragging = ref(false)
const isSubmitting = ref(false)
const currentStep = ref(1)
const isTranslating = ref(false)

const form = reactive({
  // Step 1 - Metadata
  title: '',
  language: '',
  author: '',
  publicationYear: new Date().getFullYear(),
  keywords: '',
  subject: '',
  documentType: '',
  advisor: '',
  funding: '',
  researchLocation: '',
  description: '',
  translatedAbstract: '',

  // Step 3 - Upload Data
  file: null as File | null,
  attachments: [] as File[],

  // Step 4 - License & Declaration
  licenseAgreement: false,
  originalWork: false,
  permissionGranted: false,

  // Legacy fields (keep for backward compatibility)
  category: '',
  year: new Date().getFullYear(),
  publisher: ''
})

const errors = reactive({
  title: '',
  language: '',
  author: '',
  publicationYear: '',
  keywords: '',
  subject: '',
  documentType: '',
  description: '',
  file: '',
  licenseAgreement: '',
  originalWork: '',
  permissionGranted: ''
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

  console.log('=== File Validation Started ===')
  console.log('File details:', {
    name: file.name,
    type: file.type,
    size: file.size,
    lastModified: file.lastModified,
    isFileObject: file instanceof File
  })

  // CRITICAL: Ensure it's a File object
  if (!(file instanceof File)) {
    errors.file = 'File tidak valid'
    console.error('Not a File object:', file)
    toast.error('File Tidak Valid', 'File yang dipilih tidak valid')
    return
  }

  // Check file extension (more reliable than MIME type)
  const fileName = file.name.toLowerCase()
  const allowedExtensions = ['.pdf', '.doc', '.docx']
  const hasValidExtension = allowedExtensions.some(ext => fileName.endsWith(ext))

  // Check file type (MIME type)
  const allowedTypes = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
  ]
  const hasValidType = allowedTypes.includes(file.type) || file.type === ''

  // Accept if either extension OR type is valid (some browsers don't set MIME type correctly)
  if (!hasValidExtension && !hasValidType) {
    errors.file = 'Hanya file PDF, DOC, atau DOCX yang diperbolehkan'
    console.error('Invalid file type:', file.type, 'and extension:', fileName)
    toast.error('Format File Tidak Valid', 'Hanya file PDF, DOC, atau DOCX yang diperbolehkan')
    return
  }

  // Check file size (50MB to match backend validation)
  const maxSize = 50 * 1024 * 1024
  if (file.size > maxSize) {
    errors.file = 'Ukuran file maksimal 50MB'
    const sizeInMB = (file.size / (1024 * 1024)).toFixed(2)
    console.error('File too large:', sizeInMB + 'MB')
    toast.error('File Terlalu Besar', `Ukuran file ${sizeInMB}MB melebihi batas maksimal 50MB`)
    return
  }

  // Validation passed
  form.file = file
  console.log('✓ File validation PASSED:', file.name, `(${formatFileSize(file.size)})`)
  toast.success('File Diterima', `${file.name} (${formatFileSize(file.size)})`)

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

// Wizard helper functions
const getStepName = (step: number) => {
  const names = ['Metadata', 'Ringkasan', 'Upload', 'Lisensi', 'Selesai']
  return names[step - 1]
}

const getStepDescription = () => {
  const descriptions = [
    'Langkah 1 dari 5: Isi informasi metadata dokumen',
    'Langkah 2 dari 5: Verifikasi metadata dokumen',
    'Langkah 3 dari 5: Upload file dokumen',
    'Langkah 4 dari 5: Persetujuan lisensi dan pernyataan',
    'Upload dokumen berhasil'
  ]
  return descriptions[currentStep.value - 1]
}

const getButtonText = () => {
  if (currentStep.value < 4) return 'Lanjut'
  if (currentStep.value === 4) return isSubmitting.value ? 'Mengunggah...' : 'Upload Dokumen'
  return 'Tutup'
}

const formatLanguage = (lang: string) => {
  const map: Record<string, string> = {
    'id': 'Bahasa Indonesia',
    'en': 'English',
    'other': 'Lainnya'
  }
  return map[lang] || lang
}

const formatDocumentType = (type: string) => {
  const map: Record<string, string> = {
    'penelitian': 'Penelitian',
    'laporan': 'Laporan',
    'artikel': 'Artikel',
    'jurnal': 'Jurnal',
    'skripsi': 'Skripsi/Tesis',
    'buku': 'Buku',
    'lainnya': 'Lainnya'
  }
  return map[type] || type
}

// Step validation functions with toast notifications and field focusing
const validateStep1 = () => {
  let isValid = true
  let firstErrorField = ''

  errors.title = ''
  errors.language = ''
  errors.author = ''
  errors.publicationYear = ''
  errors.keywords = ''
  errors.subject = ''
  errors.documentType = ''

  if (!form.title.trim()) {
    errors.title = 'Judul dokumen harus diisi'
    isValid = false
    if (!firstErrorField) firstErrorField = 'title'
  }
  if (!form.language) {
    errors.language = 'Bahasa harus dipilih'
    isValid = false
    if (!firstErrorField) firstErrorField = 'language'
  }
  if (!form.documentType) {
    errors.documentType = 'Jenis dokumen harus dipilih'
    isValid = false
    if (!firstErrorField) firstErrorField = 'documentType'
  }
  if (!form.author.trim()) {
    errors.author = 'Penulis harus diisi'
    isValid = false
    if (!firstErrorField) firstErrorField = 'author'
  }
  if (!form.publicationYear || form.publicationYear < 1900 || form.publicationYear > new Date().getFullYear()) {
    errors.publicationYear = 'Tahun terbit tidak valid'
    isValid = false
    if (!firstErrorField) firstErrorField = 'publicationYear'
  }
  if (!form.keywords.trim()) {
    errors.keywords = 'Kata kunci harus diisi'
    isValid = false
    if (!firstErrorField) firstErrorField = 'keywords'
  }
  if (!form.subject.trim()) {
    errors.subject = 'Subjek harus diisi'
    isValid = false
    if (!firstErrorField) firstErrorField = 'subject'
  }

  if (!isValid && firstErrorField) {
    // Show toast with first error message
    const errorMessage = errors[firstErrorField as keyof typeof errors]
    toast.error('Validasi Gagal', errorMessage)

    // Focus the first invalid field after a short delay to ensure DOM is ready
    setTimeout(() => {
      const fieldElement = document.querySelector(`[placeholder*="${getFieldPlaceholder(firstErrorField)}"]`) as HTMLElement
      if (fieldElement) {
        fieldElement.focus()
        fieldElement.scrollIntoView({ behavior: 'smooth', block: 'center' })
      }
    }, 100)
  }

  return isValid
}

const validateStep3 = () => {
  errors.file = ''

  // Check if file exists
  if (!form.file) {
    errors.file = 'File dokumen harus dipilih'
    toast.error('Validasi Gagal', 'File dokumen utama harus dipilih')

    // Scroll to file upload area
    setTimeout(() => {
      const fileUploadArea = document.querySelector('[accept*=".pdf"]')?.closest('.space-y-5')
      if (fileUploadArea) {
        fileUploadArea.scrollIntoView({ behavior: 'smooth', block: 'start' })
      }
    }, 100)

    return false
  }

  // Validate file is a File object
  if (!(form.file instanceof File)) {
    errors.file = 'File tidak valid. Silakan upload ulang.'
    toast.error('Validasi Gagal', 'File yang dipilih tidak valid. Silakan upload ulang file.')
    console.error('Invalid file object:', form.file)
    form.file = null
    return false
  }

  // Validate file size
  const maxSize = 50 * 1024 * 1024 // 50MB (matching backend validation)
  if (form.file.size > maxSize) {
    errors.file = 'Ukuran file melebihi batas maksimal 50MB'
    toast.error('File Terlalu Besar', `Ukuran file: ${formatFileSize(form.file.size)}. Maksimal: 50MB`)
    return false
  }

  // Validate file type
  const fileName = form.file.name.toLowerCase()
  const allowedExtensions = ['.pdf', '.doc', '.docx']
  const hasValidExtension = allowedExtensions.some(ext => fileName.endsWith(ext))

  if (!hasValidExtension) {
    errors.file = 'Hanya file PDF, DOC, atau DOCX yang diperbolehkan'
    toast.error('Format File Tidak Valid', 'Hanya file PDF, DOC, atau DOCX yang diperbolehkan')
    return false
  }

  console.log('✓ Step 3 validation passed. File ready:', {
    name: form.file.name,
    size: formatFileSize(form.file.size),
    type: form.file.type
  })

  return true
}

const validateStep4 = () => {
  // DUMMY MODE: Auto-pass license validation for now
  // This will be activated when license system is ready for production
  return true

  /* COMMENTED OUT: Uncomment this block when ready to activate license validation

  let isValid = true
  let firstErrorMessage = ''

  errors.licenseAgreement = ''
  errors.originalWork = ''
  errors.permissionGranted = ''

  if (!form.licenseAgreement) {
    errors.licenseAgreement = 'Anda harus menyetujui pernyataan ini'
    isValid = false
    if (!firstErrorMessage) firstErrorMessage = 'Anda harus menyetujui pernyataan lisensi repositori'
  }
  if (!form.originalWork) {
    errors.originalWork = 'Anda harus menyetujui pernyataan ini'
    isValid = false
    if (!firstErrorMessage) firstErrorMessage = 'Anda harus menyatakan bahwa dokumen adalah karya asli'
  }
  if (!form.permissionGranted) {
    errors.permissionGranted = 'Anda harus menyetujui pernyataan ini'
    isValid = false
    if (!firstErrorMessage) firstErrorMessage = 'Anda harus menyetujui proses review dokumen'
  }

  if (!isValid && firstErrorMessage) {
    toast.error('Validasi Gagal', firstErrorMessage)

    // Scroll to first unchecked checkbox
    setTimeout(() => {
      const firstCheckbox = document.querySelector('.bg-blue-50 input[type="checkbox"]:not(:checked)') as HTMLElement
      if (firstCheckbox) {
        firstCheckbox.focus()
        firstCheckbox.scrollIntoView({ behavior: 'smooth', block: 'center' })
      }
    }, 100)
  }

  return isValid
  */
}

// Helper function to get field placeholder for focusing
const getFieldPlaceholder = (fieldName: string): string => {
  const placeholders: Record<string, string> = {
    'title': 'Masukkan judul',
    'language': 'Pilih Bahasa',
    'documentType': 'Pilih Jenis',
    'author': 'Nama penulis',
    'publicationYear': '2024',
    'keywords': 'Pisahkan dengan koma',
    'subject': 'Bidang ilmu'
  }
  return placeholders[fieldName] || ''
}

// Wizard navigation
const handleStepSubmit = async () => {
  if (currentStep.value === 1) {
    if (validateStep1()) {
      currentStep.value = 2
    }
  } else if (currentStep.value === 2) {
    currentStep.value = 3
  } else if (currentStep.value === 3) {
    if (validateStep3()) {
      currentStep.value = 4
    }
  } else if (currentStep.value === 4) {
    if (validateStep4()) {
      await submitForm()
    }
  }
}

const previousStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--
  }
}

const translateAbstract = async () => {
  if (!form.description.trim()) {
    toast.error('Error', 'Tidak ada teks untuk diterjemahkan')
    return
  }

  isTranslating.value = true
  try {
    // Simulate API call for translation
    // In real implementation, use translation API
    await new Promise(resolve => setTimeout(resolve, 1500))
    form.translatedAbstract = `[Translated to English]\n\n${form.description}`
    toast.success('Success', 'Abstrak berhasil diterjemahkan')
  } catch {
    toast.error('Error', 'Gagal menerjemahkan abstrak')
  } finally {
    isTranslating.value = false
  }
}

const handleAttachmentSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files) {
    Array.from(target.files).forEach(file => {
      if (file.name.toLowerCase().endsWith('.pdf') && file.size <= 10 * 1024 * 1024) {
        form.attachments.push(file)
      }
    })
  }
}

const removeAttachment = (index: number) => {
  form.attachments.splice(index, 1)
}

// Final submission
const submitForm = async () => {
  isSubmitting.value = true

  try {
    // CRITICAL: Validate file exists before submitting
    if (!form.file) {
      toast.error('File Required', 'Please upload the main document file')
      isSubmitting.value = false
      return
    }

    // Validate file is actually a File object
    if (!(form.file instanceof File)) {
      console.error('form.file is not a File object:', form.file)
      toast.error('Invalid File', 'The uploaded file is invalid. Please try uploading again.')
      isSubmitting.value = false
      return
    }

    console.log('=== File Validation PASSED ===')
    console.log('File object:', {
      name: form.file.name,
      size: form.file.size,
      type: form.file.type,
      lastModified: form.file.lastModified
    })

    const formData = new FormData()

    // Append main file (ensure it's a File object)
    formData.append('file', form.file, form.file.name)

    // Append attachments
    form.attachments.forEach((file, index) => {
      if (file instanceof File) {
        formData.append(`attachments[${index}]`, file, file.name)
      }
    })

    // Basic metadata
    formData.append('title', form.title)
    formData.append('year_published', form.publicationYear.toString())
    formData.append('keywords', form.keywords || '')
    formData.append('language', form.language || '')

    // Abstract
    formData.append('abstract_id', form.description || '')

    // Research info
    formData.append('funding_program', form.funding || '')
    formData.append('research_location', form.researchLocation || '')

    // REQUIRED: Authors array (backend requires at least one)
    const authorNames = form.author.split(' ')
    const firstName = authorNames[0] || form.author
    const lastName = authorNames.slice(1).join(' ') || ''

    formData.append('authors[0][first_name]', firstName)
    formData.append('authors[0][last_name]', lastName)
    formData.append('authors[0][email]', '')
    formData.append('authors[0][institution]', '')

    // Access right (default to public)
    formData.append('access_right', 'public')

    // Statement agreed (send as string '1' which Laravel accepts as true)
    formData.append('statement_agreed', '1')

    // Supervisor (optional)
    if (form.advisor) {
      formData.append('supervisors[0][name]', form.advisor)
    }

    // Debug: Log all form data
    console.log('=== Submitting Form Data ===')
    console.log('Total fields:', Array.from(formData.entries()).length)
    for (const [key, value] of formData.entries()) {
      if (value instanceof File) {
        console.log(`${key}: [File] ${value.name} (${value.size} bytes)`)
      } else {
        console.log(`${key}:`, value)
      }
    }

    const response = await api.documents.upload(formData) as { success: boolean; data: { id: number; title: string; status: string; created_at: string; [key: string]: unknown } }

    if (response.success && response.data) {
      const newDocument = {
        id: response.data.id,
        title: response.data.title,
        author: form.author,
        category: form.documentType,
        status: response.data.status,
        uploadDate: response.data.created_at,
        description: form.description,
        year: form.publicationYear,
        publisher: form.funding,
        keywords: form.keywords
      }

      currentStep.value = 5

      setTimeout(() => {
        emit('uploaded', newDocument)
        setTimeout(() => {
          emit('close')
        }, 2000)
      }, 1500)
    }
  } catch (error) {
    console.error('Upload failed:', error)

    let errorMessage = 'Terjadi kesalahan saat mengunggah dokumen.'

    if (error instanceof Error) {
      // Parse error message for better display
      if (error.message.includes('403') || error.message.includes('Forbidden')) {
        errorMessage = 'Anda tidak memiliki izin untuk upload dokumen. Pastikan Anda login sebagai Contributor.'
      } else if (error.message.includes('401') || error.message.includes('Unauthorized')) {
        errorMessage = 'Sesi Anda telah berakhir. Silakan login kembali.'
        setTimeout(() => {
          localStorage.removeItem('auth_token')
          localStorage.removeItem('user')
          window.location.href = '/login'
        }, 2000)
      } else if (error.message.includes('422') || error.message.includes('Unprocessable')) {
        // Try to extract validation errors from message
        const detailsMatch = error.message.match(/Details:\n(.+)/s)
        if (detailsMatch) {
          errorMessage = 'Validasi gagal:\n' + detailsMatch[1]
        } else {
          errorMessage = 'Data tidak valid. Pastikan semua field wajib sudah diisi dengan benar.'
        }
      } else if (error.message.includes('413') || error.message.includes('too large')) {
        errorMessage = 'Ukuran file terlalu besar. Maksimal 50MB untuk file utama dan 20MB untuk lampiran.'
      } else if (error.message.includes('file')) {
        errorMessage = 'File tidak valid. Pastikan file adalah PDF, DOC, atau DOCX.'
      } else {
        errorMessage = error.message
      }
    }

    toast.error('Gagal Mengunggah', errorMessage)
  } finally {
    isSubmitting.value = false
  }
}
</script>
