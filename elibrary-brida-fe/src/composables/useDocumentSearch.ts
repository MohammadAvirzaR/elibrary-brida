import { ref } from 'vue'

export interface Document {
  id: number
  title: string
  author?: string
  description?: string
  cover_image?: string
  file_path?: string
  category?: string
  published_date?: string
  // tambahkan field lain sesuai response API Anda
}

export interface SearchResponse {
  data: Document[]
  total: number
  page?: number
  per_page?: number
}

const API_BASE_URL = 'http://127.0.0.1:8000/api'

export function useDocumentSearch() {
  const searchResults = ref<Document[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const totalResults = ref(0)

  const searchDocuments = async (query: string, filter?: string) => {
    if (!query.trim() && !filter) {
      searchResults.value = []
      totalResults.value = 0
      return
    }

    isLoading.value = true
    error.value = null

    try {
      let url = `${API_BASE_URL}/documents/search?`

      if (query.trim()) {
        url += `q=${encodeURIComponent(query)}`
      }

      if (filter) {
        url += query.trim() ? `&filter=${filter}` : `filter=${filter}`
      }

      const response = await fetch(url)

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const data: SearchResponse = await response.json()

      searchResults.value = data.data || []
      totalResults.value = data.total || 0
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch search results'
      searchResults.value = []
      totalResults.value = 0
      console.error('Search error:', err)
    } finally {
      isLoading.value = false
    }
  }

  const clearResults = () => {
    searchResults.value = []
    totalResults.value = 0
    error.value = null
  }

  return {
    searchResults,
    isLoading,
    error,
    totalResults,
    searchDocuments,
    clearResults,
  }
}
