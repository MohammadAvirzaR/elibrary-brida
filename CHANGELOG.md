# Changelog - E-Library BRIDA

Semua perubahan penting pada proyek ini akan didokumentasikan di file ini.

---

## [1.5.0] - 2024-11-10

### ✨ Added - Role Change Notification System
- **Smart Role Change Detection**
  - Sistem deteksi perubahan role yang cerdas menggunakan `localStorage`
  - Hanya menampilkan popup ketika role **benar-benar berubah**
  - Tidak menampilkan popup setiap kali reload halaman
  - Tracking role menggunakan `last_known_role` di localStorage

- **Role Change Notification Component**
  - Popup notifikasi dengan gradient header (blue to purple)
  - Menampilkan perubahan role: "Old Role → New Role"
  - Tombol "Reload Page" untuk apply role baru
  - Tombol "Dismiss" untuk menutup notifikasi
  - Auto-hide setelah 15 detik dengan progress bar
  - Animasi slide-in/out yang smooth

- **Notification Logic**
  - **Login Pertama**: Menyimpan role sebagai `last_known_role`, tidak ada popup
  - **Login Berikutnya (Role Sama)**: Tidak ada popup
  - **Login Berikutnya (Role Berubah)**: Popup muncul, update `last_known_role`
  - **Super Admin**: Tidak perlu popup karena sudah di tingkat tertinggi
  - **In-Session Changes**: Deteksi setiap 10 detik jika admin mengubah role user yang sedang login

### 📁 Files Created
- `src/composables/useRoleChangeDetector.ts` - Composable untuk role change detection
- `src/components/RoleChangeNotification.vue` - Komponen notifikasi popup

### 🔧 Modified Files
- `src/App.vue` - Integrasi global RoleChangeNotification
- `src/pages/auth/LoginView.vue` - Set `last_known_role` saat login
- `src/pages/dashboard/UsersView.vue` - Trigger detection saat admin ubah role user
- `src/router/index.ts` - Update redirect logic, hapus WelcomeView route

### 🗑️ Removed - File Optimization
- **Duplicate Folders**
  - `elibrary-brida-fe/elibrary-brida-fe/` - Folder duplikat yang tidak diperlukan

- **Unused Views**
  - `src/views/HomeView.vue` - View tidak terpakai
  - `src/views/AboutView.vue` - View tidak terpakai
  - `src/views/` folder - Folder views kosong

- **Deprecated Documentation Files (Root)**
  - `VERIFICATION_CHECKLIST.md`
  - `USER_DASHBOARD_GUIDE.md`
  - `USERS_MANAGEMENT.md`
  - `TESTING_COMPLETE_FLOW.md`
  - `SYSTEM_CHECK_REPORT.md`
  - `SUPER_ADMIN_CREDENTIALS.md`
  - `ROLES_MANAGEMENT.md`
  - `BUG_REPORT_AND_FIXES.md`

- **Deprecated Documentation Files (Frontend)**
  - `elibrary-brida-fe/API_CLIENT_GUIDE.md`
  - `elibrary-brida-fe/API_INTEGRATION.md`
  - `elibrary-brida-fe/DASHBOARD_INTEGRATION.md`
  - `elibrary-brida-fe/INTEGRATION_STATUS.md`
  - `elibrary-brida-fe/QUICK_START.md`
  - `elibrary-brida-fe/ROLE_PERMISSIONS.md`

- **Deprecated Documentation Files (Backend)**
  - `elibrary-brida-be/TODO.md`

### 📝 Documentation
- **CHANGELOG.md** (new) - Single source of truth untuk semua update proyek
- **README.md** (existing) - Dokumentasi utama project setup
- **CREDENTIALS.md** (existing) - Credential akses sistem

---

## [1.4.0] - 2024-11-09

### ✨ Added - Discord-Style Role Management
- **Role Management View** (`/role-management`)
  - Two-panel layout: Role list (left) + Role details (right)
  - Drag & drop role hierarchy system
  - Color picker untuk customization role
  - Member count per role
  - Display options: Display separately, Allow anyone to @mention
  
- **Granular Permission System**
  - Grouped permissions by category:
    - General Permissions
    - Membership Permissions
    - Text Permissions
    - Voice Permissions
    - Apps Permissions
    - Events Permissions
    - Advanced Permissions
  - Individual permission toggles
  - Save & Cancel buttons

### 🔧 Modified
- `src/router/index.ts` - Added `/role-management` route

---

## [1.3.0] - 2024-11-08

### ✨ Added - User Dashboard Enhancements
- **Sticky Navbar di User Dashboard**
  - Logo e-Library BRIDA
  - Navigation menu: Beranda, Dokumen, Profil
  - Profile dropdown dengan user initials
  - Upload button untuk quick access
  - Beranda button untuk kembali ke landing page

### 🎨 Improved
- **Modal Scrollbar Removal**
  - Hapus scrollbar di semua modal/popup
  - Update `base.css` dengan global scrollbar hiding
  - Lebih clean dan modern UI

### 🐛 Fixed
- **Advanced Search Button**
  - Fixed handler function (`openAdvancedSearch` vs `toggleAdvancedSearch`)
  - Import `AdvancedSearchModal` component
  - Proper modal integration di SearchResultsView

---

## [1.2.0] - 2024-11-07

### ✨ Added - Flexible User Editing System
- **Optional Fields di Edit User**
  - Name: Optional (kecuali untuk create)
  - Email: Optional (kecuali untuk create)
  - Institution: Optional (kecuali untuk create)
  - Password: Optional dengan toggle "Ubah Password"
  - Role: Always required

- **Super Admin Protection**
  - Tidak bisa edit role super_admin
  - Tidak bisa hapus user super_admin
  - Alert jika mencoba edit/hapus super_admin

### 🔧 Modified
- `UsersView.vue` - Flexible validation logic
- Role dropdown - Select2 integration

---

## [1.1.0] - 2024-11-06

### 🐛 Fixed - Role Dropdown Bug
- **Backend Fixes**
  - `UserSeeder.php` - Changed from 'user' to 'guest' role
  - `RoleController.php` - Removed non-existent 'permissions' relation
  - `api.php` - Fixed API routes for roles

- **Frontend Fixes**
  - Role dropdown now populated correctly
  - API integration for roles list

---

## [1.0.0] - 2024-11-05

### 🎉 Initial Release
- **Authentication System**
  - Login & Register functionality
  - Role-based access control (Super Admin, Admin, Contributor, Reviewer, Guest)
  - Token-based authentication with Laravel Sanctum

- **Admin Dashboard**
  - User management (CRUD)
  - Role management
  - Document management
  - Statistics & analytics

- **User Dashboard**
  - Upload document feature
  - Document list view
  - Profile management

- **Public Pages**
  - Landing page dengan hero search
  - Search results view
  - About page

- **Tech Stack**
  - **Frontend**: Vue 3, TypeScript, Vite, Tailwind CSS
  - **Backend**: Laravel 10, MySQL, Sanctum
  - **UI Components**: Shadcn-vue, Select2

---

## Development Guidelines

### Version Numbering
- **Major (X.0.0)**: Breaking changes, major feature releases
- **Minor (1.X.0)**: New features, non-breaking changes
- **Patch (1.0.X)**: Bug fixes, small improvements

### How to Update This File
1. Add new entry di bagian atas (setelah header)
2. Format: `## [Version] - YYYY-MM-DD`
3. Kategori: `✨ Added`, `🔧 Modified`, `🐛 Fixed`, `🗑️ Removed`, `📝 Documentation`
4. Bullet points dengan detail perubahan
5. Mention file yang berubah jika relevan

### Git Commit Message Convention
```
feat: Add role change notification system
fix: Fix role dropdown empty issue
docs: Update CHANGELOG.md
refactor: Remove unused views and documentation
style: Update modal scrollbar styling
```

---

## Upcoming Features

### 🚀 Planned for v1.6.0
- WebSocket integration for real-time notifications (replace polling)
- Email notification saat role berubah
- Notification history log
- Sound notification option
- Dark mode support

### 💡 Ideas for Future Versions
- Advanced search filters
- Document versioning
- Bulk upload documents
- Export to PDF/Excel
- Activity logs & audit trail
- API documentation with Swagger

---

## Maintenance Notes

### Important Credentials
- Lihat `CREDENTIALS.md` untuk detail akses sistem
- Super Admin credentials tidak boleh di-commit ke repository
- Gunakan `.env` untuk sensitive data

### Testing Checklist (Before Release)
- [ ] Login dengan semua role (Super Admin, Admin, Contributor, Reviewer, Guest)
- [ ] Test role change notification (default → upgraded role)
- [ ] Test user CRUD operations
- [ ] Test document upload & search
- [ ] Test responsive design (mobile/tablet/desktop)
- [ ] Check console errors di browser
- [ ] Verify API responses
- [ ] Test logout & session management

### Deployment Steps
1. Pull latest changes dari `development` branch
2. Run `composer install` di backend
3. Run `npm install` di frontend
4. Update `.env` dengan production credentials
5. Run migrations: `php artisan migrate`
6. Build frontend: `npm run build`
7. Test critical user flows
8. Merge ke `main` branch
9. Tag release: `git tag v1.5.0`

---

**Maintained by**: MohammadAvirzaR  
**Project**: E-Library BRIDA  
**Repository**: elibrary-brida  
**Last Updated**: 2024-11-10
