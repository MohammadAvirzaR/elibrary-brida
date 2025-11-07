# Roles Management - E-Library BRIDA

## Overview
Halaman Roles Management adalah fitur untuk Super Admin mengelola role dan permissions dalam sistem E-Library BRIDA. Halaman ini memungkinkan Super Admin untuk membuat, mengedit, dan menghapus role serta mengatur privilege/permissions untuk setiap role.

## Access Control
- **Route**: `/roles`
- **Required Role**: `super_admin` (Only Super Admin can access this page)
- **Location**: Dashboard Menu → Roles

## Features

### 1. **View Roles Table**
Menampilkan daftar semua role yang ada dalam sistem dengan informasi:
- Name: Nama role
- Description: Deskripsi role dan tanggung jawabnya
- Actions: Edit dan Delete buttons

### 2. **Search Functionality**
- Search bar untuk mencari role berdasarkan name atau description
- Real-time filtering

### 3. **Bulk Selection & Delete**
- Checkbox untuk memilih multiple roles
- "Select All" untuk memilih semua role di halaman saat ini
- Bulk delete untuk menghapus beberapa role sekaligus
- Counter menampilkan jumlah role yang dipilih

### 4. **Add New Role**
Klik tombol "Tambah" untuk membuat role baru:
- **Role Name**: Nama role (required)
- **Description**: Deskripsi role (required)
- **Permissions**: Pilih satu atau lebih permissions dari daftar yang tersedia

#### Available Permissions:
1. **Kelola User**: Dapat membuat, mengedit, dan menghapus user
2. **Kelola Roles**: Dapat mengelola role dan permissions
3. **Upload Dokumen**: Dapat mengunggah dokumen baru ke sistem
4. **Review Dokumen**: Dapat mereview dan menyetujui dokumen
5. **Approve Dokumen**: Dapat menyetujui atau menolak dokumen
6. **Hapus Dokumen**: Dapat menghapus dokumen dari sistem
7. **Lihat Analytics**: Dapat melihat analytics dan statistik sistem
8. **Manage Kategori**: Dapat mengelola kategori dokumen

### 5. **Edit Role**
Klik icon pencil (✏️) untuk mengedit role:
- Update nama role
- Update deskripsi
- Ubah permissions yang diberikan

### 6. **Delete Role**
Klik icon trash (🗑️) untuk menghapus role:
- Konfirmasi modal akan muncul
- Tindakan tidak dapat dibatalkan

### 7. **Pagination**
- Menampilkan 5 role per halaman
- Navigasi Previous/Next
- Direct page navigation
- Page indicator

## Default Roles

### 1. Super Admin
- **Description**: Pengelola, Reviewer, Admin Utama
- **Permissions**: ALL (Full access to all features)

### 2. Admin
- **Description**: Pengelola, Reviewer
- **Permissions**: 
  - Kelola User
  - Upload Dokumen
  - Review Dokumen
  - Approve Dokumen
  - Lihat Analytics

### 3. Kontributor
- **Description**: Peng-upload, Verified Viewer
- **Permissions**: 
  - Upload Dokumen

### 4. Guest
- **Description**: Viewer Tamu
- **Permissions**: None (Read-only access)

## User Interface

### Design Elements:
- **Modern & Clean**: Minimalist design dengan white background
- **Professional Table**: Proper spacing dan hover effects
- **Color Scheme**:
  - Primary: Black (buttons)
  - Secondary: Orange (edit icon)
  - Danger: Red (delete icon, delete button)
  - Border: Gray-200
- **Icons**: Lucide icons untuk konsistensi
- **Responsive**: Adaptive untuk berbagai ukuran layar

### Modal Design:
- **Add/Edit Modal**: 
  - Full form untuk nama, deskripsi, dan permissions
  - Checkbox list untuk permissions dengan descriptions
  - Cancel & Submit buttons
  
- **Delete Confirmation Modal**:
  - Warning icon
  - Confirmation message
  - Cancel & Delete buttons

## Integration Notes

### Frontend
- **File**: `elibrary-brida-fe/src/pages/dashboard/RolesView.vue`
- **Framework**: Vue 3 Composition API + TypeScript
- **Styling**: Tailwind CSS
- **Icons**: Lucide icons via unplugin-icons
- **State Management**: Local reactive state with ref()

### Backend Integration (TODO)
Untuk integrasi dengan backend Laravel, Anda perlu:

1. **API Endpoints**:
   ```
   GET    /api/roles           - Fetch all roles
   POST   /api/roles           - Create new role
   GET    /api/roles/:id       - Get single role
   PUT    /api/roles/:id       - Update role
   DELETE /api/roles/:id       - Delete role
   DELETE /api/roles/bulk      - Bulk delete roles
   ```

2. **Database Schema**:
   ```sql
   -- roles table
   CREATE TABLE roles (
     id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     name VARCHAR(255) NOT NULL,
     description TEXT,
     created_at TIMESTAMP NULL,
     updated_at TIMESTAMP NULL
   );

   -- permissions table
   CREATE TABLE permissions (
     id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     key VARCHAR(255) NOT NULL UNIQUE,
     label VARCHAR(255) NOT NULL,
     description TEXT,
     created_at TIMESTAMP NULL,
     updated_at TIMESTAMP NULL
   );

   -- role_permission pivot table
   CREATE TABLE role_permission (
     role_id BIGINT UNSIGNED NOT NULL,
     permission_id BIGINT UNSIGNED NOT NULL,
     PRIMARY KEY (role_id, permission_id),
     FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
     FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
   );
   ```

3. **Laravel Models**:
   ```php
   // Role.php
   class Role extends Model {
     protected $fillable = ['name', 'description'];
     
     public function permissions() {
       return $this->belongsToMany(Permission::class);
     }
   }

   // Permission.php
   class Permission extends Model {
     protected $fillable = ['key', 'label', 'description'];
     
     public function roles() {
       return $this->belongsToMany(Role::class);
     }
   }
   ```

4. **Controller Example**:
   ```php
   // RoleController.php
   public function index() {
     return Role::with('permissions')->get();
   }

   public function store(Request $request) {
     $validated = $request->validate([
       'name' => 'required|string|max:255',
       'description' => 'required|string',
       'permissions' => 'array'
     ]);

     $role = Role::create([
       'name' => $validated['name'],
       'description' => $validated['description']
     ]);

     if (isset($validated['permissions'])) {
       $role->permissions()->sync($validated['permissions']);
     }

     return $role->load('permissions');
   }
   ```

## Usage Flow

1. **Login as Super Admin**
   - Email: `admin@brida.com`
   - Password: `admin123`

2. **Navigate to Roles Page**
   - Click "Role" menu di sidebar
   - Atau akses langsung ke `/roles`

3. **Manage Roles**
   - View: Lihat semua role dalam tabel
   - Search: Cari role tertentu
   - Add: Tambah role baru dengan permissions
   - Edit: Update role existing
   - Delete: Hapus role yang tidak diperlukan

4. **Assign Permissions**
   - Buka Add/Edit modal
   - Centang permissions yang ingin diberikan
   - Save perubahan

## Security Considerations

1. **Route Protection**: 
   - Only `super_admin` role can access
   - Navigation guard akan redirect ke `/unauthorized` jika user lain mencoba akses

2. **Delete Protection**:
   - Konfirmasi modal sebelum delete
   - Tidak bisa delete role yang sedang digunakan (TODO: backend validation)

3. **Audit Trail** (TODO):
   - Log semua perubahan role
   - Track siapa yang membuat/mengubah/menghapus role

## Future Enhancements

1. **Backend Integration**: Connect dengan Laravel API
2. **Advanced Permissions**: Hierarchical permissions
3. **Role Templates**: Pre-defined role templates
4. **Usage Stats**: Show berapa user menggunakan setiap role
5. **Export/Import**: Export role configurations
6. **Audit Logs**: Complete audit trail
7. **Permission Groups**: Group permissions by category
8. **Custom Permissions**: Allow creating custom permissions

## Testing

### Manual Testing Checklist:
- [ ] Super admin dapat mengakses halaman
- [ ] Non-super admin tidak dapat mengakses
- [ ] Search functionality bekerja
- [ ] Add role modal membuka dengan benar
- [ ] Form validation bekerja
- [ ] Edit role memuat data dengan benar
- [ ] Delete confirmation muncul
- [ ] Bulk selection bekerja
- [ ] Pagination berfungsi
- [ ] Responsive di mobile

## Support

Untuk pertanyaan atau issue terkait Roles Management:
- Check dokumentasi di `ROLE_PERMISSIONS.md`
- Review code di `src/pages/dashboard/RolesView.vue`
- Check router configuration di `src/router/index.ts`

---

**Last Updated**: November 7, 2025
**Version**: 1.0.0
**Author**: Development Team
