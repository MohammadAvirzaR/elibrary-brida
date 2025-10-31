<template>
  <section
    id="catalog"
    class="container mx-auto my-16 px-6 min-h-screen scroll-mt-20 pt-5"
  >
    <!-- Search Results Info -->
    <div v-if="searchQuery && totalResults > 0" class="mb-4 text-center">
      <p class="text-gray-600">
        Ditemukan <span class="font-semibold">{{ totalResults }}</span> hasil untuk "<span class="font-semibold">{{ searchQuery }}</span>"
      </p>
    </div>

    <!-- Tabs -->
    <div class="flex justify-center space-x-6 border-b pb-2 text-gray-600 mb-8">
      <button
        v-for="tab in tabs"
        :key="tab.value"
        @click="activeTab = tab.value"
        :class="[
          'pb-2 transition-all',
          activeTab === tab.value
            ? 'text-gray-900 font-semibold border-b-2 border-blue-600'
            : 'hover:text-gray-900 transition ease-in-out duration-200',
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
        class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white shadow-lg rounded-full p-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
        aria-label="Scroll left"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="w-6 h-6 text-gray-700"
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
        class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white shadow-lg rounded-full p-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
        aria-label="Scroll right"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="w-6 h-6 text-gray-700"
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
        class="flex gap-6 overflow-x-auto scrollbar-hide scroll-smooth pb-4"
        style="scrollbar-width: none; -ms-overflow-style: none"
      >
        <div
          v-for="book in filteredBooks"
          :key="book.id"
          class="flex-shrink-0 w-48 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 cursor-pointer"
        >
          <div class="p-4">
            <img
              :src="book.image"
              :alt="book.title"
              class="w-full h-64 object-cover rounded-md mb-3"
            />
            <p class="font-semibold text-gray-800 text-sm line-clamp-2 mb-1">
              {{ book.title }}
            </p>
            <p class="text-xs text-gray-500 mb-1">{{ book.author }}</p>
            <p class="text-xs text-gray-400">{{ book.year }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script lang="ts" setup>
import { ref, computed } from "vue";
import { useSearch } from '@/composables/useSearch'
import { useDocumentSearch } from '@/composables/useDocumentSearch'

const { searchQuery } = useSearch()
const { searchResults, totalResults } = useDocumentSearch()
const activeTab = ref("unggulan");
const scrollContainerRef = ref<HTMLElement | null>(null);

const tabs = [
  { label: "Unggulan", value: "unggulan" },
  { label: "Terbaru", value: "terbaru" },
  { label: "Paling Banyak Diunduh", value: "paling-banyak-diunduh" },
];

// Contoh data - nanti bisa diganti dengan data dari API
const books = ref({
  unggulan: [
    {
      id: 1,
      year: 2021,
      image:
        "https://upload.wikimedia.org/wikipedia/en/2/21/The_Universe_in_a_Nutshell_%28cover%29.jpg",
      title: "The Universe in a Nutshell",
      author: "Stephen Hawking",
    },
    {
      id: 2,
      year: 2021,
      image:
        "https://upload.wikimedia.org/wikipedia/en/2/21/The_Universe_in_a_Nutshell_%28cover%29.jpg",
      title: "The Universe in a Nutshell",
      author: "Stephen Hawking",
    },
    {
      id: 3,
      year: 2021,
      image:
        "https://upload.wikimedia.org/wikipedia/en/2/21/The_Universe_in_a_Nutshell_%28cover%29.jpg",
      title: "The Universe in a Nutshell",
      author: "Stephen Hawking",
    },
    {
      id: 4,
      year: 2021,
      image:
        "https://upload.wikimedia.org/wikipedia/en/2/21/The_Universe_in_a_Nutshell_%28cover%29.jpg",
      title: "The Universe in a Nutshell",
      author: "Stephen Hawking",
    },
    {
      id: 5,
      year: 1859,
      image:
        "https://upload.wikimedia.org/wikipedia/en/7/7a/Origin_of_Species_title_page.jpg",
      title: "The Origin of Species",
      author: "Charles Darwin",
    },
  ],
  terbaru: [
    {
      id: 6,
      year: 1988,
      image:
        "https://upload.wikimedia.org/wikipedia/en/3/3c/BriefHistoryTime.jpg",
      title: "A Brief History of Time",
      author: "Stephen Hawking",
    },
    {
      id: 7,
      year: 1988,
      image:
        "https://upload.wikimedia.org/wikipedia/en/3/3c/BriefHistoryTime.jpg",
      title: "A Brief History of Time",
      author: "Stephen Hawking",
    },
    {
      id: 8,
      year: 1988,
      image:
        "https://upload.wikimedia.org/wikipedia/en/3/3c/BriefHistoryTime.jpg",
      title: "A Brief History of Time",
      author: "Stephen Hawking",
    },
  ],
  "paling-banyak-diunduh": [
    {
      id: 9,
      year: 1980,
      image:
        "https://upload.wikimedia.org/wikipedia/en/2/2b/Cosmos_book_cover.jpg",
      title: "Cosmos",
      author: "Carl Sagan",
    },
    {
      id: 10,
      year: 1980,
      image:
        "https://upload.wikimedia.org/wikipedia/en/2/2b/Cosmos_book_cover.jpg",
      title: "Cosmos",
      author: "Carl Sagan",
    },
    {
      id: 11,
      year: 1980,
      image:
        "https://upload.wikimedia.org/wikipedia/en/2/2b/Cosmos_book_cover.jpg",
      title: "Cosmos",
      author: "Carl Sagan",
    },
  ],
});

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

  // Jika tidak ada search, tampilkan data tab
  const tabBooks = books.value[activeTab.value as keyof typeof books.value] || [];

  // Filter berdasarkan search query di data lokal (fallback)
  if (!searchQuery.value) {
    return tabBooks;
  }

  const query = searchQuery.value.toLowerCase();
  return tabBooks.filter((book) => {
    return (
      book.title.toLowerCase().includes(query) ||
      book.author.toLowerCase().includes(query) ||
      book.year.toString().includes(query)
    );
  });
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
