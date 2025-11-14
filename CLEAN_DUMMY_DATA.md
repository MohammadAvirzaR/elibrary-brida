# Membersihkan Dummy Data dan Menggunakan Upload Real

## 📋 Status

✅ **Upload System Sudah Terintegrasi**
- Backend API: `/api/documents/upload` (contributor)
- Frontend Modal: `UploadDocumentModal.vue`
- Dashboard: `ContributorDashboard.vue`

## 🗑️ Cara Membersihkan Dummy Data

### Opsi 1: Menggunakan Seeder (Recommended)

```bash
cd elibrary-brida-be
php artisan db:seed --class=CleanDummyDataSeeder
```

### Opsi 2: Manual via MySQL

```sql
-- Login ke MySQL
mysql -u root -p

-- Pilih database
USE elibrary_brida;

-- Hapus semua dokumen dummy
TRUNCATE TABLE document_subject;
TRUNCATE TABLE documents;

-- Verifikasi
SELECT COUNT(*) as total_documents FROM documents;
-- Harusnya return 0
```

### Opsi 3: Fresh Migration (Hapus Semua Data)

⚠️ **WARNING: Ini akan menghapus SEMUA data termasuk users!**

```bash
cd elibrary-brida-be
php artisan migrate:fresh --seed
```

## ✅ Verifikasi Setelah Pembersihan

1. **Cek Database:**
```bash
php artisan tinker
```
```php
// Di Tinker
Document::count(); // Should return 0
```

2. **Test Upload:**
   - Login sebagai contributor
   - Buka `/contributor-dashboard`
   - Klik "Unggah Dokumen"
   - Upload file PDF/DOC
   - Cek di admin dashboard apakah muncul dengan status "pending"

## 🔄 Sistem Upload yang Sudah Aktif

### Backend (DocumentController.php)

- ✅ `POST /api/documents/upload` - Upload dokumen (contributor)
- ✅ `GET /api/documents` - Get user's documents
- ✅ `GET /api/documents/review` - Get pending documents (admin)
- ✅ `PUT /api/documents/{id}` - Update document status (approve/reject)
- ✅ `DELETE /api/documents/{id}` - Delete document

### Frontend (UploadDocumentModal.vue)

- ✅ Form validation
- ✅ File upload (PDF, DOC, DOCX max 10MB)
- ✅ Metadata input (title, author, year, keywords, etc.)
- ✅ API integration dengan FormData
- ✅ Auto-reload after upload

### File Storage

- ✅ Files disimpan di: `storage/app/public/documents/`
- ✅ Public access via: `php artisan storage:link`

## 📝 Catatan Penting

1. **Seeder telah dinonaktifkan:**
   - `DocumentsSeeder.php` tidak lagi dipanggil di `DatabaseSeeder.php`
   - `DocumentSubjectSeeder.php` tidak lagi dipanggil

2. **Upload Flow:**
   ```
   Contributor → Upload Form → API (status: pending) → Database → Admin Review
   ```

3. **Admin Workflow:**
   ```
   Admin Dashboard → Queue Review → Approve/Reject → Document Status Updated
   ```

## 🚀 Testing Upload

### 1. Buat User Contributor (jika belum ada)

```bash
php artisan tinker
```
```php
// Create test contributor
$contributorRole = Role::where('name', 'contributor')->first();
$user = User::create([
    'name' => 'Test Contributor',
    'email' => 'contributor@test.com',
    'password' => bcrypt('password'),
    'role_id' => $contributorRole->id
]);
```

### 2. Login sebagai Contributor

- Email: `contributor@test.com`
- Password: `password`

### 3. Upload Dokumen

- Dashboard → Upload Dokumen
- Pilih file PDF
- Isi metadata
- Submit

### 4. Verifikasi

- Login sebagai super_admin
- Cek Dashboard → Queue Review
- Dokumen harus muncul dengan status "Waiting"

## 🔧 Troubleshooting

### Upload gagal "File not found"
```bash
# Create storage symlink
cd elibrary-brida-be
php artisan storage:link
```

### Upload gagal "Unauthenticated"
- Pastikan token sudah ada di localStorage
- Pastikan middleware `auth:sanctum` aktif
- Cek header Authorization dalam request

### File terlalu besar
- Default max: 10MB
- Ubah di `php.ini`:
  ```ini
  upload_max_filesize = 20M
  post_max_size = 20M
  ```

## ✨ Next Steps

1. ✅ Hapus dummy data
2. ✅ Test upload real document
3. ✅ Verify admin approval workflow
4. ⏳ Add file download functionality
5. ⏳ Add document preview
6. ⏳ Add search/filter in contributor dashboard

---

**Last Updated:** November 14, 2025
**Status:** ✅ Production Ready - No Dummy Data
