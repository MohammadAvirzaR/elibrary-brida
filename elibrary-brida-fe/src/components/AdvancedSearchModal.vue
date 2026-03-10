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
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto no-scrollbar">
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

                  @keyup.enter="scrollToCatalog"
                  placeholder="Hinted search text"
                  class="w-full px-4 py-3 bg-white rounded-lg focus:outline-none   focus:border-transparent text-neutral-950 placeholder-neutral-400 shadow-sm border border-neutral-200  focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                />
                <i-lucide-search class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-neutral-950" />
              </div>
            </div>

            <!-- License & Access Rights (Commented out - not ready yet) -->
            <!-- <div class="grid grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-3">
                  Filter Lisensi
                </label>
                <div class="space-y-2">
                  <label
                    v-for="license in licenses"
                    :key="license.value"
                    class="flex items-center space-x-3 cursor-pointer"
                  >
                    <input
                      v-model="selectedLicense"
                      type="radio"
                      :value="license.value"
                      class="w-4 h-4 text-purple-600 focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">{{ license.label }}</span>
                  </label>
                </div>
              </div>

              <div>
                <label class="block text-sm font-bold text-gray-900 mb-3">
                  Filter Hak Akses
                </label>
                <select
                  v-model="selectedAccessRight"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                >
                  <option value="">Pilih Hak Akses</option>
                  <option
                    v-for="accessRight in accessRights"
                    :key="accessRight"
                    :value="accessRight"
                  >
                    {{ accessRight.charAt(0).toUpperCase() + accessRight.slice(1) }}
                  </option>
                </select>
              </div>
            </div> -->


            <!-- Filters Grid -->
            <div class="grid grid-cols-3 gap-6">
              <!-- Filter Subjek -->
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-3">
                  Filter Subjek
                </label>
                <div class="space-y-2 max-h-64  pr-2">
                  <label
                    v-for="subject in subjects"
                    :key="subject.value"
                    class="flex items-center space-x-3 cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      :value="subject.value"
                      v-model="selectedSubjects"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">{{ subject.label }}</span>
                  </label>
                </div>
              </div>

              <!-- Filter Tipe -->
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-3">
                  Filter Tipe
                </label>
                <div class="space-y-2 max-h-64 overflow-y-auto pr-2">
                  <label
                    v-for="type in documentTypes"
                    :key="type.value"
                    class="flex items-center space-x-3 cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      :value="type.value"
                      v-model="selectedTypes"
                      class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">{{ type.label }}</span>
                  </label>
                </div>
              </div>

              <!-- Filter Tahun -->
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-3">
                  Filter Tahun
                </label>
                <div class="space-y-2 max-h-64 overflow-y-auto pr-2">
                  <label
                    v-for="year in years"
                    :key="year"
                    class="flex items-center space-x-3 cursor-pointer"
                  >
                    <input
                      type="radio"
                      :value="year"
                      v-model="selectedYear"
                      class="w-4 h-4 text-purple-600 focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-700">Last {{ year }} years</span>
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

interface SearchFilters {
  query: string
  license: string
  accessRight: string
  subjects: string[]
  types: string[]
  year: number | null
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
const setSearchQuery = (query: string) => {
  searchQuery.value = query
}
const selectedLicense = ref('')
const selectedAccessRight = ref('')
const selectedSubjects = ref<string[]>([])
const selectedTypes = ref<string[]>([])
const selectedYear = ref<number | null>(null)

// Dynamic filters from API
const subjects = ref<Array<{ label: string; value: string; id: number }>>([])
const documentTypes = ref<Array<{ label: string; value: string; id: number }>>([])
const licenses = ref<Array<{ label: string; value: string; id: number }>>([])
const accessRights = ref<string[]>([])
const years = ref<number[]>([])

// Maps to store ID lookups
const subjectMap = ref<Map<string, number>>(new Map())
const typeMap = ref<Map<string, number>>(new Map())
const licenseMap = ref<Map<string, number>>(new Map())

const localSearch = ref(searchQuery.value)

// enter to search
const scrollToCatalog = async () => {
  if (localSearch.value.trim()) {
    setSearchQuery(localSearch.value)
    router.push({
      name: 'search',
      query: {
        q: localSearch.value,
        page: 1
      }
    })
    return
  }

  const catalogElement = document.getElementById('catalog')
  if (catalogElement) {
    catalogElement.scrollIntoView({ behavior: 'smooth' })
  }
}

const closeModal = () => {
  emit('close')
}

const performSearch = () => {
  // Get selected subject IDs
  const subjectIds = selectedSubjects.value
    .map(name => subjectMap.value.get(name))
    .filter((id): id is number => id !== undefined)

  // Get selected type IDs
  const typeIds = selectedTypes.value
    .map(name => typeMap.value.get(name))
    .filter((id): id is number => id !== undefined)

  // Get selected license ID (if license filter is enabled) - Not ready yet
  // const licenseId = selectedLicense.value
  //   ? licenseMap.value.get(selectedLicense.value)
  //   : undefined

  const filters = {
    query: searchQuery.value,
    license: selectedLicense.value,
    accessRight: selectedAccessRight.value,
    subjects: selectedSubjects.value,
    types: selectedTypes.value,
    year: selectedYear.value,
  }

  emit('search', filters)

  // Navigate to catalog page with filter IDs
  const queryParams: Record<string, string> = {}

  if (searchQuery.value.trim()) {
    queryParams.q = searchQuery.value
  }

  if (subjectIds.length > 0) {
    queryParams.subject_id = subjectIds.join(',')
  }

  if (typeIds.length > 0) {
    queryParams.type_id = typeIds.join(',')
  }

  // Commented out - not ready yet
  // if (licenseId) {
  //   queryParams.license_id = licenseId.toString()
  // }

  // if (selectedAccessRight.value) {
  //   queryParams.access_right = selectedAccessRight.value
  // }

  if (selectedYear.value) {
    queryParams.year = selectedYear.value.toString()
  }

  router.push({
    name: 'catalog',
    query: queryParams
  })

  closeModal()
}

// Load filters on mount
onMounted(async () => {
  try {
    const response = await api.filters.getAll() as {
      subjects: Array<{ id: number; subject_name: string }>
      types: Array<{ id: number; type_name: string }>
      licenses: Array<{ id: number; license_name: string }>
      access_rights: string[]
      years: number[]
    }

    // Map subjects to label-value format and create ID map
    subjects.value = response.subjects?.map(s => {
      subjectMap.value.set(s.subject_name, s.id)
      return {
        id: s.id,
        label: s.subject_name,
        value: s.subject_name
      }
    }) || []

    // Map document types to label-value format and create ID map
    documentTypes.value = response.types?.map(t => {
      typeMap.value.set(t.type_name, t.id)
      return {
        id: t.id,
        label: t.type_name,
        value: t.type_name
      }
    }) || []

    // Map licenses to label-value format and create ID map
    licenses.value = response.licenses?.map(l => {
      licenseMap.value.set(l.license_name, l.id)
      return {
        id: l.id,
        label: l.license_name,
        value: l.license_name
      }
    }) || []

    // Set access rights from API
    accessRights.value = response.access_rights || []

    // Set years from API
    years.value = response.years || []
  } catch (error) {
    console.error('Failed to load filters:', error)
  }
})
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
</style>
