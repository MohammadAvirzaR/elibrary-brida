<template>
  <section
    class="flex flex-col items-center gap-[30px] w-full max-w-[720px] mx-auto text-center pt-64 min-h-screen"
  >
    <h2 class="text-[32px] font-heading font-bold text-neutral-950 mb-6">
      E-Library BRIDA Sulawesi Tenggara
    </h2>
    <div class="relative w-full">
      <i-lucide-search
        class="absolute right-6 top-1/2 -translate-y-1/2 w-6 h-6 text-neutral-400"
        v-if="!isLoading"
      />
      <svg
        v-if="isLoading"
        class="absolute right-6 top-1/2 -translate-y-1/2 w-6 h-6 animate-spin text-neutral-400"
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
        class="bg-neutral-200 placeholder-neutral-400 text-neutral-950 rounded-full px-6 py-3 w-full focus:outline-none hover:bg-neutral-300 transition"
      />
    </div>

    <button
      @click="scrollToCatalog"
      class="bg-neutral-800 text-white font-body px-8 py-3 rounded-md hover:bg-neutral-950 transition"
    >
      Advanced Search
    </button>
  </section>
</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useSearch } from '@/composables/useSearch'
import { useDocumentSearch } from '@/composables/useDocumentSearch'
import { useDebounceFn } from '@vueuse/core'

const router = useRouter()
const { searchQuery, setSearchQuery } = useSearch()
const { searchDocuments, isLoading } = useDocumentSearch()
const localSearch = ref(searchQuery.value)

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
      query: { q: localSearch.value }
    })
    return
  }

  // Jika tidak ada query, scroll ke catalog
  const catalogElement = document.getElementById('catalog')
  if (catalogElement) {
    catalogElement.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>
