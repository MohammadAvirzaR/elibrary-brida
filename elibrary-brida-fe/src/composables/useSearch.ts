import { ref, computed } from 'vue'

const searchQuery = ref('')

export function useSearch() {
  const setSearchQuery = (query: string) => {
    searchQuery.value = query
  }

  const clearSearch = () => {
    searchQuery.value = ''
  }

  return {
    searchQuery: computed(() => searchQuery.value),
    setSearchQuery,
    clearSearch,
  }
}
