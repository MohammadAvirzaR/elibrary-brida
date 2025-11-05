<template>
  <!-- Modal Overlay -->
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
        @click.self="closeModal"
      >
        <!-- Modal Content -->
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[100vh] max-h-[100vh] overflow-y-auto">
          <!-- Header -->
          <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <button
              @click="closeModal"
              class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition"
            >
              <i-lucide-chevron-left class="w-6 h-6 text-gray-700" />
            </button>
            <h2 class="text-xl font-bold text-gray-900">Advanced Search</h2>
            <div class="w-10"></div> <!-- Spacer -->
          </div>

          <!-- Content -->
          <div class="p-6 space-y-6">
            <!-- Search Input -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Apa yang sedang Anda cari?
              </label>
              <div class="relative">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Hinted search text"
                  class="w-full px-4 py-3 bg-neutral-200  rounded-lg focus:outline-none   focus:border-transparent text-neutral-950 placeholder-neutral-400"
                />
                <i-lucide-search class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-neutral-950" />
              </div>
            </div>

            <!-- License & Access Rights -->
            <div class="grid grid-cols-2 gap-6">
              <!-- Filter Lisensi -->
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-3">
                  Filter Lisensi
                </label>
                <div class="space-y-2">
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      v-model="selectedLicense"
                      type="radio"
                      value="cc"
                      class="w-4 h-4 text-purple-600 focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">CC</span>
                  </label>
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      v-model="selectedLicense"
                      type="radio"
                      value="all-rights-reserved"
                      class="w-4 h-4 text-purple-600 focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">All Rights Reserved</span>
                  </label>
                </div>
              </div>

              <!-- Filter Hak Akses -->
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-3">
                  Filter Hak Akses
                </label>
                <select
                  v-model="selectedAccessRight"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                >
                  <option value="">Pilih Hak Akses</option>
                  <option value="public">Public</option>
                  <option value="restricted">Restricted</option>
                  <option value="private">Private</option>
                </select>
              </div>
            </div>

            <!-- Filters Grid -->
            <div class="grid grid-cols-3 gap-6">
              <!-- Filter Subjek -->
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-3">
                  Filter Subjek
                </label>
                <div class="space-y-2 max-h-64 overflow-y-auto pr-2">
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      value="name"
                      v-model="selectedSubjects"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Name</span>
                  </label>
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      value="ilmu-komputer"
                      v-model="selectedSubjects"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Ilmu Komputer</span>
                  </label>
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      value="matematika"
                      v-model="selectedSubjects"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Matematika</span>
                  </label>
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      value="hukum"
                      v-model="selectedSubjects"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Hukum</span>
                  </label>
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      value="seni"
                      v-model="selectedSubjects"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Seni</span>
                  </label>
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      value="psikologi"
                      v-model="selectedSubjects"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Psikologi</span>
                  </label>
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      value="lainnya"
                      v-model="selectedSubjects"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Lainnya</span>
                  </label>
                </div>
              </div>

              <!-- Filter Tipe -->
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-3">
                  Filter Tipe
                </label>
                <div class="space-y-2 max-h-64 overflow-y-auto pr-2">
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      value="name"
                      v-model="selectedTypes"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Name</span>
                  </label>
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      value="artikel-jurnal"
                      v-model="selectedTypes"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Artikel Jurnal</span>
                  </label>
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      value="skripsi"
                      v-model="selectedTypes"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Skripsi</span>
                  </label>
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      value="tesis"
                      v-model="selectedTypes"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Tesis</span>
                  </label>
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      value="disertasi"
                      v-model="selectedTypes"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Disertasi</span>
                  </label>
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      value="laporan-penelitian"
                      v-model="selectedTypes"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Laporan Penelitian</span>
                  </label>
                </div>
              </div>

              <!-- Filter Tahun -->
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-3">
                  Filter Tahun
                </label>
                <div class="space-y-2 max-h-64 overflow-y-auto pr-2">
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      value="name"
                      v-model="selectedYears"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Name</span>
                  </label>
                  <label
                    v-for="year in years"
                    :key="year"
                    class="flex items-center space-x-3 cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      :value="year.toString()"
                      v-model="selectedYears"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">{{ year }}</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Search Button -->
            <div class="flex justify-center pt-4">
              <button
                @click="performSearch"
                class="px-8 py-3 bg-gray-800 hover:bg-gray-900 text-white font-semibold rounded-lg transition duration-300 ease-in-out transform hover:scale-105 active:scale-95 flex items-center gap-2"
              >
                Search
                <i-lucide-search class="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

interface SearchFilters {
  query: string
  license: string
  accessRight: string
  subjects: string[]
  types: string[]
  years: string[]
}

defineProps<{
  isOpen: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'search', filters: SearchFilters): void
}>()

const router = useRouter()

const searchQuery = ref('')
const selectedLicense = ref('')
const selectedAccessRight = ref('')
const selectedSubjects = ref<string[]>([])
const selectedTypes = ref<string[]>([])
const selectedYears = ref<string[]>([])

const currentYear = new Date().getFullYear()
const years = computed(() => {
  const yearList = []
  for (let i = currentYear; i >= currentYear - 30; i -= 5) {
    yearList.push(i)
  }
  return yearList
})

const closeModal = () => {
  emit('close')
}

const performSearch = () => {
  const filters = {
    query: searchQuery.value,
    license: selectedLicense.value,
    accessRight: selectedAccessRight.value,
    subjects: selectedSubjects.value,
    types: selectedTypes.value,
    years: selectedYears.value,
  }

  emit('search', filters)

  // Navigate to search page with filters
  router.push({
    name: 'search',
    query: {
      q: searchQuery.value,
      license: selectedLicense.value,
      access: selectedAccessRight.value,
      subjects: selectedSubjects.value.join(','),
      types: selectedTypes.value.join(','),
      years: selectedYears.value.join(','),
    }
  })

  closeModal()
}
</script>

<style scoped>
/* Modal transition */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active > div,
.modal-leave-active > div {
  transition: transform 0.3s ease;
}

.modal-enter-from > div,
.modal-leave-to > div {
  transform: scale(0.95);
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}
</style>
