# Panduan Menggunakan Postman Collection E-Library BRIDA

## 📋 Daftar Isi
- [Cara Import Collection](#cara-import-collection)
- [Struktur Collection](#struktur-collection)
- [Panduan Testing](#panduan-testing)
- [Catatan Penting](#catatan-penting)

## 🚀 Cara Import Collection

### Step 1: Buka Postman
Pastikan Postman sudah terinstal di komputer Anda. [Download Postman](https://www.postman.com/downloads/)

### Step 2: Import Collection
1. Klik tombol **Import** di bagian kiri atas Postman
2. Pilih **File** atau **Folder**
3. Cari dan pilih file `E-Library-BRIDA-API.postman_collection.json`
4. Klik **Import**

### Step 3: Import Environment
1. Klik ikon **Environments** di sidebar (kiri)
2. Klik tombol **Import**
3. Cari dan pilih file `E-Library-BRIDA-Environment.postman_environment.json`
4. Klik **Import**

### Step 4: Pilih Environment
1. Di bagian kanan atas Postman, ada dropdown environment
2. Pilih **"E-Library BRIDA Environment"**

## 📁 Struktur Collection

Collection ini diorganisir dalam beberapa folder utama:

### 1. **Auth** (Autentikasi)
- `Register` - Mendaftar user baru
- `Resend OTP` - Mengirim ulang OTP
- `Verify OTP` - Memverifikasi OTP
- `Login` - Login ke sistem
- `Get Current User` - Mendapatkan data user yang login
- `Logout` - Logout dari sistem

### 2. **Documents** (Manajemen Dokumen)
- `Search Documents (Public)` - Pencarian dokumen (publik)
- `Get Featured Content (Public)` - Konten unggulan
- `View Document (Public)` - Lihat detail dokumen
- `Download Document File (Public)` - Download file dokumen
- `List All Documents` - Daftar semua dokumen (authenticated)
- `My Documents` - Dokumen milik saya
- `Upload Document` - Upload dokumen baru
- `Update Document` - Edit dokumen
- `Delete Document` - Hapus dokumen
- `Document Preview` - Preview dokumen

### 3. **Contributor Requests** (Permintaan Kontributor)
- `Create Contributor Request` - Buat permintaan kontributor
- `Check Pending Request` - Cek permintaan yang menunggu
- `List All Requests` - Daftar semua permintaan (Super Admin)
- `Approve Request` - Setujui permintaan
- `Reject Request` - Tolak permintaan

### 4. **Download Requests** (Permintaan Download)
- `Create Download Request` - Buat permintaan download
- `List Download Requests` - Daftar permintaan download
- `Send Download Link` - Kirim link download
- `Reject Download Request` - Tolak permintaan download

### 5. **Users Management** (Manajemen User - Admin Only)
- `List All Users` - Daftar user
- `Get User Details` - Detail user
- `Create User` - Buat user baru
- `Update User` - Edit user
- `Delete User` - Hapus user

### 6. **Admin Documents** (Manajemen Dokumen Admin)
- `Approve Document` - Setujui dokumen
- `Reject Document` - Tolak dokumen

### 7. **Filters & Filters** (Filter)
- `Get All Filters` - Dapatkan semua filter

### 8. **Roles & Permissions** (Role dan Permission - Super Admin Only)
- `List All Roles` - Daftar role
- `Create Role` - Buat role baru
- `Update Role` - Edit role
- `Delete Role` - Hapus role
- `Get All Permissions` - Daftar permission
- `Sync Permissions to Role` - Sinkronisasi permission ke role

### 9. **Statistics** (Statistik - Admin Only)
- `Get Statistics Dashboard` - Dashboard statistik

## 🧪 Panduan Testing

### Testing Authorization
1. **Pertama kali**: Buka folder **Auth**
2. Jalankan request **Register** dengan email baru
3. Jalankan request **Resend OTP** (periksa database untuk OTP)
4. Jalankan request **Verify OTP** dengan OTP yang benar
5. Jalankan request **Login** untuk mendapatkan token
6. Token akan otomatis disimpan di environment variable `token`

### Testing Public Endpoints
- Endpoints publik (tanpa auth) bisa ditest tanpa login:
  - Search Documents
  - Get Featured Content
  - View Document
  - Download File
  - Get Filters

### Testing Authenticated Endpoints
1. Login terlebih dahulu (lihat bagian Authorization di atas)
2. Token akan otomatis tersimpan
3. Jalankan endpoint yang memerlukan autentikasi
4. Semua request akan menggunakan token otomatis

### Testing Admin/Super Admin Endpoints
Pastikan user yang login memiliki role `admin` atau `super_admin`

## ⚙️ Variabel Environment

Collection menggunakan variabel environment berikut:

| Variabel | Default | Deskripsi |
|----------|---------|-----------|
| `baseUrl` | `http://127.0.0.1:8000` | URL base API |
| `token` | (kosong) | Bearer token dari login |
| `userId` | `1` | ID user untuk testing |
| `documentId` | `1` | ID dokumen untuk testing |
| `requestId` | `1` | ID request untuk testing |

Anda bisa mengubah nilai-nilai ini di Environment settings.

## 📝 Catatan Penting

### Mengubah Base URL
1. Klik **Environments** di sidebar
2. Pilih **"E-Library BRIDA Environment"**
3. Ubah nilai `baseUrl` sesuai server Anda:
   - Local: `http://127.0.0.1:8000`
   - Production: `https://api.elibrary-brida.com` (contoh)
4. Klik **Save**

### Rate Limiting
Beberapa endpoint memiliki rate limiting:
- Register: 3 per menit
- Resend OTP: 2 per menit
- Verify OTP: 5 per menit
- Login: 5 per menit

Tunggu jika Anda mendapat error rate limiting.

### Format File Upload
Untuk testing upload dokumen:
1. Buka request **Upload Document**
2. Di bagian Body, pilih **form-data**
3. Untuk field `file`, pilih tipe **File** dan pilih file PDF/dokumen Anda
4. Sesuaikan `title`, `description`, dan `category` sesuai kebutuhan

### Testing dengan Database Testing
Untuk testing OTP verification:
1. Register user baru
2. Cek database untuk OTP yang digenerate
3. Gunakan OTP tersebut di request Verify OTP

### Custom Tests
Setiap request sudah dilengkapi dengan test script. Anda bisa:
1. Edit test dengan klik tab **Tests** di bawah request
2. Tambahkan custom test sesuai kebutuhan
3. Hasil test akan tampil di tab **Test Results**

## 🆘 Troubleshooting

### Error: "Invalid token"
- Pastikan sudah login terlebih dahulu
- Periksa apakah token masih valid
- Coba login lagi untuk mendapatkan token baru

### Error: "Unauthorized"
- Endpoint memerlukan role tertentu
- Pastikan user sudah di-assign role yang tepat
- Contact admin untuk assign role

### Error: "404 Not Found"
- Periksa base URL sudah benar
- Pastikan endpoint path sudah sesuai
- Periksa ID resource yang digunakan sudah benar

### Error: "Rate limit exceeded"
- Tunggu beberapa saat sebelum melakukan request lagi
- Periksa rate limit rules di atas

## 📚 Referensi Tambahan

- [API Documentation](../AUTHENTICATION_GUIDE.md)
- [Laravel Documentation](https://laravel.com/docs)
- [Postman Documentation](https://learning.postman.com/)

---

**Dibuat dengan ❤️ untuk E-Library BRIDA**
