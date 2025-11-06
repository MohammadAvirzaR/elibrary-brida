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
  current_page?: number
  last_page?: number
  per_page?: number
  from?: number
  to?: number
}

const API_BASE_URL = 'http://127.0.0.1:8000/api'

export function useDocumentSearch() {
  const searchResults = ref<Document[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const totalResults = ref(0)
  const currentPage = ref(1)
  const lastPage = ref(1)
  const perPage = ref(10)

  const searchDocuments = async (query: string, page: number = 1, filter?: string) => {
    isLoading.value = true
    error.value = null

    try {
      // Bangun URL dengan query parameter yang benar
      const params = new URLSearchParams()

      if (query.trim()) {
        params.append('q', query.trim())
      }

      if (page > 1) {
        params.append('page', page.toString())
      }

      if (filter) {
        params.append('filter', filter)
      }

      const url = `${API_BASE_URL}/documents/search?${params.toString()}`

      const response = await fetch(url)

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const data: SearchResponse = await response.json()

      searchResults.value = data.data || []
      totalResults.value = data.total || 0
      currentPage.value = data.current_page || 1
      lastPage.value = data.last_page || 1
      perPage.value = data.per_page || 10
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
    currentPage,
    lastPage,
    perPage,
    searchDocuments,
    clearResults,
  }
}
