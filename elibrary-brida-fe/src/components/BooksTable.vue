<template>
  <section
    id="catalog"
    class="container mx-auto my-20 px-6 scroll-mt-20"
  >
    <!-- Search Results Info -->
    <div v-if="searchQuery && totalResults > 0" class="mb-8 text-center">
      <p class="text-neutral-600 text-sm">
        Ditemukan <span class="font-semibold text-neutral-900">{{ totalResults }}</span> hasil untuk "<span class="font-semibold text-neutral-900">{{ searchQuery }}</span>"
      </p>
    </div>

    <!-- Tabs -->
    <div class="flex justify-center space-x-8 border-b border-neutral-200 pb-3 mb-10">
      <button
        v-for="tab in tabs"
        :key="tab.value"
        @click="activeTab = tab.value"
        :class="[
          'pb-3 px-1 transition-all text-sm font-medium',
          activeTab === tab.value
            ? 'text-neutral-900 border-b-2 border-blue-600'
            : 'text-neutral-500 hover:text-neutral-900',
        ]"
      >
        {{ tab.label }}
      </button>
    </div>

    <!-- Horizontal Scrollable Container -->
    <div class="relative group">
      <!-- Left Arrow -->
      <button
        @click="scrollContainer('left')"
        class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 z-10 bg-white shadow-lg rounded-full p-3 opacity-0 group-hover:opacity-100 transition-all duration-300 hover:scale-110"
        aria-label="Scroll left"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="w-5 h-5 text-neutral-700"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 19l-7-7 7-7"
          />
        </svg>
      </button>

      <!-- Right Arrow -->
      <button
        @click="scrollContainer('right')"
        class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 z-10 bg-white shadow-lg rounded-full p-3 opacity-0 group-hover:opacity-100 transition-all duration-300 hover:scale-110"
        aria-label="Scroll right"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="w-5 h-5 text-neutral-700"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 5l7 7-7 7"
          />
        </svg>
      </button>

      <!-- Book Cards Container -->
      <div
        ref="scrollContainerRef"
        class="flex gap-5 overflow-x-auto scrollbar-hide scroll-smooth pb-6"
        style="scrollbar-width: none; -ms-overflow-style: none"
      >
        <!-- Loading State -->
        <div v-if="isLoadingDocs" class="flex gap-4 w-full justify-center items-center py-20">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
          <p class="text-neutral-600 text-sm">Memuat data...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredBooks.length === 0" class="w-full text-center py-20">
          <p class="text-neutral-500 text-sm">Tidak ada buku ditemukan</p>
        </div>

        <!-- Book Cards -->
        <router-link
          v-else
          v-for="book in filteredBooks"
          :key="book.id"
          :to="isAuthenticated ? { name: 'document-detail', params: { id: book.id } } : '/login'"
          class="flex-shrink-0 w-44 bg-white rounded-xl border border-neutral-100 hover:border-neutral-200 hover:shadow-lg transition-all duration-300 cursor-pointer group"
        >
          <div class="p-3">
            <div class="relative overflow-hidden rounded-lg mb-3">
              <img
                :src="book.image"
                :alt="book.title"
                class="w-full h-60 object-cover group-hover:scale-105 transition-transform duration-300"
              />
            </div>
            <p class="font-semibold text-neutral-900 text-sm line-clamp-2 mb-1.5 leading-tight">
              {{ book.title }}
            </p>
            <p class="text-xs text-neutral-500 mb-0.5">{{ book.author }}</p>
            <p class="text-xs text-neutral-400">{{ book.year }}</p>
          </div>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script lang="ts" setup>
import { ref, computed, watch, onMounted } from "vue";
import { useSearch } from '@/composables/useSearch'
import { useDocumentSearch } from '@/composables/useDocumentSearch'
import { useDocuments } from '@/composables/useDocuments'

const { searchQuery } = useSearch()
const { searchResults, totalResults } = useDocumentSearch()
const { documents, fetchDocuments, isLoading: isLoadingDocs } = useDocuments()
const activeTab = ref("unggulan");
const scrollContainerRef = ref<HTMLElement | null>(null);

// Check if user is authenticated
const isAuthenticated = computed(() => {
  return !!localStorage.getItem('auth_token')
});

const tabs = [
  { label: "Unggulan", value: "unggulan", filter: "is_featured" as const },
  { label: "Terbaru", value: "terbaru", filter: "upload_date" as const },
  { label: "Paling Banyak Diunduh", value: "paling-banyak-diunduh", filter: "download_count" as const },
];

// Fetch data saat component dimount
onMounted(() => {
  const currentTab = tabs.find(tab => tab.value === activeTab.value)
  if (currentTab) {
    fetchDocuments(currentTab.filter)
  }
})

// Watch activeTab untuk fetch data saat tab berubah
watch(activeTab, (newTab) => {
  // Jangan fetch jika ada search query aktif
  if (searchQuery.value) return

  const tab = tabs.find(t => t.value === newTab)
  if (tab) {
    fetchDocuments(tab.filter)
  }
})

const filteredBooks = computed(() => {
  // Jika ada hasil search dari API, gunakan itu
  if (searchQuery.value && searchResults.value.length > 0) {
    return searchResults.value.map((doc) => ({
      id: doc.id,
      title: doc.title,
      author: doc.author || 'Unknown Author',
      year: doc.published_date ? new Date(doc.published_date).getFullYear() : '',
      image: doc.cover_image || 'https://via.placeholder.com/192x256?text=No+Cover',
    }));
  }

  // Jika tidak ada search, tampilkan data dari API berdasarkan tab
  if (!searchQuery.value && documents.value.length > 0) {
    return documents.value.map((doc) => ({
      id: doc.id,
      title: doc.title,
      author: doc.author || 'Unknown Author',
      year: doc.published_date ? new Date(doc.published_date).getFullYear() : '',
      image: doc.cover_image || 'https://via.placeholder.com/192x256?text=No+Cover',
    }));
  }

  // Fallback: return empty array
  return []
});

const scrollContainer = (direction: "left" | "right") => {
  if (scrollContainerRef.value) {
    const scrollAmount = 300;
    scrollContainerRef.value.scrollBy({
      left: direction === "left" ? -scrollAmount : scrollAmount,
      behavior: "smooth",
    });
  }
};
</script>

<style scoped>
/* Hide scrollbar for Chrome, Safari and Opera */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
