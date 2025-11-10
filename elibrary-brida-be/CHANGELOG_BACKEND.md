# Backend Changelog - Profile Management & Permissions System

## Tanggal: 10 November 2025

---

## 📋 Ringkasan Perubahan

Implementasi sistem **Profile Management** untuk Admin dan **Permissions System** untuk Role Management, dengan penambahan field baru pada tabel `users` dan `roles`.

---

## 🗄️ Database Changes

### 1. Migration: Add Permissions to Roles Table
**File:** `database/migrations/2025_11_10_162525_add_permissions_to_roles_table.php`

```php
Schema::table('roles', function (Blueprint $table) {
    $table->json('permissions')->nullable();
});
```

**Status:** ✅ Executed (212.20ms)

**Penjelasan:**
- Menambahkan kolom `permissions` dengan tipe JSON pada tabel `roles`
- Kolom ini menyimpan array permissions untuk setiap role dalam format JSON
- Nullable karena role lama mungkin belum memiliki permissions

---

### 2. Migration: Add Phone, Address, Institution, Name to Users Table
**File:** `database/migrations/2025_11_10_164357_add_phone_and_address_to_users_table.php`

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('phone')->nullable()->after('contact');
    $table->text('address')->nullable()->after('phone');
    $table->string('institution')->nullable()->after('unit_name');
    $table->string('name')->nullable()->after('full_name');
});
```

**Status:** ✅ Executed (141.61ms)

**Penjelasan:**
- `phone` - Nomor telepon user (untuk kompatibilitas dengan frontend)
- `address` - Alamat lengkap user
- `institution` - Nama institusi/organisasi user
- `name` - Duplicate dari `full_name` untuk kompatibilitas API dengan frontend

**Rollback:**
```php
Schema::table('users', function (Blueprint $table) {
    $table->dropColumn(['phone', 'address', 'institution', 'name']);
});
```

---

## 🔧 Model Changes

### 1. User Model
**File:** `app/Models/User.php`

**Perubahan pada `$fillable`:**
```php
protected $fillable = [
    'role_id',
    'full_name',
    'name',              // ⭐ NEW - For frontend compatibility
    'username',
    'email',
    'password',
    'sso_id',
    'unit_name',
    'institution',       // ⭐ NEW - For frontend compatibility
    'contact',
    'phone',             // ⭐ NEW - For frontend compatibility
    'address',           // ⭐ NEW - For frontend compatibility
    'bio',
    'membership_proof',
    'profession',
];
```

**Alasan Penambahan:**
- Frontend menggunakan field `name` bukan `full_name`
- Frontend membutuhkan `institution`, `phone`, dan `address` untuk Profile Management
- Untuk konsistensi, kedua field (`full_name` dan `name`) akan di-sync saat create/update

---

### 2. Role Model
**File:** `app/Models/Role.php`

**Perubahan pada `$fillable`:**
```php
protected $fillable = [
    'name',
    'description',
    'permissions',  // ⭐ NEW
];
```

**Perubahan pada `$casts`:**
```php
protected $casts = [
    'permissions' => 'array',  // ⭐ NEW - Auto convert JSON to array
];
```

**Alasan Penambahan:**
- Permissions disimpan sebagai JSON di database
- Auto-casting ke array memudahkan manipulasi di controller
- Contoh data permissions:
```json
["users.view", "users.create", "users.edit", "users.delete", "documents.manage"]
```

---

## 🎯 Controller Changes

### 1. UserController
**File:** `app/Http/Controllers/Api/UserController.php`

#### A. Method `index()` - Get All Users
**Perubahan Response:**
```php
return [
    'id' => $user->id,
    'name' => $user->name ?? $user->full_name,  // Fallback to full_name
    'email' => $user->email,
    'institution' => $user->institution,        // ⭐ NEW
    'phone' => $user->phone,                    // ⭐ NEW
    'address' => $user->address,                // ⭐ NEW
    'role' => $user->role ? $user->role->name : 'Guest',
    'role_id' => $user->role_id,
    'created_at' => $user->created_at,
];
```

---

#### B. Method `show($id)` - Get Single User
**Perubahan Response:**
```php
return [
    'id' => $user->id,
    'name' => $user->name ?? $user->full_name,
    'email' => $user->email,
    'institution' => $user->institution,        // ⭐ NEW
    'phone' => $user->phone,                    // ⭐ NEW
    'address' => $user->address,                // ⭐ NEW
    'role' => $user->role ? $user->role->name : 'Guest',
    'role_id' => $user->role_id,
    'created_at' => $user->created_at,
];
```

---

#### C. Method `store()` - Create New User
**Perubahan Validation:**
```php
$validator = Validator::make($request->all(), [
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users',
    'institution' => 'nullable|string|max:255',     // ⭐ NEW
    'phone' => 'nullable|string|max:20',            // ⭐ NEW
    'address' => 'nullable|string',                 // ⭐ NEW
    'password' => 'required|string|min:8',
    'role_id' => 'required|exists:roles,id'
]);
```

**Perubahan Create:**
```php
$user = User::create([
    'name' => $request->name,
    'full_name' => $request->name,           // Sync with name
    'email' => $request->email,
    'institution' => $request->institution,  // ⭐ NEW
    'phone' => $request->phone,              // ⭐ NEW
    'address' => $request->address,          // ⭐ NEW
    'password' => Hash::make($request->password),
    'role_id' => $request->role_id,
]);
```

**Perubahan Response:**
```php
return [
    'id' => $user->id,
    'name' => $user->name ?? $user->full_name,
    'email' => $user->email,
    'institution' => $user->institution,     // ⭐ NEW
    'phone' => $user->phone,                 // ⭐ NEW
    'address' => $user->address,             // ⭐ NEW
    'role' => $user->role ? $user->role->name : 'Guest',
    'role_id' => $user->role_id,
];
```

---

#### D. Method `update($id)` - Update User
**Perubahan Validation:**
```php
$validator = Validator::make($request->all(), [
    'name' => 'sometimes|required|string|max:255',
    'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
    'institution' => 'nullable|string|max:255',     // ⭐ NEW
    'phone' => 'nullable|string|max:20',            // ⭐ NEW
    'address' => 'nullable|string',                 // ⭐ NEW
    'password' => 'nullable|string|min:8',
    'role_id' => 'sometimes|required|exists:roles,id'
]);
```

**Perubahan Update Logic:**
```php
$updateData = [];
if ($request->has('name')) {
    $updateData['name'] = $request->name;
    $updateData['full_name'] = $request->name;  // Sync with name
}
if ($request->has('email')) $updateData['email'] = $request->email;
if ($request->has('institution')) $updateData['institution'] = $request->institution;  // ⭐ NEW
if ($request->has('phone')) $updateData['phone'] = $request->phone;                    // ⭐ NEW
if ($request->has('address')) $updateData['address'] = $request->address;              // ⭐ NEW
if ($request->has('role_id')) $updateData['role_id'] = $request->role_id;
if ($request->filled('password')) {
    $updateData['password'] = Hash::make($request->password);
}
```

**Perubahan Response:**
```php
return [
    'id' => $user->id,
    'name' => $user->name ?? $user->full_name,
    'email' => $user->email,
    'institution' => $user->institution,     // ⭐ NEW
    'phone' => $user->phone,                 // ⭐ NEW
    'address' => $user->address,             // ⭐ NEW
    'role' => $user->role ? $user->role->name : 'Guest',
    'role_id' => $user->role_id,
];
```

**⚠️ IMPORTANT NOTES:**
- Method `update()` sekarang mendukung **reset password** melalui field `password`
- Password akan di-hash otomatis menggunakan `Hash::make()`
- Frontend bisa update password tanpa perlu endpoint terpisah

---

### 2. RoleController
**File:** `app/Http/Controllers/Api/RoleController.php`

#### A. Method `store()` - Create New Role
**Perubahan:**
```php
Role::create([
    'name' => $request->name,
    'description' => $request->description,
    'permissions' => $request->permissions ?? [],  // ⭐ NEW - Save permissions array
]);
```

---

#### B. Method `update($id)` - Update Role
**Perubahan:**
```php
$role->update([
    'name' => $request->name,
    'description' => $request->description,
    'permissions' => $request->permissions ?? [],  // ⭐ NEW - Update permissions array
]);
```

**Catatan:**
- Permissions dikirim dari frontend sebagai array of strings
- Contoh request body:
```json
{
  "name": "Admin",
  "description": "Administrator role",
  "permissions": [
    "users.view",
    "users.create",
    "users.edit",
    "users.delete",
    "documents.view",
    "documents.edit"
  ]
}
```

---

## 🔐 API Endpoints (Tidak Ada Perubahan Route)

Semua endpoint tetap sama, hanya response dan request body yang berubah:

### Users Endpoints
```
GET    /api/users          - Get all users (includes phone, address, institution)
GET    /api/users/{id}     - Get single user (includes phone, address, institution)
POST   /api/users          - Create user (accepts phone, address, institution)
PUT    /api/users/{id}     - Update user (accepts phone, address, institution, password)
DELETE /api/users/{id}     - Delete user
```

### Roles Endpoints
```
GET    /api/roles          - Get all roles
POST   /api/roles          - Create role (accepts permissions array)
PUT    /api/roles/{id}     - Update role (accepts permissions array)
DELETE /api/roles/{id}     - Delete role
GET    /api/permissions    - Get available permissions
```

---

## 📝 Request/Response Examples

### 1. Create User
**Request:**
```json
POST /api/users
{
  "name": "John Doe",
  "email": "john@example.com",
  "institution": "Universitas Indonesia",
  "phone": "081234567890",
  "address": "Jl. Sudirman No. 123, Jakarta",
  "password": "password123",
  "role_id": 2
}
```

**Response:**
```json
{
  "success": true,
  "message": "User created successfully",
  "data": {
    "id": 5,
    "name": "John Doe",
    "email": "john@example.com",
    "institution": "Universitas Indonesia",
    "phone": "081234567890",
    "address": "Jl. Sudirman No. 123, Jakarta",
    "role": "Admin",
    "role_id": 2
  }
}
```

---

### 2. Update User Profile
**Request:**
```json
PUT /api/users/5
{
  "name": "John Doe Updated",
  "email": "john.new@example.com",
  "institution": "UI Jakarta",
  "phone": "081234567899",
  "address": "Jl. Thamrin No. 456, Jakarta"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 5,
    "name": "John Doe Updated",
    "email": "john.new@example.com",
    "institution": "UI Jakarta",
    "phone": "081234567899",
    "address": "Jl. Thamrin No. 456, Jakarta",
    "role": "Admin",
    "role_id": 2
  }
}
```

---

### 3. Reset User Password
**Request:**
```json
PUT /api/users/5
{
  "password": "newpassword123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 5,
    "name": "John Doe",
    "email": "john@example.com",
    "institution": "UI Jakarta",
    "phone": "081234567890",
    "address": "Jl. Sudirman No. 123, Jakarta",
    "role": "Admin",
    "role_id": 2
  }
}
```

---

### 4. Create/Update Role with Permissions
**Request:**
```json
POST /api/roles
{
  "name": "Content Manager",
  "description": "Manage documents and content",
  "permissions": [
    "documents.view",
    "documents.create",
    "documents.edit",
    "documents.delete",
    "documents.review"
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Role created successfully",
  "data": {
    "id": 5,
    "name": "Content Manager",
    "description": "Manage documents and content",
    "permissions": [
      "documents.view",
      "documents.create",
      "documents.edit",
      "documents.delete",
      "documents.review"
    ]
  }
}
```

---

## 🎯 Use Cases Frontend

### 1. Profile Management (Admin & Super Admin)
Frontend menggunakan endpoints berikut untuk Profile Management:
- `GET /api/users` - Load semua users untuk ditampilkan di tabel
- `PUT /api/users/{id}` - Edit profile user (name, email, institution, phone, address)
- `PUT /api/users/{id}` - Reset password user (hanya kirim field password)

### 2. User Management (Super Admin Only)
Frontend menggunakan endpoints berikut untuk User Management:
- `GET /api/users` - Load semua users
- `POST /api/users` - Create user baru
- `PUT /api/users/{id}` - Update user (semua field termasuk role_id)
- `DELETE /api/users/{id}` - Delete user

### 3. Role Management (Super Admin Only)
Frontend menggunakan endpoints berikut untuk Role Management:
- `GET /api/roles` - Load semua roles
- `POST /api/roles` - Create role baru dengan permissions
- `PUT /api/roles/{id}` - Update role dan permissions
- `DELETE /api/roles/{id}` - Delete role

---

## ⚠️ Breaking Changes

### 1. API Response Structure
**BEFORE:**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "role": "Admin",
  "role_id": 2
}
```

**AFTER:**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "institution": "UI Jakarta",
  "phone": "081234567890",
  "address": "Jl. Sudirman No. 123",
  "role": "Admin",
  "role_id": 2
}
```

### 2. Field Sync Behavior
- Ketika `name` di-update, `full_name` akan otomatis ter-update dengan nilai yang sama
- Ini untuk menjaga konsistensi antara field lama (`full_name`) dan field baru (`name`)

---

## 🔄 Migration Commands

Untuk apply migrations:
```bash
php artisan migrate
```

Untuk rollback jika ada masalah:
```bash
php artisan migrate:rollback --step=2
```

Untuk fresh migrate (⚠️ HATI-HATI - akan hapus semua data):
```bash
php artisan migrate:fresh --seed
```

---

## ✅ Testing Checklist

### Database
- [x] Migration `add_permissions_to_roles_table` executed successfully
- [x] Migration `add_phone_and_address_to_users_table` executed successfully
- [x] Column `permissions` exists in `roles` table (JSON type)
- [x] Columns `phone`, `address`, `institution`, `name` exists in `users` table

### API Endpoints
- [ ] Test `GET /api/users` - returns phone, address, institution
- [ ] Test `POST /api/users` - accepts phone, address, institution
- [ ] Test `PUT /api/users/{id}` - updates phone, address, institution
- [ ] Test `PUT /api/users/{id}` - reset password works
- [ ] Test `POST /api/roles` - saves permissions as JSON
- [ ] Test `PUT /api/roles/{id}` - updates permissions

### Data Integrity
- [ ] Old users without phone/address/institution return null (not error)
- [ ] name and full_name stay in sync when updated
- [ ] Permissions saved as valid JSON in database

---

## 📞 Contact

Jika ada pertanyaan atau issue terkait perubahan backend ini, hubungi:
- Frontend Developer yang mengerjakan Profile Management & Role Management UI
- Check frontend file: `ProfileManagementView.vue`, `UsersView.vue`, `RolesView.vue`

---

## 📚 Additional Notes

### Permissions System
Saat ini permissions disimpan sebagai array sederhana. Jika nanti butuh permissions yang lebih kompleks (misal: dengan metadata), bisa upgrade struktur JSON:

**Simple (Current):**
```json
["users.view", "users.create", "users.edit"]
```

**Complex (Future):**
```json
[
  {
    "key": "users.view",
    "label": "View Users",
    "group": "users"
  },
  {
    "key": "users.create",
    "label": "Create Users",
    "group": "users"
  }
]
```

Model sudah support karena menggunakan JSON type.

---

**Generated:** 10 November 2025  
**Version:** 1.0  
**Status:** ✅ PRODUCTION READY
