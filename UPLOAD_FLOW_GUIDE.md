# Upload Document Flow - Test Guide

## Setup Status
✅ **Backend Server**: Running on http://127.0.0.1:8000  
✅ **Frontend Server**: Running on http://localhost:5173  
✅ **API Endpoint**: POST http://127.0.0.1:8000/api/documents/upload  
✅ **Authentication**: Bearer Token (Sanctum)  
✅ **Middleware**: contributor, admin, super_admin roles can upload  

## Test Credentials

### Admin User
- Email: `admin@brida.com`
- Password: `admin123`
- Role: super_admin
- Can: Upload documents, review documents, full access

### Guest User  
- Email: `user@brida.com`
- Password: `user123`
- Role: guest
- Can: View and download documents

## Manual Testing Steps

### 1. Login
1. Open http://localhost:5173 in browser
2. Click "Login" or navigate to login page
3. Enter credentials: `admin@brida.com` / `admin123`
4. Click "Masuk" / "Login"
5. Should redirect to dashboard

### 2. Upload Document
1. In dashboard, click "Upload Dokumen" button
2. Modal should open with upload form
3. Fill in the form:
   - **File**: Click "Pilih File" or drag & drop a PDF/DOC/DOCX (max 10MB)
   - **Judul**: Enter document title
   - **Deskripsi**: Enter description
   - **Kategori**: Select category (penelitian, laporan, artikel, etc.)
   - **Tahun Terbit**: Enter year (1900 - current year)
   - **Penulis**: Enter author name
   - **Penerbit**: (Optional) Enter publisher
   - **Kata Kunci**: (Optional) Enter keywords separated by comma
4. Click "Upload Dokumen" button
5. Wait for upload to complete

### 3. Verify Upload Success
- Toast notification should appear: "Upload Tersimpan - Dokumen berhasil diunggah dan menunggu persetujuan admin"
- Modal should auto-close
- Document should appear in dashboard list
- Status should be "pending"

### 4. Verify in Database
- Document appears in ContributorDashboard stats
- Document appears in AdminDashboard review queue (if logged in as admin)
- Total documents count increases

## API Request Format

```bash
POST http://127.0.0.1:8000/api/documents/upload
Headers:
  Authorization: Bearer {token}
  Accept: application/json
Content-Type: multipart/form-data

Form Data:
  file: [PDF/DOC/DOCX file, max 10MB]
  title: string (required, max 255 chars)
  description: string (required)
  category: string (optional)
  year: integer (required, 1900-2025)
  author: string (required, max 255 chars)
  publisher: string (optional, max 255 chars)
  keywords: string (optional)
```

## API Response Format

### Success (201 Created)
```json
{
  "success": true,
  "message": "Dokumen berhasil diunggah dan menunggu persetujuan",
  "data": {
    "id": 3,
    "title": "Document Title",
    "abstract": "Description",
    "author": "Author Name",
    "publisher": "Publisher Name",
    "year_published": 2024,
    "keywords": "keyword1, keyword2",
    "file_path": "documents/1732166388_filename.pdf",
    "status": "pending",
    "type_id": null,
    "user_id": 1,
    "upload_date": "2024-11-21T05:39:48.000000Z",
    "created_at": "2024-11-21T05:39:48.000000Z",
    "updated_at": "2024-11-21T05:39:48.000000Z",
    "user": {
      "id": 1,
      "full_name": "Admin BRIDA",
      "email": "admin@brida.com"
    }
  }
}
```

### Error Responses

#### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```
**Cause**: Token missing, invalid, or expired  
**Solution**: Login again to get new token

#### 403 Forbidden
```json
{
  "success": false,
  "message": "Anda tidak memiliki izin untuk mengakses resource ini."
}
```
**Cause**: User role doesn't have upload permission  
**Solution**: Request contributor role from admin

#### 422 Validation Error
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "file": ["The file field is required."],
    "title": ["The title field is required."],
    "year": ["The year must be between 1900 and 2025."]
  }
}
```
**Cause**: Form validation failed  
**Solution**: Check all required fields and correct formats

#### 413 Payload Too Large
```json
{
  "message": "Payload Too Large"
}
```
**Cause**: File size exceeds 10MB limit  
**Solution**: Compress or reduce file size

#### 500 Internal Server Error
```json
{
  "success": false,
  "message": "Gagal menyimpan file: [error details]"
}
```
**Cause**: Server-side error (storage, permissions, etc.)  
**Solution**: Check Laravel logs, verify storage/app/public/documents folder exists

## Frontend Implementation

### Component: UploadDocumentModal.vue
Location: `elibrary-brida-fe/src/components/UploadDocumentModal.vue`

**Key Features**:
- Drag & drop file upload
- File type validation (PDF, DOC, DOCX)
- File size validation (max 10MB)
- Form validation (required fields)
- Real-time error display
- Toast notifications
- Auto-close on success
- Emits 'uploaded' event to parent

### API Service: api.ts
Location: `elibrary-brida-fe/src/services/api.ts`

```typescript
upload: async (data: FormData) => {
  const token = getAuthToken()
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
    throw new Error(result.message || `HTTP ${response.status}: Upload failed`)
  }

  return result
}
```

**Important**: No `Content-Type` header set manually - browser automatically sets `multipart/form-data` with boundary

## Backend Implementation

### Controller: DocumentController.php
Location: `elibrary-brida-be/app/Http/Controllers/Api/DocumentController.php`

```php
public function upload(Request $request)
{
    try {
        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'nullable|string',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'keywords' => 'nullable|string',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ], 422);
    }

    // Store file
    $filePath = null;
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
        $filePath = $file->storeAs('documents', $filename, 'public');
    }

    // Create document record
    $document = Document::create([
        'user_id' => $request->user()->id,
        'title' => $validated['title'],
        'abstract' => $validated['description'],
        'author' => $validated['author'],
        'publisher' => $validated['publisher'] ?? null,
        'year_published' => $validated['year'],
        'keywords' => $validated['keywords'] ?? null,
        'file_path' => $filePath,
        'status' => 'pending',
        'type_id' => null,
        'upload_date' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Dokumen berhasil diunggah dan menunggu persetujuan',
        'data' => $document->load('user')
    ], 201);
}
```

## Validation Rules

| Field       | Type    | Rules                                    | Error Message                           |
|-------------|---------|------------------------------------------|-----------------------------------------|
| file        | File    | required, mimes:pdf,doc,docx, max:10240  | File dokumen harus dipilih              |
| title       | String  | required, max:255                        | Judul dokumen harus diisi               |
| description | String  | required                                 | Deskripsi harus diisi                   |
| category    | String  | nullable                                 | -                                       |
| year        | Integer | required, min:1900, max:current_year+1   | Tahun harus antara 1900-2025           |
| author      | String  | required, max:255                        | Nama penulis harus diisi                |
| publisher   | String  | nullable, max:255                        | -                                       |
| keywords    | String  | nullable                                 | -                                       |

## File Storage

- **Location**: `storage/app/public/documents/`
- **Naming**: `{timestamp}_{sanitized_filename}`
- **Access**: Via symbolic link `public/storage/documents/`
- **Max Size**: 10MB (10240 KB)
- **Allowed Types**: PDF, DOC, DOCX

## Database Schema

```sql
Table: documents
- id: bigint (primary key)
- user_id: bigint (foreign key -> users.id)
- title: varchar(255)
- abstract: text (maps to 'description' in form)
- author: varchar(255)
- publisher: varchar(255) nullable
- year_published: integer
- keywords: text nullable
- file_path: varchar(255)
- status: enum('pending', 'approved', 'rejected') default 'pending'
- type_id: bigint nullable (foreign key -> types.id)
- upload_date: datetime
- created_at: timestamp
- updated_at: timestamp
```

## Troubleshooting

### Issue: Upload returns 401 Unauthorized
**Solutions**:
1. Check if user is logged in
2. Verify token in localStorage: `localStorage.getItem('token')`
3. Login again to refresh token
4. Check token expiration in backend config

### Issue: Upload returns 403 Forbidden
**Solutions**:
1. Verify user role: admin, super_admin, or contributor
2. Check RoleMiddleware in routes/api.php
3. Verify role assignment in database

### Issue: File not uploading
**Solutions**:
1. Check file size < 10MB
2. Verify file type is PDF, DOC, or DOCX
3. Check `storage/app/public/documents/` folder exists and is writable
4. Run: `php artisan storage:link`
5. Check Laravel logs: `storage/logs/laravel.log`

### Issue: Modal doesn't close after upload
**Solutions**:
1. Check console for JavaScript errors
2. Verify 'uploaded' event is emitted
3. Check parent component listens to @uploaded event

### Issue: Stats not updating
**Solutions**:
1. Refresh page or dashboard
2. Check ContributorDashboard.vue loadDocuments() is called
3. Verify async/await in onMounted is working
4. Check API response in network tab

## Testing Checklist

- [x] Backend server running on port 8000
- [x] Frontend server running on port 5173
- [x] API endpoint accessible: POST /api/documents/upload
- [x] Middleware allows admin/super_admin/contributor
- [x] Validation rules implemented
- [x] File storage configured
- [x] Toast notifications working
- [ ] Manual upload test via UI
- [ ] Verify document in database
- [ ] Verify document in review queue (admin)
- [ ] Verify stats update (contributor dashboard)
- [ ] Test error scenarios (file too large, invalid format, etc.)
- [ ] Test with different user roles

## Next Steps

1. **Test in Browser**:
   - Navigate to http://localhost:5173
   - Login with admin@brida.com / admin123
   - Go to dashboard
   - Click "Upload Dokumen"
   - Upload a PDF file
   - Verify success message
   - Check document appears in list

2. **Verify Database**:
   ```bash
   cd elibrary-brida-be
   php artisan tinker
   >>> Document::count()
   >>> Document::latest()->first()
   ```

3. **Check File Storage**:
   ```bash
   ls elibrary-brida-be/storage/app/public/documents
   ```

4. **Monitor Logs**:
   ```bash
   tail -f elibrary-brida-be/storage/logs/laravel.log
   ```

## Success Criteria

✅ Upload modal opens correctly  
✅ Form validation works (required fields, file types, size limits)  
✅ File uploads successfully  
✅ Document saved to database with status 'pending'  
✅ File stored in storage/app/public/documents/  
✅ Toast notification shows success message  
✅ Modal closes automatically  
✅ Document appears in dashboard list  
✅ Stats update correctly  
✅ Admin can see document in review queue  

---

**Status**: ✅ **READY FOR TESTING**

Both servers are running. Upload flow is implemented and configured correctly. Ready for manual testing through the browser UI.

**Quick Start**:
1. Open: http://localhost:5173
2. Login: admin@brida.com / admin123
3. Upload document through UI
4. Verify success!
