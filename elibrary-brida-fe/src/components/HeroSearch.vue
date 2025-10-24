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
      />
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
import { useSearch } from '@/composables/useSearch'

const { searchQuery, setSearchQuery } = useSearch()
const localSearch = ref(searchQuery.value)

// Sinkronisasi dengan global search state
watch(searchQuery, (newValue) => {
  localSearch.value = newValue
})

const handleSearch = () => {
  setSearchQuery(localSearch.value)
}

const scrollToCatalog = () => {
  const catalogElement = document.getElementById('catalog')
  if (catalogElement) {
    catalogElement.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>
