# Implementasi University Dropdown di Form Upload Dokumen

## 📋 Ringkasan

Kontributor sekarang bisa memilih universitas dari dropdown ketika upload dokumen. Data universitas otomatis terekam dan tertampil di statistik "Jumlah Penelitian per Instansi" di dashboard super admin.

## 🏗️ Arsitektur Perubahan

```
Frontend (Upload Form)
    ↓
    └─→ Dropdown Universitas (pilih saat upload)
        ↓
Backend (DocumentController)
    ↓
    └─→ Simpan university_id ke documents table
        ↓
StatisticsController
    ↓
    └─→ Query dari documents.university_id
        ↓
Dashboard Chart
    ↓
    └─→ "Jumlah Penelitian per Instansi" (Real-time)
```

## 📁 File yang Diubah/Dibuat

### Backend

1. **Migrations (Database)**
   - `2026_04_30_000002_add_university_id_to_documents_table.php` - Tambah kolom university_id ke documents table

2. **Models**
   - `app/Models/Document.php` - Tambah relationship `university()`

3. **Controllers**
   - `app/Http/Controllers/Api/UniversityController.php` - Baru, untuk get list universitas
   - `app/Http/Controllers/Api/DocumentController.php` - Update upload() untuk handle university_id
   - `app/Http/Controllers/Api/StatisticsController.php` - Update untuk prioritas documents.university_id

4. **Routes**
   - `routes/api.php` - Tambah route GET /api/universities

### Frontend

1. **API Service**
   - `src/services/api.ts` - Tambah universities API endpoint

2. **Components**
   - `src/components/UploadDocumentModal.vue` - Tambah university dropdown di step 1

## 🚀 Cara Kerja

### 1. Saat Kontributor Upload Dokumen

```
Step 1: Metadata
├─ Judul
├─ Bahasa
├─ Jenis Dokumen
├─ Penulis
├─ Tahun Terbit
├─ Subjek
└─ 🆕 Universitas ← DROPDOWN
```

### 2. Data Flow Saat Upload

```
Form Data
└─ university_id: 1 (contoh)
    ↓
DocumentController::upload()
    ↓
Document::create([
    'university_id' => $request->university_id,
    ...
])
    ↓
Tersimpan di documents.university_id
```

### 3. Statistik Dashboard

```
GET /api/statistics
    ↓
StatisticsController::index()
    ↓
Query documents.university_id
    ├─ Ambil dari documents table (prioritas)
    ├─ Fallback ke document_authors
    └─ Fallback ke document_supervisors
    ↓
Response
    ├─ institution_data: [...]
    │   ├─ institution: "UNIVERSITAS HALU OLEO"
    │   ├─ count: 15
    │   └─ ...
    ↓
Frontend Bar Chart
    └─ "Jumlah Penelitian per Instansi"
```

## 📊 Database Schema

### documents table
```sql
ALTER TABLE documents ADD COLUMN university_id BIGINT UNSIGNED NULLABLE;
ALTER TABLE documents ADD FOREIGN KEY (university_id) REFERENCES university(id) ON DELETE SET NULL;
```

### university table (existing)
```sql
CREATE TABLE university (
    id BIGINT UNSIGNED PRIMARY KEY,
    university_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🔗 Relationships

```
University (1) ──→ (Many) Document (Many) ──→ (1) User
                      └─→ DocumentAuthor
                      └─→ DocumentSupervisor
```

## 📝 Form Validation

### Backend Validation
```php
'university_id' => 'nullable|exists:university,id',
```

- Optional field
- Harus valid ID dari tabel university
- Jika null, akan tersimpan sebagai NULL

## 🧪 Testing

### 1. Test GET /api/universities
```bash
curl http://127.0.0.1:8000/api/universities
```

**Expected Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "UNIVERSITAS HALU OLEO"
    },
    {
      "id": 2,
      "name": "STIKES KARYA KESEHATAN KENDARI"
    },
    ...
  ]
}
```

### 2. Test Upload dengan University
```bash
curl -X POST http://127.0.0.1:8000/api/documents/upload \
  -H "Authorization: Bearer TOKEN" \
  -F "title=Test Document" \
  -F "language=id" \
  -F "type_id=1" \
  -F "university_id=1" \
  -F "subject_id=1" \
  -F "file=@test.pdf" \
  -F "authors[0][first_name]=John" \
  -F "authors[0][last_name]=Doe"
```

### 3. Test Statistics
```bash
curl http://127.0.0.1:8000/api/statistics \
  -H "Authorization: Bearer TOKEN"
```

**Expected:** institution_data akan menampilkan universitas berdasarkan documents.university_id

## 📈 Data Pipeline

```
Kontributor Upload
        ↓
Form dengan university_id
        ↓
DocumentController::upload()
        ↓
Document::create([university_id => X])
        ↓
documents table
        ↓
StatisticsController::index()
        ↓
SELECT COALESCE(u.university_name, ...) FROM documents
LEFT JOIN university u ON documents.university_id = u.id
WHERE documents.status = 'approved'
GROUP BY university_name
        ↓
Response API
        ↓
Frontend Chart Update
        ↓
Dashboard "Jumlah Penelitian per Instansi"
```

## ✨ Features

✅ Dropdown universitas di form upload
✅ Automatic university_id save
✅ Real-time statistics update
✅ Backward compatible (falls back ke document_authors jika null)
✅ Data terstruktur dan terorganisir
✅ Query optimize dengan JOIN
✅ Support TOP 10 universitas

## 🔄 Backward Compatibility

Jika dokumen lama tidak punya `university_id`:
1. StatisticsController cek documents.university_id
2. Jika kosong, fallback ke document_authors
3. Jika masih kosong, fallback ke document_supervisors

Sehingga data lama tetap terdisplay tanpa perlu migration data.

## 🛠️ Admin Panel (Optional Future Enhancement)

```
Super Admin Dashboard
    └─ University Management
        ├─ CRUD universitas
        ├─ Edit nama universitas
        ├─ Lihat statistik per universitas
        └─ Export data per universitas
```

## 📞 Troubleshooting

### Issue: Dropdown universitas kosong
**Solusi:** 
- Pastikan GET /api/universities berhasil
- Check universitas sudah di-seed
- Clear cache: `php artisan cache:clear`

### Issue: Statistik tidak update
**Solusi:**
- Pastikan document sudah di-approve (status = 'approved')
- Pastikan university_id ter-save dengan benar
- Run: `php artisan cache:clear`

### Issue: Dropdown tidak muncul di frontend
**Solusi:**
- Lihat browser console untuk error
- Pastikan api.universities.getAll() dipanggil di onMounted
- Check network tab apakah request ke /universities berhasil

## 📚 API Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/universities` | No | Get all universities |
| POST | `/api/documents/upload` | Yes | Upload dokumen dengan university_id |
| GET | `/api/statistics` | Yes | Get statistics dengan institution data |

## 🎯 Next Steps

1. ✅ Test upload dengan university dropdown
2. ✅ Verify data tersimpan di documents.university_id
3. ✅ Check statistics menampilkan data benar
4. Optional: Tambah filter by university di dashboard
5. Optional: Export statistics per university

---

**Last Updated:** 2026-04-30
**Status:** ✅ Production Ready
**Version:** 1.0
