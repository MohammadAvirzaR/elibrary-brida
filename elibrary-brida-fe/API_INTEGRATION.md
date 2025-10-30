# API Integration Guide - E-Library BRIDA

## Search API Integration

### Endpoint
```
GET http://127.0.0.1:8000/api/documents/search?q={query}
```

### Implementation

#### 1. Composable: `useDocumentSearch.ts`
File ini berisi logic untuk memanggil API search dan mengelola state hasil pencarian.

**Location:** `src/composables/useDocumentSearch.ts`

**Features:**
- `searchDocuments(query)` - Fungsi untuk melakukan pencarian
- `searchResults` - Array hasil pencarian
- `isLoading` - Status loading
- `error` - Error message jika ada
- `totalResults` - Total hasil yang ditemukan
- `clearResults()` - Fungsi untuk clear hasil pencarian

**Expected API Response Format:**
```typescript
{
  data: [
    {
      id: number,
      title: string,
      author?: string,
      description?: string,
      cover_image?: string,
      file_path?: string,
      category?: string,
      published_date?: string
    }
  ],
  total: number,
  page?: number,
  per_page?: number
}
```

#### 2. Hero Search Component
**Location:** `src/components/HeroSearch.vue`

**Features:**
- Debounced search (500ms delay)
- Loading indicator (spinning icon)
- Auto-scroll ke catalog saat Enter atau klik "Advanced Search"
- Sinkronisasi dengan global search state

**Usage:**
```vue
<HeroSearch />
```

#### 3. Books Table Component
**Location:** `src/components/BooksTable.vue`

**Features:**
- Menampilkan hasil search dari API
- Fallback ke data lokal jika tidak ada search
- Menampilkan total hasil pencarian
- Tab switching (Unggulan, Terbaru, Paling Banyak Diunduh)

### How It Works

1. **User Types in Search Bar** (HeroSearch.vue)
   - Input di-debounce 500ms
   - Memanggil `searchDocuments(query)` dari composable
   - Menampilkan loading spinner saat fetching

2. **API Call** (useDocumentSearch.ts)
   - Fetch ke `http://127.0.0.1:8000/api/documents/search?q={query}`
   - Handle loading state
   - Handle error
   - Simpan results ke reactive state

3. **Display Results** (BooksTable.vue)
   - Subscribe ke `searchResults` dari composable
   - Transform data API ke format display
   - Tampilkan jumlah hasil
   - Fallback ke placeholder image jika tidak ada cover

### Configuration

Untuk mengubah base URL API, edit file:
```typescript
// src/composables/useDocumentSearch.ts
const API_BASE_URL = 'http://127.0.0.1:8000/api'
```

### Customization

#### Mengubah Debounce Delay
```typescript
// src/components/HeroSearch.vue
const debouncedSearch = useDebounceFn(async (query: string) => {
  setSearchQuery(query)
  await searchDocuments(query)
}, 500) // Ubah value ini (dalam milliseconds)
```

#### Menambahkan Field pada Document Interface
```typescript
// src/composables/useDocumentSearch.ts
export interface Document {
  id: number
  title: string
  author?: string
  // Tambahkan field baru di sini
  publisher?: string
  isbn?: string
  // dst...
}
```

### Error Handling

API errors akan ditampilkan di console dan tersimpan di `error` state:
```typescript
const { error } = useDocumentSearch()

// Error akan berisi message jika ada error
console.log(error.value) // "Failed to fetch search results" atau error message lain
```

### Dependencies

- `@vueuse/core` - Untuk debounce functionality
- Native Fetch API - Untuk HTTP requests

### Testing

Untuk test search functionality:
1. Pastikan backend API running di `http://127.0.0.1:8000`
2. Buka aplikasi Vue
3. Ketik di search bar
4. Hasil akan muncul setelah 500ms
5. Klik "Advanced Search" atau tekan Enter untuk scroll ke catalog

### Next Steps

- [ ] Tambahkan pagination untuk hasil search
- [ ] Implementasi advanced filters (category, date range, etc.)
- [ ] Cache search results untuk query yang sama
- [ ] Implementasi search history
- [ ] Error boundary untuk handle API errors lebih baik
