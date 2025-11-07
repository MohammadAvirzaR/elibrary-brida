# Users Management - E-Library BRIDA

## Overview
Halaman Users Management adalah fitur untuk Super Admin dan Admin mengelola pengguna dalam sistem E-Library BRIDA. Halaman ini memungkinkan untuk melihat, menambah, mengedit, dan menghapus user, serta **mengubah role** user yang sudah terdaftar.

## Access Control
- **Route**: `/users`
- **Required Roles**: `super_admin` dan `admin`
- **Location**: Dashboard Menu → User

## Features

### 1. **User List Table**
Menampilkan daftar semua user yang terdaftar dengan informasi:
- **Username**: Nama lengkap user
- **Email**: Alamat email user
- **Unit/Instansi**: Institusi/organisasi user
- **Role**: Role saat ini (Super Admin, Admin, Kontributor, Reviewer, Guest)
- **Actions**: View, Edit, Delete buttons

### 2. **Tabs Navigation**
- **Daftar Pengguna**: Tab untuk melihat list user existing
- **Pengguna Baru**: Tab untuk menambahkan user baru (coming soon)

### 3. **Search Functionality**
- Search bar untuk mencari user berdasarkan:
  - Username
  - Email
  - Unit/Instansi
  - Role
- Real-time filtering

### 4. **Bulk Selection & Delete**
- Checkbox untuk memilih multiple users
- "Select All" untuk memilih semua user di halaman saat ini
- Bulk delete untuk menghapus beberapa user sekaligus
- Counter menampilkan jumlah user yang dipilih

### 5. **Add New User**
Klik tombol "Tambah" untuk membuat user baru dengan field:
- **Nama Lengkap** (required)
- **Email** (required)
- **Unit/Instansi** (required)
- **Role** (required) - Dropdown dengan pilihan:
  - Super Admin
  - Admin
  - Kontributor
  - Reviewer
  - Guest
- **Password** (required untuk user baru)
- **Konfirmasi Password** (required untuk user baru)

### 6. **Edit User & Change Role** ⭐
Fitur utama untuk mengubah role user:
- Klik icon pencil (✏️) untuk edit user
- Form yang sama seperti Add User
- **Role dapat diubah** melalui dropdown
- Password fields tidak muncul saat edit (optional update)
- Super Admin dapat mengubah role user dari Guest → Kontributor, Admin, dll

### 7. **View User Details**
Klik icon mata (👁️) untuk melihat detail user:
- Nama Lengkap
- Email
- Unit/Instansi
- Role (ditampilkan dengan badge)
- Modal read-only

### 8. **Delete User**
Klik icon trash (🗑️) untuk menghapus user:
- Konfirmasi modal akan muncul
- Tindakan tidak dapat dibatalkan
- Bisa delete single user atau bulk delete

### 9. **Pagination**
- Menampilkan 5 user per halaman
- Navigasi Previous/Next
- Direct page navigation
- Page indicator

## User Interface

### Design Elements:
- **Layout**: Sama dengan Dashboard (sidebar biru + topbar)
- **Modern & Clean**: Professional table design
- **Color Scheme**:
  - Green: View icon (eye)
  - Orange: Edit icon (pencil)
  - Red: Delete icon (trash)
  - Black: Add button
  - Blue: Tab aktif, role badges
- **Icons**: Lucide icons
- **Responsive**: Adaptive layout

### Modals:
1. **Add/Edit User Modal**:
   - Form lengkap dengan validasi
   - Role dropdown untuk assign/change role
   - Password fields (only for new user)
   - Cancel & Submit buttons

2. **View User Modal**:
   - Display user information
   - Role badge
   - Close button

3. **Delete Confirmation Modal**:
   - Warning icon
   - Confirmation message
   - Cancel & Delete buttons

## Role Management Flow

### Scenario: Mengubah Role User dari Guest ke Admin

1. **Login sebagai Super Admin**
   - Akses halaman Users Management

2. **Cari User**
   - Gunakan search untuk menemukan user
   - Atau scroll melalui pagination

3. **Edit User**
   - Klik icon pencil (orange) di baris user
   - Modal Edit User akan terbuka

4. **Ubah Role**
   - Pilih role baru dari dropdown
   - Misal: Guest → Admin
   - Role options: Super Admin, Admin, Kontributor, Reviewer, Guest

5. **Simpan Perubahan**
   - Klik "Simpan Perubahan"
   - Role user akan terupdate
   - User akan mendapat permissions sesuai role baru

## Sample Data

### Default Users (for testing):
1. **Fahmi**
   - Email: fahmi@mail.com
   - Instansi: BRIDA Sulawesi Tenggara
   - Role: Super Admin

2. **Sedayu**
   - Email: sedayu@mail.com
   - Instansi: BRIDA Sulawesi Tenggara
   - Role: Admin

3. **Avirza**
   - Email: avirza@mail.com
   - Instansi: Universitas Gadjah Mada
   - Role: Kontributor

4. **Sounda**
   - Email: sounda@mail.com
   - Instansi: Toko Alat Tulis AJM
   - Role: Kontributor

5. **A**
   - Email: a@mail.com
   - Instansi: Bank BRI
   - Role: Kontributor

## Integration Notes

### Frontend
- **File**: `elibrary-brida-fe/src/pages/dashboard/UsersView.vue`
- **Framework**: Vue 3 Composition API + TypeScript
- **Styling**: Tailwind CSS
- **Icons**: Lucide icons
- **State**: Local reactive state

### Backend Integration (TODO)

1. **API Endpoints**:
   ```
   GET    /api/users              - Fetch all users
   POST   /api/users              - Create new user
   GET    /api/users/:id          - Get single user
   PUT    /api/users/:id          - Update user (including role change)
   DELETE /api/users/:id          - Delete user
   DELETE /api/users/bulk         - Bulk delete users
   PUT    /api/users/:id/role     - Change user role specifically
   ```

2. **Request/Response Examples**:

   **Update User with Role Change**:
   ```json
   PUT /api/users/3
   {
     "name": "Avirza",
     "email": "avirza@mail.com",
     "institution": "Universitas Gadjah Mada",
     "role_id": 2  // Changed from Guest (5) to Admin (2)
   }
   ```

   **Response**:
   ```json
   {
     "status": "success",
     "message": "User updated successfully",
     "user": {
       "id": 3,
       "name": "Avirza",
       "email": "avirza@mail.com",
       "institution": "Universitas Gadjah Mada",
       "role_id": 2,
       "role": "Admin"
     }
   }
   ```

3. **Database Schema Update**:
   ```sql
   -- Users already have role_id foreign key
   -- No schema change needed
   
   -- Just ensure role mapping
   -- 1 = Super Admin
   -- 2 = Admin
   -- 3 = Reviewer
   -- 4 = Kontributor
   -- 5 = Guest
   ```

4. **Laravel Controller Example**:
   ```php
   // UserController.php
   public function index() {
     return User::with('role')->get();
   }

   public function update(Request $request, $id) {
     $validated = $request->validate([
       'name' => 'required|string|max:255',
       'email' => 'required|email|unique:users,email,' . $id,
       'institution' => 'required|string',
       'role_id' => 'required|exists:roles,id'
     ]);

     $user = User::findOrFail($id);
     $user->update($validated);

     return response()->json([
       'status' => 'success',
       'message' => 'User updated successfully',
       'user' => $user->load('role')
     ]);
   }

   // Special endpoint for role change
   public function changeRole(Request $request, $id) {
     $validated = $request->validate([
       'role_id' => 'required|exists:roles,id'
     ]);

     $user = User::findOrFail($id);
     $oldRole = $user->role->name;
     
     $user->update(['role_id' => $validated['role_id']]);
     
     // Optional: Log role change
     ActivityLog::create([
       'user_id' => auth()->id(),
       'action' => 'role_changed',
       'description' => "Changed {$user->name}'s role from {$oldRole} to {$user->role->name}"
     ]);

     return response()->json([
       'status' => 'success',
       'message' => 'User role updated successfully',
       'user' => $user->load('role')
     ]);
   }
   ```

## Usage Flow

1. **Login as Super Admin/Admin**
   - Email: `admin@brida.com`
   - Password: `admin123`

2. **Navigate to Users Page**
   - Click menu "User" di sidebar
   - Atau akses langsung: `http://localhost:5173/users`

3. **View Users**
   - Lihat list semua registered users
   - Search untuk menemukan user tertentu

4. **Add New User**
   - Klik "Tambah"
   - Isi form
   - Pilih role yang sesuai
   - Submit

5. **Change User Role** (Main Feature)
   - Klik icon pencil di user yang ingin diubah
   - Update role melalui dropdown
   - Save changes
   - Role user langsung berubah

6. **View User Details**
   - Klik icon eye untuk lihat detail
   - Informasi ditampilkan dalam modal

7. **Delete User**
   - Klik icon trash
   - Konfirmasi penghapusan
   - User akan dihapus dari sistem

## Security Considerations

1. **Access Control**:
   - Only Super Admin and Admin can access
   - Route guard akan redirect unauthorized users

2. **Role Change Validation**:
   - Validate role exists di backend
   - Prevent circular role changes
   - Log all role changes for audit

3. **Deletion Protection**:
   - Konfirmasi sebelum delete
   - Prevent self-deletion
   - Cannot delete user yang sedang digunakan

4. **Password Security**:
   - Password hashing di backend
   - Minimum 8 characters
   - Confirmation match validation

## Future Enhancements

1. **Backend API Integration**: Connect dengan Laravel endpoints
2. **Real-time Updates**: WebSocket untuk live user updates
3. **Advanced Filters**: Filter by role, institution, registration date
4. **Bulk Role Change**: Change multiple users' role at once
5. **User Import/Export**: CSV/Excel import/export
6. **Activity Logs**: Track all user management actions
7. **Email Notifications**: Notify users when role changes
8. **Password Reset**: Allow admin to reset user passwords
9. **User Statistics**: Show user analytics and activity
10. **Profile Pictures**: Avatar upload for users

## Testing Checklist

### Manual Testing:
- [ ] Super admin dapat mengakses halaman
- [ ] Admin dapat mengakses halaman
- [ ] Non-admin/super-admin tidak dapat akses
- [ ] Search functionality bekerja
- [ ] Add user modal membuka dengan benar
- [ ] Form validation bekerja
- [ ] Edit user memuat data dengan benar
- [ ] **Role dapat diubah via dropdown** ✅
- [ ] View modal menampilkan detail user
- [ ] Delete confirmation muncul
- [ ] Bulk selection bekerja
- [ ] Pagination berfungsi
- [ ] Responsive di mobile

### Role Change Testing:
- [ ] Guest → Kontributor works
- [ ] Kontributor → Admin works
- [ ] Admin → Super Admin works (if allowed)
- [ ] Role dropdown shows all available roles
- [ ] Updated role reflects immediately
- [ ] User permissions change accordingly

## Support

Untuk pertanyaan atau issue terkait Users Management:
- Check dokumentasi di `ROLES_MANAGEMENT.md`
- Review code di `src/pages/dashboard/UsersView.vue`
- Check router configuration di `src/router/index.ts`

---

**Last Updated**: November 7, 2025
**Version**: 1.0.0
**Author**: Development Team
**Key Feature**: ⭐ Change User Role Management
