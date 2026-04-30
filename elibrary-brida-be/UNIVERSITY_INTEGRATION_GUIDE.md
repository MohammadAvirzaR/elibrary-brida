# Panduan Integrasi Database Universitas dengan Statistik Penelitian

## 📋 Ringkasan Implementasi

Integrasi ini menghubungkan tabel `university` (database universitas) dengan statistik "Jumlah Penelitian per Instansi" di dashboard super admin.

## 🗄️ Struktur Database

### Tabel yang Dimodifikasi:

**`document_authors` (tabel yang diupdate)**
```
- university_id (BIGINT, nullable, foreign key ke university.id)
- institution (STRING, nullable - tetap ada untuk backward compatibility)
```

**`university` (tabel existing)**
```
- id (BIGINT, primary key)
- university_name (STRING)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

## 🔗 Relationship

```
University (1) ──→ (Many) DocumentAuthor (Many) ──→ (1) Document
```

## 📦 File-File yang Dibuat/Dimodifikasi

### 1. **Baru Dibuat:**
- `app/Models/University.php` - Model untuk universitas
- `database/migrations/2026_04_30_000000_add_university_id_to_document_authors_table.php` - Migration
- `database/seeders/UniversitySeeder.php` - Seeder untuk data universitas

### 2. **Dimodifikasi:**
- `app/Models/DocumentAuthor.php` - Tambah relationship ke University
- `app/Http/Controllers/Api/StatisticsController.php` - Update query untuk join dengan university
- `database/seeders/DatabaseSeeder.php` - Tambah UniversitySeeder ke call list

## 🚀 Cara Implementasi

### Step 1: Run Migration
```bash
php artisan migrate
```

### Step 2: Jalankan Seeder (untuk seeding data universitas)
```bash
php artisan db:seed --class=UniversitySeeder
# ATAU jalankan semua seeder
php artisan db:seed
```

### Step 3: Update Document Authors dengan University ID
Anda perlu update data `document_authors` yang sudah ada dengan `university_id` yang sesuai berdasarkan nama institusi mereka.

**Contoh SQL Query:**
```sql
UPDATE document_authors da
SET university_id = (
    SELECT u.id FROM university u 
    WHERE u.university_name = da.institution 
    LIMIT 1
)
WHERE da.institution IS NOT NULL 
AND da.university_id IS NULL;
```

### Step 4: Test Endpoint
Buka Postman atau Browser:
```
GET http://localhost:8000/api/statistics
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "total_research": 1640,
    "brida_research": 5,
    "non_brida_research": 1635,
    "category_data": {
      "labels": ["BRIDA", "S2", "S1"],
      "values": [5, 800, 835]
    },
    "trend_data": {
      "series": [...]
    },
    "institution_data": {
      "institutions": [
        "UNIVERSITAS HALU OLEO",
        "STIKES KARYA KESEHATAN KENDARI",
        ...
      ],
      "counts": [1200, 800, ...]
    }
  }
}
```

## 🎯 Fitur yang Didapatkan

### Dashboard Super Admin - Chart "Jumlah Penelitian per Instansi"

Sekarang menampilkan:
- ✅ Data universitas dari database `university` (lebih terstruktur)
- ✅ Fallback ke field `institution` jika `university_id` kosong (backward compatible)
- ✅ Top 10 universitas dengan jumlah penelitian terbanyak
- ✅ Data real-time dari database

### Query yang Digunakan:

```php
$institutionData = DB::table('document_authors')
    ->leftJoin('university', 'document_authors.university_id', '=', 'university.id')
    ->join('documents', 'document_authors.document_id', '=', 'documents.id')
    ->where('documents.status', 'approved')
    ->selectRaw('COALESCE(university.university_name, document_authors.institution) as institution_name, COUNT(*) as total')
    ->groupBy('institution_name')
    ->orderBy('total', 'desc')
    ->limit(10)
    ->get();
```

**Cara Kerja Query:**
1. LEFT JOIN ke `university` table (agar bisa null jika belum ter-link)
2. INNER JOIN ke `documents` table (hanya approved documents)
3. COALESCE untuk fallback ke `institution` field jika `university_id` null
4. GROUP BY institusi dan COUNT jumlah penelitian
5. ORDER BY jumlah penelitian (DESC) dan LIMIT 10

## 📝 Menambah Universitas Baru

### Di Database:
```php
University::create([
    'university_name' => 'UNIVERSITAS BARU'
]);
```

### Di Seeder:
Edit `database/seeders/UniversitySeeder.php` dan tambah ke array `$universities`.

## 🔄 Backward Compatibility

- Field `institution` tetap ada dan berfungsi
- Jika `university_id` kosong, sistem akan pakai nilai dari `institution`
- Tidak ada breaking changes untuk existing data

## 📊 Dashboard Visualization

Bar Chart di DashboardView.vue menampilkan:
```
Jumlah Penelitian per Instansi

UNIVERSITAS HALU OLEO              ████████████ 1200
STIKES KARYA KESEHATAN KENDARI     ████████ 800
UNIVERSITAS SULAWESI TENGGARA      ██████ 600
INSTITUT TEKNOLOGI AVICENNA        █████ 400
UNIVERSITAS MUHAMMADIYAH KENDARI   ████ 350
UNIVERSITAS LAKIDENDE              ███ 300
...
```

## 🐛 Troubleshooting

### Issue: Statistics endpoint menampilkan data lama
**Solusi:** Pastikan migration sudah dijalankan dan document_authors sudah di-update dengan university_id

### Issue: University name tidak muncul di chart
**Solusi:** Pastikan nilai `institution` di document_authors match dengan `university_name` di tabel university

### Issue: Beberapa institusi tidak muncul
**Solusi:** Chart hanya menampilkan TOP 10. Untuk melihat semua, ubah `->limit(10)` di StatisticsController

## 💡 Next Steps (Optional Improvements)

1. **Tambah kolom ke University:**
   - `city` (kota universitas)
   - `province` (provinsi)
   - `abbreviation` (singkatan)

2. **Filter Chart:**
   - Filter by date range
   - Filter by university type

3. **Export Data:**
   - Export statistik ke Excel/PDF

4. **Admin Panel:**
   - CRUD universitas di admin panel (bukan hanya seed)

---

**Last Updated:** 2026-04-30
**Status:** ✅ Production Ready
