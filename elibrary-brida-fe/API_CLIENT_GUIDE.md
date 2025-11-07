# API Client Usage Guide

## 📚 Overview

The API client (`src/services/api.ts`) provides a centralized, type-safe way to interact with the Laravel backend. All API calls are handled through this service.

---

## 🔧 Configuration

### Environment Variables
Located in `.env`:
```env
VITE_API_BASE_URL=http://127.0.0.1:8000/api
```

### Import the API Client
```typescript
import api from '@/services/api'
```

---

## 🔐 Authentication Endpoints

### 1. Register
**Purpose:** Create a new user account with auto-login

```typescript
// Example usage
const registerUser = async () => {
  try {
    const response = await api.auth.register({
      name: 'John Doe',
      email: 'john@example.com',
      institution: 'University ABC',
      password: 'password123',
      password_confirmation: 'password123'
    })
    
    // Response includes user data and token
    console.log(response.user)  // { id, name, email, role, ... }
    console.log(response.token) // "1|xyz..."
    
    // Store token
    localStorage.setItem('auth_token', response.token)
  } catch (error) {
    console.error('Registration failed:', error.message)
  }
}
```

**Backend Endpoint:** `POST /api/register`

**Response:**
```typescript
{
  user: {
    id: number
    name: string
    email: string
    institution: string
    role: string  // Default: "guest"
  }
  token: string
}
```

---

### 2. Login
**Purpose:** Authenticate existing user

```typescript
const loginUser = async (email: string, password: string) => {
  try {
    const response = await api.auth.login({
      email,
      password
    })
    
    // Store token
    localStorage.setItem('auth_token', response.token)
    
    // Store user data
    localStorage.setItem('user', JSON.stringify(response.user))
    
    // Redirect based on role
    const role = response.user.role
    if (role === 'super_admin') {
      router.push('/dashboard')
    } else if (role === 'admin') {
      router.push('/welcome')
    } else {
      router.push('/')
    }
  } catch (error) {
    console.error('Login failed:', error.message)
  }
}
```

**Backend Endpoint:** `POST /api/login`

**Response:**
```typescript
{
  user: {
    id: number
    name: string
    email: string
    role: string
  }
  token: string
}
```

---

### 3. Get Current User
**Purpose:** Fetch current authenticated user's data

```typescript
const getCurrentUser = async () => {
  try {
    const response = await api.auth.me()
    
    // Update localStorage with fresh data
    localStorage.setItem('user', JSON.stringify(response.user))
    
    return response.user
  } catch (error) {
    // Token invalid or expired
    console.error('Failed to fetch user:', error.message)
    // Logout user
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
  }
}
```

**Backend Endpoint:** `GET /api/me`  
**Auth Required:** Yes (Bearer token)

**Response:**
```typescript
{
  user: {
    id: number
    name: string
    email: string
    institution: string
    role: {
      id: number
      name: string
      permissions: Permission[]
    }
  }
}
```

---

### 4. Logout
**Purpose:** Invalidate user token

```typescript
const logoutUser = async () => {
  try {
    await api.auth.logout()
    
    // Clear local storage
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
    
    // Notify other components
    window.dispatchEvent(new Event('auth-changed'))
    
    // Redirect to home
    router.push('/')
  } catch (error) {
    // Even if API call fails, clear local data
    localStorage.clear()
    router.push('/')
  }
}
```

**Backend Endpoint:** `POST /api/logout`  
**Auth Required:** Yes

---

## 📄 Document Endpoints

### 1. Search Documents
**Purpose:** Search for documents with filters

```typescript
const searchDocuments = async (query: string, filters?: object) => {
  try {
    const response = await api.documents.search({
      q: query,
      type: 'jurnal',
      year: 2023,
      page: 1,
      limit: 10,
      ...filters
    })
    
    return response.data  // Array of documents
  } catch (error) {
    console.error('Search failed:', error.message)
    return []
  }
}
```

**Backend Endpoint:** `GET /api/documents/search`

**Query Parameters:**
- `q`: Search query string
- `type`: Document type (jurnal, buku, prosiding, etc.)
- `year`: Publication year
- `page`: Page number (pagination)
- `limit`: Items per page

**Response:**
```typescript
{
  data: Document[]
  pagination: {
    current_page: number
    last_page: number
    total: number
    per_page: number
  }
}
```

---

### 2. Get Featured Content
**Purpose:** Fetch featured/highlighted documents

```typescript
const getFeaturedDocs = async () => {
  try {
    const response = await api.documents.featuredContent()
    return response.data
  } catch (error) {
    console.error('Failed to fetch featured content:', error)
    return []
  }
}
```

**Backend Endpoint:** `GET /api/documents/featured-content`

---

### 3. Get All Documents (Admin)
**Purpose:** List all documents for management

```typescript
const getAllDocuments = async () => {
  try {
    const response = await api.documents.getAll()
    return response.data
  } catch (error) {
    console.error('Failed to fetch documents:', error)
    return []
  }
}
```

**Backend Endpoint:** `GET /api/documents`  
**Auth Required:** Yes (Admin, Super Admin)

---

### 4. Create Document
**Purpose:** Add new document to library

```typescript
const createDocument = async (docData: object) => {
  try {
    const response = await api.documents.create({
      title: 'Document Title',
      author: 'Author Name',
      type: 'jurnal',
      year: 2024,
      abstract: 'Document abstract...',
      keywords: ['keyword1', 'keyword2'],
      file_path: '/uploads/document.pdf'
    })
    
    return response.document
  } catch (error) {
    console.error('Failed to create document:', error)
  }
}
```

**Backend Endpoint:** `POST /api/documents`  
**Auth Required:** Yes (Admin, Contributor)

---

### 5. Update Document
**Purpose:** Edit existing document

```typescript
const updateDocument = async (docId: number, updates: object) => {
  try {
    const response = await api.documents.update(docId, {
      title: 'Updated Title',
      abstract: 'Updated abstract'
    })
    
    return response.document
  } catch (error) {
    console.error('Failed to update document:', error)
  }
}
```

**Backend Endpoint:** `PUT /api/documents/{id}`  
**Auth Required:** Yes (Admin, Contributor - own documents)

---

### 6. Delete Document
**Purpose:** Remove document from library

```typescript
const deleteDocument = async (docId: number) => {
  try {
    await api.documents.delete(docId)
    console.log('Document deleted successfully')
  } catch (error) {
    console.error('Failed to delete document:', error)
  }
}
```

**Backend Endpoint:** `DELETE /api/documents/{id}`  
**Auth Required:** Yes (Admin, Super Admin)

---

### 7. Review Document
**Purpose:** Approve or reject submitted documents

```typescript
const reviewDocument = async (docId: number, approved: boolean, comment?: string) => {
  try {
    const response = await api.documents.review(
      docId,
      approved ? 'approved' : 'rejected',
      comment
    )
    
    return response.document
  } catch (error) {
    console.error('Failed to review document:', error)
  }
}
```

**Backend Endpoint:** `POST /api/documents/{id}/review`  
**Auth Required:** Yes (Reviewer, Admin, Super Admin)

**Request Body:**
```typescript
{
  status: 'approved' | 'rejected' | 'pending'
  comment?: string  // Optional review comment
}
```

---

### 8. Upload Document File
**Purpose:** Upload document file (PDF, DOCX, etc.)

```typescript
const uploadDocument = async (file: File) => {
  try {
    const formData = new FormData()
    formData.append('file', file)
    formData.append('title', 'Document Title')
    formData.append('type', 'jurnal')
    
    const response = await api.documents.upload(formData)
    return response.document
  } catch (error) {
    console.error('Upload failed:', error)
  }
}
```

**Backend Endpoint:** `POST /api/documents/upload`  
**Auth Required:** Yes (Contributor, Admin, Super Admin)

**Note:** Use `FormData` for file uploads

---

## 🔍 Filter Endpoints

### Get All Filters
**Purpose:** Fetch available filter options for search

```typescript
const loadFilters = async () => {
  try {
    const response = await api.filters.getAll()
    
    // Response contains filter options
    const types = response.types        // ['jurnal', 'buku', 'prosiding']
    const years = response.years        // [2024, 2023, 2022, ...]
    const authors = response.authors    // ['Author 1', 'Author 2', ...]
    
    return response
  } catch (error) {
    console.error('Failed to load filters:', error)
  }
}
```

**Backend Endpoint:** `GET /api/filters`

**Response:**
```typescript
{
  types: string[]      // Document types
  years: number[]      // Available years
  authors: string[]    // List of authors
  keywords: string[]   // Popular keywords
}
```

---

## ⚠️ Error Handling

### Standard Error Format
All API errors throw an `Error` with a message extracted from the backend response:

```typescript
try {
  await api.auth.login({ email: 'wrong@example.com', password: 'wrong' })
} catch (error) {
  // error.message contains user-friendly message
  console.error(error.message)
  // Example: "Invalid credentials" or "Email is required"
}
```

### Backend Error Response
```typescript
{
  message: string           // Main error message
  errors?: {               // Validation errors (422)
    email: ['Email is required'],
    password: ['Password must be at least 8 characters']
  }
}
```

### Common HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized (invalid/missing token)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## 🔒 Authorization

### Protected Endpoints
Most document management and admin endpoints require authentication.

The API client automatically includes the Bearer token from `localStorage`:

```typescript
// Token automatically added to headers
Authorization: Bearer {token}
```

### Token Management
```typescript
// Get token
const token = localStorage.getItem('auth_token')

// Store token (after login/register)
localStorage.setItem('auth_token', response.token)

// Remove token (on logout)
localStorage.removeItem('auth_token')
```

### Token Expiration
If a request returns `401 Unauthorized`, the token is likely expired:
```typescript
try {
  await api.documents.getAll()
} catch (error) {
  if (error.message.includes('Unauthorized')) {
    // Clear session and redirect to login
    localStorage.clear()
    router.push('/login')
  }
}
```

---

## 🎨 TypeScript Usage

### Type-Safe API Calls
Define interfaces for your data:

```typescript
interface Document {
  id: number
  title: string
  author: string
  type: string
  year: number
  abstract: string
  file_path: string
  created_at: string
}

interface SearchResponse {
  data: Document[]
  pagination: {
    current_page: number
    last_page: number
    total: number
  }
}

// Use with API
const searchDocs = async (): Promise<Document[]> => {
  const response = await api.documents.search({ q: 'machine learning' }) as SearchResponse
  return response.data
}
```

---

## 📝 Best Practices

### 1. Always Handle Errors
```typescript
const fetchData = async () => {
  try {
    const data = await api.documents.search({ q: 'test' })
    return data
  } catch (error) {
    console.error('API Error:', error)
    // Show user-friendly message
    alert('Failed to fetch documents. Please try again.')
    return null
  }
}
```

### 2. Use Loading States
```typescript
const isLoading = ref(false)

const loadDocuments = async () => {
  isLoading.value = true
  try {
    const docs = await api.documents.getAll()
    // Update UI
  } finally {
    isLoading.value = false
  }
}
```

### 3. Cache API Responses
```typescript
const cachedDocs = ref<Document[]>([])

const getDocuments = async (forceRefresh = false) => {
  if (cachedDocs.value.length && !forceRefresh) {
    return cachedDocs.value
  }
  
  const response = await api.documents.getAll()
  cachedDocs.value = response.data
  return cachedDocs.value
}
```

### 4. Debounce Search Requests
```typescript
import { debounce } from 'lodash'

const searchQuery = ref('')

const debouncedSearch = debounce(async (query: string) => {
  if (!query) return
  const results = await api.documents.search({ q: query })
  // Update UI
}, 500)  // Wait 500ms after user stops typing

watch(searchQuery, (newQuery) => {
  debouncedSearch(newQuery)
})
```

---

## 🧪 Testing API Calls

### Using Browser Console
```javascript
// Import API in component
import api from '@/services/api'

// Test in console
await api.auth.login({ email: 'test@example.com', password: 'password' })
await api.documents.search({ q: 'test' })
await api.auth.me()
```

### Using DevTools Network Tab
1. Open DevTools → Network
2. Filter: `Fetch/XHR`
3. Trigger API call
4. Inspect request/response

---

## 📖 Examples

### Complete Login Flow
```vue
<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  
  try {
    const response = await api.auth.login({
      email: email.value,
      password: password.value
    })
    
    // Store auth data
    localStorage.setItem('auth_token', response.token)
    localStorage.setItem('user', JSON.stringify(response.user))
    
    // Notify components
    window.dispatchEvent(new Event('auth-changed'))
    
    // Redirect based on role
    const role = response.user.role
    if (role === 'super_admin') {
      router.push('/dashboard')
    } else {
      router.push('/')
    }
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}
</script>
```

### Complete Search Component
```vue
<script setup lang="ts">
import { ref, watch } from 'vue'
import api from '@/services/api'

const searchQuery = ref('')
const documents = ref([])
const loading = ref(false)

const performSearch = async () => {
  if (!searchQuery.value) {
    documents.value = []
    return
  }
  
  loading.value = true
  try {
    const response = await api.documents.search({
      q: searchQuery.value,
      limit: 20
    })
    documents.value = response.data
  } catch (error) {
    console.error('Search failed:', error)
    documents.value = []
  } finally {
    loading.value = false
  }
}

watch(searchQuery, () => {
  performSearch()
})
</script>
```

---

**API Client Version:** 1.0.0  
**Last Updated:** 2024  
**Backend:** Laravel 10 with Sanctum Authentication
