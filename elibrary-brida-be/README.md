# E-Library BRIDA Backend

Backend API untuk E-Library BRIDA menggunakan Laravel, Sanctum, dan Spatie Laravel Permission.

## Tujuan Dokumen

Dokumen ini adalah tutorial singkat untuk tim backend setelah migrasi RBAC ke Spatie Laravel Permission. Fokusnya bukan teori umum Laravel, tetapi aturan kerja yang sekarang berlaku di proyek ini.

## Ringkasan Perubahan Besar

Sebelum migrasi:
- User menyimpan role lewat kolom `users.role_id`
- Role menyimpan permission dalam JSON di tabel `roles`
- Banyak pengecekan akses dilakukan manual lewat relasi `role`

Sesudah migrasi:
- User memakai trait `HasRoles` dari Spatie
- Role memakai model `Spatie\Permission\Models\Role`
- Permission disimpan normalisasi di tabel `permissions` dan pivot Spatie
- Assignment role user disimpan di `model_has_roles`
- Assignment permission role disimpan di `role_has_permissions`
- Guard default backend adalah `api`

## Package dan Konfigurasi

Package yang dipakai:

```bash
composer require spatie/laravel-permission
```

Konfigurasi utama ada di:
- `config/permission.php`

Guard yang dipakai proyek ini:

```php
'guard_name' => 'api'
```

Aturan penting:
- Semua role dan permission yang dibuat backend harus memakai `guard_name = 'api'`
- Jangan campur guard `web` dan `api` untuk role/permission di proyek ini

## Struktur Database Baru

Tabel utama yang sekarang dipakai:
- `roles`
- `permissions`
- `model_has_roles`
- `model_has_permissions`
- `role_has_permissions`

Migrasi proyek yang terkait:
- `2026_03_11_170000_prepare_for_spatie_permissions.php`
- `2026_03_11_171228_create_permission_tables.php`
- `2026_03_11_172000_migrate_user_roles_to_spatie.php`

Yang dihapus dari sistem lama:
- JSON `permissions` pada tabel `roles`
- tabel `privilages`
- tabel `role_privilege`
- kolom `users.role_id`

## Model yang Berubah

### User

File:
- `app/Models/User.php`

Aturan sekarang:
- User memakai `Spatie\Permission\Traits\HasRoles`
- Role user tidak lagi dibaca dari relasi `belongsTo(Role::class)`
- Untuk kompatibilitas lama, tersedia accessor `role` yang mengembalikan role pertama dari koleksi Spatie

Contoh:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
		use HasRoles;

		protected $guard_name = 'api';
}
```

### Role

File:
- `app/Models/Role.php`

Aturan sekarang:
- Model role harus extend `Spatie\Permission\Models\Role`
- Field tambahan proyek: `description`

Contoh:

```php
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
		protected $fillable = ['name', 'guard_name', 'description'];
		protected $guard_name = 'api';
}
```

## Seeder Standar Proyek

Seeder utama:
- `database/seeders/SpatiePermissionSeeder.php`

Role default saat ini:
- `super_admin`
- `admin`
- `reviewer`
- `contributor`
- `guest`

Permission default saat ini:
- `manage_users`
- `manage_roles`
- `upload_documents`
- `review_documents`
- `approve_documents`
- `delete_documents`
- `view_analytics`
- `manage_categories`

Menjalankan seeder:

```bash
php artisan db:seed --class=SpatiePermissionSeeder
php artisan db:seed --class=UserSeeder
```

## Cara Menjalankan Setup dari Nol

Jika backend di-setup dari database kosong:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan config:clear
php artisan cache:clear
php artisan migrate --force
php artisan db:seed --class=SpatiePermissionSeeder
php artisan db:seed --class=UserSeeder
php artisan serve
```

Jika ingin jalankan semua seed dari DatabaseSeeder:

```bash
php artisan db:seed
```

## Troubleshooting

### Login 500 dan log menampilkan query ke tabel `cache`
Jika muncul error seperti:
- `select * from cache where key in (...)`
- atau `SQLSTATE[HY000] [1045] Access denied for user ...`

Pastikan `.env` backend mengarah ke database yang benar dan untuk environment local gunakan cache/session berbasis file:

```env
CACHE_STORE=file
SESSION_DRIVER=file
```

Lalu bersihkan config cache Laravel dan restart server:

```bash
php artisan optimize:clear
```

### Preview PDF tidak muncul (hanya fallback abstrak)
Preview 1–2 halaman pertama PDF membutuhkan dependency OS-level:
- PHP extension **Imagick**
- **Ghostscript** (untuk membaca PDF via Imagick)

Jika belum terpasang, endpoint preview akan fallback ke abstrak/metadata saja.

## Pola Coding Baru yang Wajib Dipakai

### 1. Assign role ke user

Jangan lagi set `role_id`.

Benar:

```php
$user->assignRole('guest');
$user->syncRoles(['admin']);
```

Salah:

```php
$user->role_id = 2;
```

### 2. Cek role user

Benar:

```php
$user->hasRole('super_admin');
$user->hasAnyRole(['admin', 'super_admin']);
```

Salah:

```php
$user->role->name === 'admin';
in_array($user->role, ['admin', 'super_admin']);
```

Catatan:
- `$user->role?->name` masih bisa dipakai untuk kompatibilitas response
- Untuk authorization, prioritaskan method Spatie seperti `hasRole()` dan `hasAnyRole()`

### 3. Cek permission user

```php
$user->hasPermissionTo('manage_users');
$user->can('manage_roles');
```

### 4. Assign permission ke role

```php
$role->givePermissionTo('manage_users');
$role->syncPermissions(['manage_users', 'manage_roles']);
```

### 5. Eager loading role user

Karena sekarang `role` bukan relasi Eloquent lama, jangan gunakan `with('role')`.

Benar:

```php
User::with('roles')->find($id);
Document::with('user.roles')->findOrFail($id);
```

Salah:

```php
User::with('role')->find($id);
Document::with('user.role')->findOrFail($id);
```

Alasan teknis:
- `with('role')` bisa bentrok dengan scope `role()` bawaan Spatie dan menyebabkan error argumen

## Middleware dan Routing

Middleware custom proyek:
- `app/Http/Middleware/RoleMiddleware.php`

Middleware ini sekarang memakai Spatie di dalamnya:

```php
if (!$user->hasRole($role)) {
		return response()->json(['message' => 'Forbidden'], 403);
}
```

Contoh pemakaian route:

```php
Route::middleware(RoleMiddleware::class . ':super_admin')->group(function () {
		Route::post('/roles', [RoleController::class, 'store']);
});
```

Contoh route yang sekarang tersedia untuk manajemen role:

```text
GET    /api/roles
POST   /api/roles
PUT    /api/roles/{id}
DELETE /api/roles/{id}
PUT    /api/roles/{id}/permissions
GET    /api/permissions
```

## Perubahan Endpoint Backend

### RoleController

File:
- `app/Http/Controllers/Api/RoleController.php`

Perilaku sekarang:
- `index()` mengembalikan role dengan daftar permission hasil `pluck('name')`
- `store()` membuat role baru dan optional sync permission
- `update()` update metadata role dan sync permission
- `syncPermissions()` dipakai frontend untuk update switch permission per role
- `permissions()` membaca permission dari tabel `permissions`, bukan hardcoded static list

Contoh response role:

```json
{
	"success": true,
	"data": [
		{
			"id": 1,
			"name": "admin",
			"description": "Administrator",
			"guard_name": "api",
			"permissions": ["manage_users", "view_analytics"]
		}
	]
}
```

### AuthController

File:
- `app/Http/Controllers/Api/AuthController.php`

Perubahan utama:
- registrasi user baru langsung `assignRole('guest')`
- login dan endpoint `me` sekarang membaca role dari `roles`

### UserController

File:
- `app/Http/Controllers/Api/UserController.php`

Perubahan utama:
- create user: buat user dulu, lalu assign role dengan Spatie
- update user: gunakan `syncRoles()` saat role berubah
- response tetap mempertahankan `role` dan `role_id` untuk kompatibilitas frontend

## Daftar Error yang Perlu Dihindari

### Error: Too few arguments to function App\Models\User::scopeRole()

Penyebab:
- masih ada query `with('role')` atau `with('user.role')`

Solusi:

```php
User::with('roles')
Document::with('user.roles')
```

### Error: role tidak terbaca setelah login

Penyebab:
- user dibuat tapi tidak diberi role default

Solusi:

```php
$user->assignRole('guest');
```

### Error: permission tidak muncul di frontend

Penyebab:
- tabel `permissions` belum di-seed
- frontend masih mengharapkan object statis lama

Solusi:
- jalankan `SpatiePermissionSeeder`
- pastikan endpoint `GET /api/permissions` mengembalikan array dari database

## Checklist Review untuk Tim Backend

Sebelum merge perubahan backend yang menyentuh authorization, cek ini:

- Tidak ada lagi assignment `role_id`
- Tidak ada lagi eager load `with('role')`
- Tidak ada lagi relasi lama `belongsTo(Role::class)` di User
- Semua role dan permission baru memakai `guard_name = 'api'`
- Controller memakai `hasRole()`, `hasAnyRole()`, atau `hasPermissionTo()`
- Seeder role/permission diperbarui jika ada capability baru
- Response API tetap kompatibel dengan frontend yang masih membaca `role` dan `role_id`

## Command Verifikasi Cepat

```bash
php artisan migrate:status
php artisan route:list --path=roles
php artisan route:list --path=permissions
php artisan db:seed --class=SpatiePermissionSeeder
php artisan tinker
```

Contoh cek di Tinker:

```php
$user = App\Models\User::first();
$user->getRoleNames();
$user->hasRole('guest');
$role = App\Models\Role::where('name', 'admin')->first();
$role->permissions->pluck('name');
```

## File yang Perlu Dipahami Tim Backend

- `app/Models/User.php`
- `app/Models/Role.php`
- `app/Http/Middleware/RoleMiddleware.php`
- `app/Http/Controllers/Api/AuthController.php`
- `app/Http/Controllers/Api/UserController.php`
- `app/Http/Controllers/Api/RoleController.php`
- `app/Http/Controllers/Api/DocumentController.php`
- `routes/api.php`
- `database/seeders/SpatiePermissionSeeder.php`

## Catatan Praktis

Sistem saat ini masih mempertahankan beberapa accessor kompatibilitas agar frontend lama tidak langsung rusak. Itu membantu transisi, tetapi untuk kode backend baru, anggap sumber kebenaran role dan permission sepenuhnya berasal dari Spatie.
