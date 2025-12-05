interface ImportMeta {
  env?: {
    VITE_API_BASE_URL?: string
  }
}

const API_BASE_URL = (import.meta as ImportMeta).env?.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api'

const getAuthToken = (): string | null => {
  return localStorage.getItem('auth_token')
}

const createHeaders = (includeAuth = false): HeadersInit => {
  const headers: HeadersInit = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  }

  if (includeAuth) {
    const token = getAuthToken()
    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }
  }

  return headers
}

async function apiCall<T>(
  endpoint: string,
  options: RequestInit = {},
  requiresAuth = false
): Promise<T> {
  const url = `${API_BASE_URL}${endpoint}`
  const headers = createHeaders(requiresAuth)

  const config: RequestInit = {
    ...options,
    headers: {
      ...headers,
      ...options.headers,
    },
  }

  try {
    const response = await fetch(url, config)
    const data = await response.json()

    if (!response.ok) {
      const errorMessage = `${response.status} - ${data.message || response.statusText || 'Request gagal'}`
      console.error(`API Error [${endpoint}]:`, errorMessage, data)
      throw new Error(errorMessage)
    }

    return data
  } catch (error) {
    if (error instanceof Error && error.message.includes('-')) {
      throw error
    }
    console.error('API Network Error:', error)
    throw error
  }
}

export const api = {
  auth: {
    register: (data: {
      name: string
      email: string
      institution: string
      password: string
      password_confirmation: string
    }) => apiCall('/register', { method: 'POST', body: JSON.stringify(data) }),

    login: (data: { email: string; password: string }) =>
      apiCall('/login', { method: 'POST', body: JSON.stringify(data) }),

    logout: () => apiCall('/logout', { method: 'POST' }, true),

    me: () => apiCall('/me', { method: 'GET' }, true),
  },

  documents: {
    search: (query: string, page = 1, limit = 10) =>
      apiCall(`/documents/search?q=${encodeURIComponent(query)}&page=${page}&limit=${limit}`),

    featuredContent: () => apiCall('/documents/featured-content'),

    getAll: (page = 1, limit = 10) =>
      apiCall(`/documents?page=${page}&limit=${limit}`, { method: 'GET' }, true),

    create: async (data: FormData) => {
      const token = getAuthToken()
      const response = await fetch(`${API_BASE_URL}/documents`, {
        method: 'POST',
        body: data,
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json',
        },
      })
      const result = await response.json()
      if (!response.ok) {
        throw new Error(result.message || 'Create failed')
      }
      return result
    },

    update: async (id: number, data: FormData | Record<string, unknown>) => {
      if (data instanceof FormData) {
        const token = getAuthToken()
        const response = await fetch(`${API_BASE_URL}/documents/${id}`, {
          method: 'POST',
          body: data,
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          },
        })
        const result = await response.json()
        if (!response.ok) {
          throw new Error(result.message || 'Update failed')
        }
        return result
      }
      return apiCall(`/documents/${id}`, {
        method: 'PUT',
        body: JSON.stringify(data)
      }, true)
    },

    delete: (id: number) =>
      apiCall(`/documents/${id}`, { method: 'DELETE' }, true),

    getById: (id: number) => {
      // Send auth token if user is logged in (to access pending docs)
      const hasToken = getAuthToken() !== null
      return apiCall(`/documents/${id}`, { method: 'GET' }, hasToken)
    },

    review: () => apiCall('/documents/review', { method: 'GET' }, true),

    upload: async (data: FormData) => {
      const token = getAuthToken()

      console.log('=== Uploading to API ===')
      console.log('Endpoint:', `${API_BASE_URL}/documents/upload`)
      console.log('FormData entries:', Array.from(data.entries()).length)

      const response = await fetch(`${API_BASE_URL}/documents/upload`, {
        method: 'POST',
        body: data,
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json',
        },
      })

      const result = await response.json()

      if (!response.ok) {
        if (result.errors) {
          console.error('Validation Errors:', result.errors)
        }

        let errorMessage = result.message || 'Upload failed'
        if (result.errors) {
          const errorDetails = Object.entries(result.errors)
            .map(([field, messages]) => `${field}: ${(messages as string[]).join(', ')}`)
            .join('\n')
          errorMessage += '\n\nDetails:\n' + errorDetails
        }

        throw new Error(errorMessage)
      }

      console.log('✓ Upload successful:', result)
      return result
    },
  },

  filters: {
    getAll: () => apiCall('/filters'),
  },

  roles: {
    getAll: () => apiCall('/roles', { method: 'GET' }, true),

    create: (data: { name: string; description: string; permissions: string[] }) =>
      apiCall('/roles', { method: 'POST', body: JSON.stringify(data) }, true),

    update: (id: number, data: { name: string; description: string; permissions: string[] }) =>
      apiCall(`/roles/${id}`, { method: 'PUT', body: JSON.stringify(data) }, true),

    delete: (id: number) =>
      apiCall(`/roles/${id}`, { method: 'DELETE' }, true),

    getPermissions: () => apiCall('/permissions', { method: 'GET' }, true),
  },

  users: {
    getAll: () => apiCall('/users', { method: 'GET' }, true),

    getById: (id: number) => apiCall(`/users/${id}`, { method: 'GET' }, true),

    create: (data: { name: string; email: string; institution?: string; phone?: string; address?: string; password: string; role_id: number }) =>
      apiCall('/users', { method: 'POST', body: JSON.stringify(data) }, true),

    update: (id: number, data: { name?: string; email?: string; institution?: string; phone?: string; address?: string; password?: string; role_id?: number }) =>
      apiCall(`/users/${id}`, { method: 'PUT', body: JSON.stringify(data) }, true),

    delete: (id: number) =>
      apiCall(`/users/${id}`, { method: 'DELETE' }, true),
  },

  contributorRequests: {
    getAll: () => apiCall('/contributor-requests', { method: 'GET' }, true),

    submit: (message: string) =>
      apiCall('/contributor-requests', { method: 'POST', body: JSON.stringify({ message }) }, true),

    checkPending: () => apiCall('/contributor-requests/check-pending', { method: 'GET' }, true),

    approve: (id: number, admin_notes?: string) =>
      apiCall(`/contributor-requests/${id}/approve`, { method: 'POST', body: JSON.stringify({ admin_notes }) }, true),

    reject: (id: number, admin_notes: string) =>
      apiCall(`/contributor-requests/${id}/reject`, { method: 'POST', body: JSON.stringify({ admin_notes }) }, true),
  },
}

export default api
