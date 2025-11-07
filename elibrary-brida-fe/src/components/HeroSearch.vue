<template>
  <section
    class="flex flex-col items-center justify-center gap-8 w-full max-w-2xl mx-auto text-center px-6 min-h-[90vh]"
  >
    <div class="space-y-3">
      <h2 class="text-4xl md:text-5xl font-heading font-bold text-neutral-900 tracking-tight">
        E-Library BRIDA
      </h2>
      <p class="text-lg text-neutral-600 font-medium">Sulawesi Tenggara</p>
    </div>

    <div class="relative w-full max-w-xl">
      <i-lucide-search
        class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-neutral-400"
        v-if="!isLoading"
      />
      <svg
        v-if="isLoading"
        class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 animate-spin text-neutral-400"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        ></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        ></path>
      </svg>
      <input
        type="text"
        v-model="localSearch"
        @input="handleSearch"
        @keyup.enter="scrollToCatalog"
        placeholder="Cari buku digital..."
        class="bg-white placeholder-neutral-400 text-neutral-950 rounded-full pl-14 pr-6 py-4 w-full shadow-sm border border-neutral-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:shadow-md transition-all duration-200"
      />
    </div>

    <button
      @click="openAdvancedSearch"
      class="bg-neutral-900 text-white font-medium px-6 py-2.5 rounded-full hover:bg-neutral-800 transition-all duration-200 shadow-sm hover:shadow-md"
    >
      Advanced Search
    </button>

    <!-- Advanced Search Modal -->
    <AdvancedSearchModal
      :is-open="isAdvancedSearchOpen"
      @close="closeAdvancedSearch"
      @search="handleAdvancedSearch"
    />
  </section>
</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useSearch } from '@/composables/useSearch'
import { useDocumentSearch } from '@/composables/useDocumentSearch'
import { useDebounceFn } from '@vueuse/core'
import AdvancedSearchModal from './AdvancedSearchModal.vue'

interface SearchFilters {
  query: string
  license: string
  accessRight: string
  subjects: string[]
  types: string[]
  years: string[]
}

const router = useRouter()
const { searchQuery, setSearchQuery } = useSearch()
const { searchDocuments, isLoading } = useDocumentSearch()
const localSearch = ref(searchQuery.value)
const isAdvancedSearchOpen = ref(false)

// Sinkronisasi dengan global search state
watch(searchQuery, (newValue) => {
  localSearch.value = newValue
})

// Debounce search untuk menghindari terlalu banyak API calls
const debouncedSearch = useDebounceFn(async (query: string) => {
  setSearchQuery(query)
  await searchDocuments(query)
}, 500)

const handleSearch = () => {
  debouncedSearch(localSearch.value)
}

const scrollToCatalog = async () => {
  // Redirect ke halaman search jika ada query
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

  // Jika tidak ada query, scroll ke catalog
  const catalogElement = document.getElementById('catalog')
  if (catalogElement) {
    catalogElement.scrollIntoView({ behavior: 'smooth' })
  }
}

const openAdvancedSearch = () => {
  isAdvancedSearchOpen.value = true
}

const closeAdvancedSearch = () => {
  isAdvancedSearchOpen.value = false
}

const handleAdvancedSearch = (filters: SearchFilters) => {
  console.log('Advanced search filters:', filters)
  // Modal akan handle redirect ke search page
}
</script>
