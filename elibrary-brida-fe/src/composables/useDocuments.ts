import { ref } from 'vue'

export interface Document {
  id: number
  title: string
  author?: string
  description?: string
  cover_image?: string
  thumbnail_url?: string
  file_path?: string
  category?: string
  published_date?: string
}

export interface DocumentsResponse {
  data: Document[]
  total: number
  page?: number
  per_page?: number
}

const API_BASE_URL = 'http://127.0.0.1:8000/api'

export function useDocuments() {
  const documents = ref<Document[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const totalDocuments = ref(0)

  // Fetch documents dengan filter (featured, latest, most_downloaded)
  const fetchDocuments = async (filter?: 'is_featured' | 'upload_date' | 'download_count') => {
    isLoading.value = true
    error.value = null

    try {
      let url = `${API_BASE_URL}/documents/search?`

      if (filter) {
        url += `filter=${filter}`
      }

      const response = await fetch(url)

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const data: DocumentsResponse = await response.json()

      documents.value = data.data || []
      totalDocuments.value = data.total || 0
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch documents'
      documents.value = []
      totalDocuments.value = 0
      console.error('Fetch documents error:', err)
    } finally {
      isLoading.value = false
    }
  }

  const clearDocuments = () => {
    documents.value = []
    totalDocuments.value = 0
    error.value = null
  }

  return {
    documents,
    isLoading,
    error,
    totalDocuments,
    fetchDocuments,
    clearDocuments,
  }
}
