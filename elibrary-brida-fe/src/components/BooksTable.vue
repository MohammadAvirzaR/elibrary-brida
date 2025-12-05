<template>
  <section
    id="catalog"
    class="container mx-auto my-12 md:my-20 px-4 sm:px-6 scroll-mt-20"
  >
    <!-- Search Results Info -->
    <div v-if="searchQuery && totalResults > 0" class="mb-6 md:mb-8 text-center">
      <p class="text-sm md:text-base text-neutral-600">
        Ditemukan <span class="font-semibold text-neutral-900">{{ totalResults }}</span> hasil untuk "<span class="font-semibold text-neutral-900">{{ searchQuery }}</span>"
      </p>
    </div>

    <!-- Tabs -->
    <div class="flex justify-center gap-4 sm:gap-8 border-b border-neutral-200 pb-3 mb-8 md:mb-10 overflow-x-auto scrollbar-hide">
      <button
        v-for="tab in tabs"
        :key="tab.value"
        @click="activeTab = tab.value"
        :class="[
          'pb-3 px-2 sm:px-1 transition-all text-xs sm:text-sm font-medium whitespace-nowrap',
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
        class="hidden md:block absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 z-10 bg-white shadow-lg rounded-full p-2 md:p-3 opacity-0 group-hover:opacity-100 transition-all duration-300 hover:scale-110"
        aria-label="Scroll left"
      >
        <i-lucide-chevron-left class="w-4 h-4 md:w-5 md:h-5 text-neutral-700" />
      </button>

      <!-- Right Arrow -->
      <button
        @click="scrollContainer('right')"
        class="hidden md:block absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 z-10 bg-white shadow-lg rounded-full p-2 md:p-3 opacity-0 group-hover:opacity-100 transition-all duration-300 hover:scale-110"
        aria-label="Scroll right"
      >
        <i-lucide-chevron-right class="w-4 h-4 md:w-5 md:h-5 text-neutral-700" />
      </button>

      <!-- Book Cards Container -->
      <div
        ref="scrollContainerRef"
        class="flex gap-3 sm:gap-4 md:gap-5 overflow-x-auto scrollbar-hide scroll-smooth pb-6"
        style="scrollbar-width: none; -ms-overflow-style: none"
      >
        <!-- Loading State -->
        <div v-if="isLoadingDocs" class="flex flex-col sm:flex-row gap-3 sm:gap-4 w-full justify-center items-center py-12 md:py-20">
          <div class="animate-spin rounded-full h-8 w-8 md:h-10 md:w-10 border-b-2 border-blue-600"></div>
          <p class="text-neutral-600 text-sm md:text-base">Memuat data...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredBooks.length === 0" class="w-full text-center py-12 md:py-20">
          <i-lucide-inbox class="w-12 h-12 md:w-16 md:h-16 text-neutral-300 mx-auto mb-3" />
          <p class="text-neutral-500 text-sm md:text-base">Tidak ada buku ditemukan</p>
        </div>

        <!-- Book Cards -->
        <router-link
          v-else
          v-for="book in filteredBooks"
          :key="book.id"
          :to="{ name: 'document-detail', params: { id: book.id } }"
          class="flex-shrink-0 w-36 sm:w-40 md:w-44 bg-white rounded-xl border border-neutral-100 hover:border-neutral-200 hover:shadow-lg transition-all duration-300 cursor-pointer"
        >
          <div class="p-2.5 sm:p-3">
            <!-- Thumbnail -->
            <div class="overflow-hidden rounded-lg mb-2.5 sm:mb-3 bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center aspect-[3/4]">
              <img
                v-if="book.thumbnail"
                :src="book.thumbnail"
                :alt="book.title"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <i-lucide-file-text class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 text-blue-300" />
              </div>
            </div>

            <!-- Document Info -->
            <h3 class="font-semibold text-neutral-900 text-xs sm:text-sm line-clamp-2 mb-1 sm:mb-1.5 leading-tight">
              {{ book.title }}
            </h3>
            <p class="text-xs text-neutral-500 mb-0.5 truncate">{{ book.author }}</p>
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
      thumbnail: doc.thumbnail_url || null,
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
      thumbnail: doc.thumbnail_url || null,
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

const handleImageError = (event: Event) => {
  // Hide image on error, will fallback to icon
  const target = event.target as HTMLImageElement
  target.style.display = 'none'
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
