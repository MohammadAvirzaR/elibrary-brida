<template>
  <div>
    <NavigationBar />
    <div class="container mx-auto px-6 py-8 min-h-screen pt-24">
      <!-- Header Section -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Katalog Dokumen</h1>

        <!-- Search Bar -->
        <div class="relative max-w-2xl">
         <i-lucide-search
            v-if="!isSearching && !isLoading"
            class="absolute left-3 sm:left-4 top-2 sm:top-3 w-4 h-4 sm:w-5 sm:h-5 text-gray-400"
          />
          <i-lucide-loader-2
            v-if="isSearching || isLoading"
            class="absolute left-3 sm:left-4 top-2 sm:top-3 w-4 h-4 sm:w-5 sm:h-5 text-blue-500 animate-spin"
          />
          <input
            type="text"
            v-model="localSearchQuery"
            @input="handleSearch"
            @keyup.enter="performSearch"
            placeholder="Cari buku digital..."
            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>

        <!-- Search Info -->
        <div v-if="totalResults > 0" class="mt-4">
          <p class="text-gray-600">
            Menampilkan
            <span class="font-semibold">{{ totalResults }}</span>
            dokumen<span v-if="searchQuery"> untuk
            "<span class="font-semibold text-blue-600">{{ searchQuery }}</span>"</span>
          </p>
        </div>
      </div>

      <!-- Sidebar and Results -->
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar - Filters -->
        <aside class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
            <h3 class="font-bold text-gray-900 mb-4">Subjek</h3>
            <div class="space-y-2 mb-6">
              <label
                v-for="subject in subjects"
                :key="subject.value"
                class="flex items-center space-x-2 cursor-pointer"
              >
                <input
                  type="checkbox"
                  :value="subject.value"
                  v-model="selectedSubjects"
                  @change="applyFilters"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span class="text-sm text-gray-700">{{ subject.label }}</span>
              </label>
            </div>

            <h3 class="font-bold text-gray-900 mb-4">Tipe</h3>
            <div class="space-y-2">
              <label
                v-for="type in documentTypes"
                :key="type.value"
                class="flex items-center space-x-2 cursor-pointer"
              >
                <input
                  type="checkbox"
                  :value="type.value"
                  v-model="selectedTypes"
                  @change="applyFilters"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span class="text-sm text-gray-700">{{ type.label }}</span>
              </label>
            </div>

            <!-- License Filter (Commented out - not ready yet) -->
            <!-- <h3 class="font-bold text-gray-900 mb-4 mt-6">Lisensi</h3>
            <div class="space-y-2">
              <label
                v-for="license in licenses"
                :key="license.value"
                class="flex items-center space-x-2 cursor-pointer"
              >
                <input
                  type="radio"
                  :value="license.value"
                  v-model="selectedLicense"
                  @change="applyFilters"
                  class="border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span class="text-sm text-gray-700">{{ license.label }}</span>
              </label>
            </div> -->

            <!-- Access Right Filter (Commented out - not ready yet) -->
            <!-- <h3 class="font-bold text-gray-900 mb-4 mt-6">Hak Akses</h3>
            <select
              v-model="selectedAccessRight"
              @change="applyFilters"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
            >
              <option value="">Semua</option>
              <option
                v-for="accessRight in accessRights"
                :key="accessRight"
                :value="accessRight"
              >
                {{ accessRight.charAt(0).toUpperCase() + accessRight.slice(1) }}
              </option>
            </select> -->

            <!-- Year Filter -->
            <h3 class="font-bold text-gray-900 mb-4 mt-6">Tahun</h3>
            <div class="space-y-2">
              <label class="flex items-center space-x-2 cursor-pointer">
                <input
                  type="radio"
                  value=""
                  v-model="selectedYear"
                  @change="applyFilters"
                  class="border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span class="text-sm text-gray-700">Semua</span>
              </label>
              <label
                v-for="year in years"
                :key="year"
                class="flex items-center space-x-2 cursor-pointer"
              >
                <input
                  type="radio"
                  :value="year"
                  v-model="selectedYear"
                  @change="applyFilters"
                  class="border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span class="text-sm text-gray-700">Last {{ year }} years</span>
              </label>
            </div>
          </div>
        </aside>

        <!-- Results Section -->
        <main class="lg:col-span-3">
          <!-- Loading State -->
          <div v-if="isLoading" class="flex justify-center items-center py-20">
            <div class="text-center">
              <i-lucide-loader-2 class="w-16 h-16 text-blue-600 mx-auto mb-4 animate-spin" />
              <p class="text-gray-600 font-medium">Memuat katalog...</p>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else-if="!isLoading && searchResults.length === 0" class="text-center py-20">
            <i-lucide-search class="w-20 h-20 text-gray-300 mx-auto mb-4" />
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak ada dokumen ditemukan</h3>
            <p class="text-gray-500">Coba gunakan kata kunci yang berbeda atau ubah filter pencarian</p>
          </div>

          <!-- Results Grid -->
          <div v-else class="space-y-6">
            <div
              v-for="(document, index) in searchResults"
              :key="document.id"
              class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-6"
            >
              <div class="flex gap-6">
                <!-- Document Number -->
                <div class="flex-shrink-0 w-12">
                  <span class="text-2xl font-bold text-gray-400">{{ (currentPage - 1) * 10 + index + 1 }}</span>
                </div>

                <!-- Document Cover -->
                <!-- <div class="flex-shrink-0 w-32">
                  <img
                    :src="document.cover_image || 'https://via.placeholder.com/128x180?text=No+Cover'"
                    :alt="document.title"
                    class="w-full h-48 object-cover rounded-lg shadow"
                  />
                </div> -->

                <!-- Document Info -->
                <div class="flex-1">
                  <h3 class="text-xl font-semibold text-blue-600 hover:text-blue-700 mb-2 cursor-pointer">
                    {{ document.title }}
                  </h3>
                  <p class="text-sm text-gray-600 mb-2">
                    by {{ getAuthorsDisplay(document) }}
                  </p>
                  <p class="text-sm text-gray-500 mb-3">
                    {{ document.subject?.subject_name || 'General' }} • {{ formatDate(document.year_published) }}
                  </p>
                  <p class="text-gray-700 text-sm line-clamp-3 mb-4">
                    {{ document.abstract_id || 'No description available.' }}
                  </p>

                  <!-- Tags -->
                  <div class="flex gap-2 mb-4">
                    <span
                      v-if="document.type"
                      class="inline-block px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full"
                    >
                      {{ document.type.type_name }}
                    </span>
                  </div>

                  <!-- Action Buttons -->
                  <div class="flex gap-3">
                    <router-link
                      v-if="isAuthenticated"
                      :to="{ name: 'document-detail', params: { id: document.id } }"
                      class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2"
                    >
                      <i-lucide-eye class="w-4 h-4" />
                      Preview
                    </router-link>
                    <router-link
                      v-else
                      to="/login"
                      class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2"
                    >
                      <i-lucide-eye class="w-4 h-4" />
                      Preview
                    </router-link>
                    <button
                      @click="openDownloadModal(document.id, document.title)"
                      class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2">
                      <i-lucide-download class="w-4 h-4" />
                      Download
                    </button>
                  </div>
                </div>

                <!-- Action Icons -->
                <div class="flex-shrink-0 flex flex-col gap-3">
                  <button class="p-2 hover:bg-gray-100 rounded-lg transition" title="Bookmark">
                    <i-lucide-bookmark class="w-5 h-5 text-gray-600" />
                  </button>
                  <button class="p-2 hover:bg-gray-100 rounded-lg transition" title="Share">
                    <i-lucide-link class="w-5 h-5 text-gray-600" />
                  </button>
                  <button class="p-2 hover:bg-gray-100 rounded-lg transition" title="Citations">
                    <i-lucide-quote class="w-5 h-5 text-gray-600" />
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="searchResults.length > 0" class="mt-8 flex justify-center">
            <nav class="flex gap-2">
              <button
                @click="prevPage"
                :disabled="currentPage === 1"
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Previous
              </button>
              <button
                v-for="page in displayPages"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'px-4 py-2 border rounded-lg',
                  currentPage === page
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'border-gray-300 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
              <button
                @click="nextPage"
                :disabled="currentPage === totalPages"
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Next
              </button>
            </nav>
          </div>
        </main>
      </div>
    </div>
    <FooterSection />
  </div>

  <RequestDownloadModal
    v-if="showDownloadModal && selectedDoc"
    :document-id="selectedDoc.id"
    :document-title="selectedDoc.title"
    @close="showDownloadModal = false"
  />
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useDocumentSearch } from '@/composables/useDocumentSearch'
import { useDebounceFn } from '@vueuse/core'
import NavigationBar from '@/components/NavigationBar.vue'
import FooterSection from '@/components/FooterSection.vue'
import RequestDownloadModal from '@/components/RequestDownloadModal.vue'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const { searchDocuments, searchResults, isLoading, totalResults, lastPage } = useDocumentSearch()

const localSearchQuery = ref('')
const searchQuery = ref('')
const isSearching = ref(false)
const currentPage = ref(1)
const selectedSubjects = ref<string[]>([])
const selectedTypes = ref<string[]>([])
// const selectedLicense = ref('')           // Not ready yet
// const selectedAccessRight = ref('')       // Not ready yet
const selectedYear = ref<number | string>('')

// Flag to prevent watch triggering on internal URL updates
const isInternalUpdate = ref(false)

const showDownloadModal = ref(false)
const selectedDoc = ref<{ id: number; title: string } | null>(null)

const openDownloadModal = (id: number, title: string) => {
  selectedDoc.value = { id, title }
  showDownloadModal.value = true
}

// Maps to store ID lookups
const subjectMap = ref<Map<string, number>>(new Map())
const typeMap = ref<Map<string, number>>(new Map())
const licenseMap = ref<Map<string, number>>(new Map())

// Check if user is authenticated
const isAuthenticated = computed(() => {
  return !!localStorage.getItem('auth_token')
});

// Dynamic filters from API
const subjects = ref<Array<{ label: string; value: string }>>([])
const documentTypes = ref<Array<{ label: string; value: string }>>([])
const licenses = ref<Array<{ label: string; value: string }>>([])
const accessRights = ref<string[]>([])
const years = ref<number[]>([])

const totalPages = computed(() => lastPage.value || Math.ceil(totalResults.value / 10))

const displayPages = computed(() => {
  const pages = []
  const maxPages = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxPages / 2))
  const end = Math.min(totalPages.value, start + maxPages - 1)

  if (end - start < maxPages - 1) {
    start = Math.max(1, end - maxPages + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

const debouncedSearch = useDebounceFn(async () => {
  searchQuery.value = localSearchQuery.value
  await performSearch()
}, 500)

const handleSearch = () => {
  isSearching.value = true
  debouncedSearch()
}

const performSearch = async () => {
  // Don't set isSearching here, it's already set by handleSearch or will be set by watch
  try {
    searchQuery.value = localSearchQuery.value

    // Get selected subject IDs
    const subjectIds = selectedSubjects.value
      .map(name => subjectMap.value.get(name))
      .filter((id): id is number => id !== undefined)

    // Get selected type IDs
    const typeIds = selectedTypes.value
      .map(name => typeMap.value.get(name))
      .filter((id): id is number => id !== undefined)

    // Get selected license ID (if enabled) - Not ready yet
    // const licenseId = selectedLicense.value
    //   ? licenseMap.value.get(selectedLicense.value)
    //   : undefined

    // Parse year as number if it's not empty
    const yearFilter = selectedYear.value && selectedYear.value !== ''
      ? (typeof selectedYear.value === 'number' ? selectedYear.value : parseInt(selectedYear.value))
      : undefined

    await searchDocuments(
      searchQuery.value,
      currentPage.value,
      undefined,
      subjectIds.length > 0 ? subjectIds : undefined,
      typeIds.length > 0 ? typeIds : undefined,
      yearFilter
      // licenseId,         // Not ready yet
      // selectedAccessRight.value || undefined  // Not ready yet
    )

    // Update URL query parameters with filters
    const queryParams: Record<string, string | number> = {}

    if (searchQuery.value.trim()) {
      queryParams.q = searchQuery.value
    }

    if (currentPage.value > 1) {
      queryParams.page = currentPage.value
    }

    if (subjectIds.length > 0) {
      queryParams.subject_id = subjectIds.join(',')
    }

    if (typeIds.length > 0) {
      queryParams.type_id = typeIds.join(',')
    }

    if (yearFilter) {
      queryParams.year = yearFilter
    }

    // Commented out - not ready yet
    // if (licenseId) {
    //   queryParams.license_id = licenseId
    // }

    // if (selectedAccessRight.value) {
    //   queryParams.access_right = selectedAccessRight.value
    // }

    // Set flag to prevent watch from triggering
    isInternalUpdate.value = true

    await router.push({
      query: queryParams
    })

    // Reset flag after navigation
    await nextTick()
    isInternalUpdate.value = false
  } finally {
    isSearching.value = false
  }
}

const applyFilters = async () => {
  console.log('Applying filters:', {
    selectedSubjects: selectedSubjects.value,
    selectedTypes: selectedTypes.value
  })

  // Reset to page 1 when applying filters
  currentPage.value = 1
  isSearching.value = true
  await performSearch()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const formatDate = (year?: number | string) => {
  if (!year) return 'N/A'
  if (typeof year === 'number') return year.toString()
  return new Date(year).getFullYear().toString()
}

interface Author {
  first_name: string
  last_name: string
}

interface DocumentWithAuthors {
  author?: string
  authors?: Author[]
}

const getAuthorsDisplay = (document: DocumentWithAuthors) => {
  if (document.authors && Array.isArray(document.authors) && document.authors.length > 0) {
    const authorNames = document.authors.map((author: Author) => {
      const firstName = author.first_name || ''
      const lastName = author.last_name || ''
      return `${firstName} ${lastName}`.trim()
    }).filter((name: string) => name.length > 0)

    if (authorNames.length === 0) return 'Unknown Author'
    if (authorNames.length === 1) return authorNames[0]
    if (authorNames.length === 2) return `${authorNames[0]} and ${authorNames[1]}`
    return `${authorNames[0]} et al.`
  }
  return document.author || 'Unknown Author'
}

const prevPage = async () => {
  if (currentPage.value > 1) {
    currentPage.value--
    isSearching.value = true
    await performSearch()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const nextPage = async () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    isSearching.value = true
    await performSearch()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const goToPage = async (page: number) => {
  currentPage.value = page
  isSearching.value = true
  await performSearch()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Initialize - load all documents and filters
onMounted(async () => {
  const query = route.query.q as string
  const page = parseInt(route.query.page as string) || 1

  if (query) {
    localSearchQuery.value = query
    searchQuery.value = query
  }

  currentPage.value = page

  // Fetch filters from API first
  try {
    const response = await api.filters.getAll() as {
      subjects: Array<{ id: number; subject_name: string }>
      types: Array<{ id: number; type_name: string }>
      licenses: Array<{ id: number; license_name: string }>
      access_rights: string[]
      years: number[]
    }

    console.log('API Response:', response)

    // Create reverse maps (ID -> name) for advanced search params
    const idToSubjectName = new Map<number, string>()
    const idToTypeName = new Map<number, string>()
    const idToLicenseName = new Map<number, string>()

    // Map subjects to label-value format and create ID maps
    subjects.value = response.subjects?.map(s => {
      subjectMap.value.set(s.subject_name, s.id)
      idToSubjectName.set(s.id, s.subject_name)
      return {
        label: s.subject_name,
        value: s.subject_name
      }
    }) || []

    // Map document types to label-value format and create ID maps
    documentTypes.value = response.types?.map(t => {
      typeMap.value.set(t.type_name, t.id)
      idToTypeName.set(t.id, t.type_name)
      return {
        label: t.type_name,
        value: t.type_name
      }
    }) || []

    // Map licenses to label-value format and create ID maps
    licenses.value = response.licenses?.map(l => {
      licenseMap.value.set(l.license_name, l.id)
      idToLicenseName.set(l.id, l.license_name)
      return {
        label: l.license_name,
        value: l.license_name
      }
    }) || []

    // Set access rights and years from API
    accessRights.value = response.access_rights || []
    years.value = response.years || []

    console.log('Loaded subjects:', subjects.value)
    console.log('Loaded document types:', documentTypes.value)
    console.log('Loaded licenses:', licenses.value)
    console.log('Loaded years:', years.value)

    // Parse advanced search params (subject_id, type_id, year, etc from advanced search)
    const subjectIdParam = route.query.subject_id as string
    const typeIdParam = route.query.type_id as string
    // const licenseIdParam = route.query.license_id as string     // Not ready yet
    // const accessRightParam = route.query.access_right as string // Not ready yet
    const yearParam = route.query.year as string

    if (subjectIdParam) {
      const subjectIds = subjectIdParam.split(',').map(id => parseInt(id))
      selectedSubjects.value = subjectIds
        .map(id => idToSubjectName.get(id))
        .filter((name): name is string => name !== undefined)
      console.log('Pre-selected subjects from URL:', selectedSubjects.value)
    }

    if (typeIdParam) {
      const typeIds = typeIdParam.split(',').map(id => parseInt(id))
      selectedTypes.value = typeIds
        .map(id => idToTypeName.get(id))
        .filter((name): name is string => name !== undefined)
      console.log('Pre-selected types from URL:', selectedTypes.value)
    }

    // Parse license_id from URL (commented out - not ready yet)
    // if (licenseIdParam) {
    //   const licenseId = parseInt(licenseIdParam)
    //   selectedLicense.value = idToLicenseName.get(licenseId) || ''
    //   console.log('Pre-selected license from URL:', selectedLicense.value)
    // }

    // Parse access_right from URL (commented out - not ready yet)
    // if (accessRightParam) {
    //   selectedAccessRight.value = accessRightParam
    //   console.log('Pre-selected access right from URL:', selectedAccessRight.value)
    // }

    // Parse year from URL
    if (yearParam) {
      selectedYear.value = parseInt(yearParam)
      console.log('Pre-selected year from URL:', selectedYear.value)
    }

    // Perform initial search with current URL params
    const subjectIds = selectedSubjects.value
      .map(name => subjectMap.value.get(name))
      .filter((id): id is number => id !== undefined)

    const typeIds = selectedTypes.value
      .map(name => typeMap.value.get(name))
      .filter((id): id is number => id !== undefined)

    const yearFilter = selectedYear.value && selectedYear.value !== ''
      ? (typeof selectedYear.value === 'number' ? selectedYear.value : parseInt(selectedYear.value))
      : undefined

    // Directly call searchDocuments without updating URL (URL is already set)
    await searchDocuments(
      query || '',
      page,
      undefined,
      subjectIds.length > 0 ? subjectIds : undefined,
      typeIds.length > 0 ? typeIds : undefined,
      yearFilter
    )
  } catch (error) {
    console.error('Failed to load filters:', error)
    // Fallback: load documents without filters
    searchDocuments(query || '', page)
  }
})

// Watch route changes
watch(() => route.query, async (newQuery, oldQuery) => {
  // Skip if this is an internal update (from our own router.push)
  if (isInternalUpdate.value) {
    return
  }

  const query = newQuery.q as string
  const page = parseInt(newQuery.page as string) || 1

  // Update local search state
  if (query !== localSearchQuery.value) {
    localSearchQuery.value = query || ''
    searchQuery.value = query || ''
  }

  // Update page
  if (page !== currentPage.value) {
    currentPage.value = page
  }

  // Check if URL params changed (from advanced search or manual navigation)
  const paramsChanged =
    newQuery.q !== oldQuery?.q ||
    newQuery.page !== oldQuery?.page ||
    newQuery.subject_id !== oldQuery?.subject_id ||
    newQuery.type_id !== oldQuery?.type_id ||
    newQuery.year !== oldQuery?.year

  // If params changed, perform search
  if (paramsChanged) {
    isSearching.value = true

    try {
      // Re-parse filters from URL
      const subjectIdParam = newQuery.subject_id as string
      const typeIdParam = newQuery.type_id as string
      const yearParam = newQuery.year as string

      // Create reverse maps
      const idToSubjectName = new Map<number, string>()
      const idToTypeName = new Map<number, string>()

      subjects.value.forEach(s => {
        const id = subjectMap.value.get(s.value)
        if (id) idToSubjectName.set(id, s.value)
      })

      documentTypes.value.forEach(t => {
        const id = typeMap.value.get(t.value)
        if (id) idToTypeName.set(id, t.value)
      })

      // Update selected filters
      if (subjectIdParam) {
        const subjectIds = subjectIdParam.split(',').map(id => parseInt(id))
        selectedSubjects.value = subjectIds
          .map(id => idToSubjectName.get(id))
          .filter((name): name is string => name !== undefined)
      } else {
        selectedSubjects.value = []
      }

      if (typeIdParam) {
        const typeIds = typeIdParam.split(',').map(id => parseInt(id))
        selectedTypes.value = typeIds
          .map(id => idToTypeName.get(id))
          .filter((name): name is string => name !== undefined)
      } else {
        selectedTypes.value = []
      }

      if (yearParam) {
        selectedYear.value = parseInt(yearParam)
      } else {
        selectedYear.value = ''
      }

      // Perform search with updated params (but don't update URL again)
      const subjectIds = selectedSubjects.value
        .map(name => subjectMap.value.get(name))
        .filter((id): id is number => id !== undefined)

      const typeIds = selectedTypes.value
        .map(name => typeMap.value.get(name))
        .filter((id): id is number => id !== undefined)

      const yearFilter = selectedYear.value && selectedYear.value !== ''
        ? (typeof selectedYear.value === 'number' ? selectedYear.value : parseInt(selectedYear.value))
        : undefined

      await searchDocuments(
        searchQuery.value,
        currentPage.value,
        undefined,
        subjectIds.length > 0 ? subjectIds : undefined,
        typeIds.length > 0 ? typeIds : undefined,
        yearFilter
      )
    } finally {
      isSearching.value = false
    }
  }
})
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
