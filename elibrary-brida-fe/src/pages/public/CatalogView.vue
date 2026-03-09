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
                    by {{ document.author || 'Unknown Author' }}
                  </p>
                  <p class="text-sm text-gray-500 mb-3">
                    {{ document.category || 'General' }} • {{ formatDate(document.published_date) }}
                  </p>
                  <p class="text-gray-700 text-sm line-clamp-3 mb-4">
                    {{ document.description || 'No description available.' }}
                  </p>

                  <!-- Tags -->
                  <div class="flex gap-2 mb-4">
                    <span
                      v-if="document.category"
                      class="inline-block px-3 py-1 text-xs font-semibold bg-orange-100 text-orange-800 rounded-full"
                    >
                      {{ document.category }}
                    </span>
                    <span class="inline-block px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                      Artikel Jurnal
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
                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2">
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
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useDocumentSearch } from '@/composables/useDocumentSearch'
import { useDebounceFn } from '@vueuse/core'
import NavigationBar from '@/components/NavigationBar.vue'
import FooterSection from '@/components/FooterSection.vue'
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

// Maps to store ID lookups
const subjectMap = ref<Map<string, number>>(new Map())
const typeMap = ref<Map<string, number>>(new Map())

// Check if user is authenticated
const isAuthenticated = computed(() => {
  return !!localStorage.getItem('auth_token')
});

// Dynamic filters from API
const subjects = ref<Array<{ label: string; value: string }>>([])
const documentTypes = ref<Array<{ label: string; value: string }>>([])

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
  isSearching.value = false
  searchQuery.value = localSearchQuery.value
  await performSearch()
}, 500)

const handleSearch = () => {
  isSearching.value = true
  debouncedSearch()
}

const performSearch = async () => {
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

    await searchDocuments(
      searchQuery.value,
      currentPage.value,
      undefined,
      subjectIds.length > 0 ? subjectIds : undefined,
      typeIds.length > 0 ? typeIds : undefined
    )

    // Update URL query parameter
    if (searchQuery.value.trim()) {
      router.push({
        query: {
          q: searchQuery.value,
          page: currentPage.value
        }
      })
    } else {
      router.push({
        query: {
          page: currentPage.value > 1 ? currentPage.value : undefined
        }
      })
    }
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
  await performSearch()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const formatDate = (date?: string) => {
  if (!date) return 'N/A'
  return new Date(date).getFullYear()
}

const prevPage = async () => {
  if (currentPage.value > 1) {
    currentPage.value--
    await performSearch()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const nextPage = async () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    await performSearch()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const goToPage = async (page: number) => {
  currentPage.value = page
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
  searchDocuments(query || '', page)

  // Fetch filters from API
  try {
    const response = await api.filters.getAll() as {
      subjects: Array<{ id: number; subject_name: string }>
      types: Array<{ id: number; type_name: string }>
    }

    console.log('API Response:', response)

    // Map subjects to label-value format and create ID map
    subjects.value = response.subjects?.map(s => {
      subjectMap.value.set(s.subject_name, s.id)
      return {
        label: s.subject_name,
        value: s.subject_name
      }
    }) || []

    // Map document types to label-value format and create ID map
    documentTypes.value = response.types?.map(t => {
      typeMap.value.set(t.type_name, t.id)
      return {
        label: t.type_name,
        value: t.type_name
      }
    }) || []

    console.log('Loaded subjects:', subjects.value)
    console.log('Loaded document types:', documentTypes.value)
    console.log('Subject ID map:', Object.fromEntries(subjectMap.value))
    console.log('Type ID map:', Object.fromEntries(typeMap.value))
  } catch (error) {
    console.error('Failed to load filters:', error)
  }
})

// Watch route changes
watch(() => route.query, (newQuery) => {
  const query = newQuery.q as string
  const page = parseInt(newQuery.page as string) || 1

  if (query !== localSearchQuery.value) {
    localSearchQuery.value = query || ''
    searchQuery.value = query || ''
  }

  if (page !== currentPage.value) {
    currentPage.value = page
    searchDocuments(query || '', page)
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
