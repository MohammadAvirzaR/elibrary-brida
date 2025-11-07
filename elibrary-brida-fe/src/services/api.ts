// API Base Configuration
// eslint-disable-next-line @typescript-eslint/no-explicit-any
const API_BASE_URL = (import.meta as any).env?.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api'

// Helper function to get auth token
const getAuthToken = (): string | null => {
  return localStorage.getItem('auth_token')
}

// Helper function to create headers
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

// Generic API call function
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
      throw new Error(data.message || 'API request failed')
    }

    return data
  } catch (error) {
    console.error('API Error:', error)
    throw error
  }
}

// Export API client
export const api = {
  // Auth endpoints
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

  // Documents endpoints
  documents: {
    search: (query: string, page = 1, limit = 10) =>
      apiCall(`/documents/search?q=${encodeURIComponent(query)}&page=${page}&limit=${limit}`),

    featuredContent: () => apiCall('/documents/featured-content'),

    getAll: (page = 1, limit = 10) =>
      apiCall(`/documents?page=${page}&limit=${limit}`, { method: 'GET' }, true),

    create: (data: FormData) =>
      apiCall('/documents', {
        method: 'POST',
        body: data,
        headers: {
          'Authorization': `Bearer ${getAuthToken()}`,
          'Accept': 'application/json',
        },
      } as RequestInit, true),

    update: (id: number, data: FormData) =>
      apiCall(`/documents/${id}`, {
        method: 'POST', // Laravel uses POST with _method=PUT for FormData
        body: data,
        headers: {
          'Authorization': `Bearer ${getAuthToken()}`,
          'Accept': 'application/json',
        },
      } as RequestInit, true),

    delete: (id: number) =>
      apiCall(`/documents/${id}`, { method: 'DELETE' }, true),

    review: () => apiCall('/documents/review', { method: 'GET' }, true),

    upload: (data: FormData) =>
      apiCall('/documents/upload', {
        method: 'POST',
        body: data,
        headers: {
          'Authorization': `Bearer ${getAuthToken()}`,
          'Accept': 'application/json',
        },
      } as RequestInit, true),
  },

  // Filters endpoints
  filters: {
    getAll: () => apiCall('/filters'),
  },

  // Roles endpoints
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

  // Users endpoints
  users: {
    getAll: () => apiCall('/users', { method: 'GET' }, true),

    getById: (id: number) => apiCall(`/users/${id}`, { method: 'GET' }, true),

    create: (data: { name: string; email: string; institution: string; password: string; role_id: number }) =>
      apiCall('/users', { method: 'POST', body: JSON.stringify(data) }, true),

    update: (id: number, data: { name?: string; email?: string; institution?: string; role_id?: number }) =>
      apiCall(`/users/${id}`, { method: 'PUT', body: JSON.stringify(data) }, true),

    delete: (id: number) =>
      apiCall(`/users/${id}`, { method: 'DELETE' }, true),
  },
}

export default api
