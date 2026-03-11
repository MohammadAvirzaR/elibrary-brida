# 📚 E-Library BRIDA

> Sistem Manajemen E-Library untuk BRIDA Sulawesi Tenggara

[![Version](https://img.shields.io/badge/version-1.7.0-blue.svg)](CHANGELOG.md)
[![Laravel](https://img.shields.io/badge/Laravel-10-red.svg)](https://laravel.com)
[![Vue](https://img.shields.io/badge/Vue-3-green.svg)](https://vuejs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-blue.svg)](https://www.typescriptlang.org)

---

## 📋 Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Tech Stack](#tech-stack)
- [Struktur Project](#struktur-project)
- [Quick Start](#quick-start)
- [Fitur Utama](#fitur-utama)
- [Role & Permissions](#role--permissions)
- [Dokumentasi](#dokumentasi)
- [Development](#development)

---

## 🎯 Tentang Proyek

E-Library BRIDA adalah sistem manajemen perpustakaan digital yang dibangun untuk BRIDA Sulawesi Tenggara. Sistem ini memiliki fitur lengkap untuk manajemen dokumen, user, dan role-based access control.

### Fitur Unggulan
✨ **Smart Role Change Notification** - Notifikasi otomatis saat role user berubah  
🎨 **Discord-Style Role Management** - Drag & drop hierarchy dengan color coding  
📤 **Document Upload & Management** - Upload, search, dan manage dokumen  
🔐 **Granular Permissions** - Kontrol akses detail per role  
📊 **Admin Dashboard** - Analytics, statistics charts, dan user management  
📄 **Reliable PDF Preview** - Preview dokumen langsung di browser tanpa download otomatis  
👤 **Profile Management** - Edit user profiles dan reset password dengan validasi keamanan  
🔒 **Smart Password Validation** - Validasi password dengan pesan error Bahasa Indonesia  

---

## 🛠 Tech Stack

### Frontend
- **Framework**: Vue 3 (Composition API)
- **Language**: TypeScript
- **Build Tool**: Vite 7.1.7
- **Styling**: Tailwind CSS
- **UI Components**: Shadcn-vue, Select2
- **State Management**: Pinia
- **Routing**: Vue Router

### Backend
- **Framework**: Laravel 10
- **Database**: MySQL
- **Authentication**: Laravel Sanctum (Token-based)
- **API**: RESTful API

---

## 📁 Struktur Project

```
elibrary-brida/
├── elibrary-brida-fe/          # Frontend (Vue 3)
│   ├── src/
│   │   ├── components/         # Reusable components
│   │   ├── composables/        # Vue composables
│   │   ├── pages/             # Page components
│   │   │   ├── auth/          # Login, Register
│   │   │   ├── dashboard/     # Admin dashboard
│   │   │   └── public/        # Public pages
│   │   ├── router/            # Vue Router config
│   │   ├── services/          # API services
│   │   ├── stores/            # Pinia stores
│   │   └── middleware/        # Route guards
│   └── package.json
│
├── elibrary-brida-be/          # Backend (Laravel)
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   └── Middleware/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/
│       └── api.php
│
├── CHANGELOG.md               # Update history (PENTING!)
├── CREDENTIALS.md             # System credentials
└── README.md                  # This file
```

---

## 🚀 Quick Start

### Prerequisites
- Node.js >= 18.x
- PHP >= 8.1
- Composer
- MySQL >= 8.0

### 1️⃣ Clone Repository
```bash
git clone https://github.com/MohammadAvirzaR/elibrary-brida.git
cd elibrary-brida
```

### 2️⃣ Setup Frontend
```bash
cd elibrary-brida-fe
npm install
cp .env.example .env         # Configure API endpoint
npm run dev                  # Dev server: http://localhost:5173
```

### 3️⃣ Setup Backend
```bash
cd ../elibrary-brida-be
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed   # Create tables & seed data
php artisan serve            # Dev server: http://localhost:8000
```

### 4️⃣ Access Aplikasi
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000/api
- **Credentials**: Lihat `CREDENTIALS.md`

---

## ✨ Fitur Utama

### 🔐 Authentication & Authorization
- Login & Register dengan validation
- Role-based access control (RBAC)
- Token-based authentication (Sanctum)
- Auto-redirect berdasarkan role
- **Smart role change notification** ⭐ NEW!

### 👥 User Management (Admin)
- CRUD users dengan flexible validation
- Assign roles ke users
- Protect super_admin dari edit/delete
- Real-time role change detection

### 👤 Profile Management (Admin & Super Admin)
- Edit user profiles (name, email, institution)
- Reset password untuk users dengan validasi minimal 8 karakter
- Password visibility toggle dengan icon yang responsive
- Custom validation dengan pesan error Bahasa Indonesia
- Modal interface yang user-friendly

### 📑 Role Management
- **Discord-style interface** dengan drag & drop
- Color-coded roles
- Granular permission system
- Member count per role
- Display options (mentionable, separated)

### 📤 Document Management
- Upload dokumen (PDF, DOC, etc.) dengan validasi ukuran file
- PDF preview yang reliable untuk semua ukuran dokumen
- Advanced search & filters
- Document categorization
- Download tracking
- Support inline preview untuk pending documents (role-based access)

### 📊 Dashboard
- **Admin Dashboard**: User stats, document stats, activity logs
  - Statistics Charts: Category distribution (pie), trend analysis (line), institution breakdown (bar)
  - Queue Review table dengan status tracking
  - Recent Activity history
- **User Dashboard**: Upload, my documents, profile

---

## 🎭 Role & Permissions

| Role | Level | Access |
|------|-------|--------|
| **Super Admin** | 5 | Full system access, tidak bisa diedit |
| **Admin** | 4 | User management, document approval |
| **Contributor** | 3 | Upload & manage own documents |
| **Reviewer** | 2 | Review & comment documents |
| **Guest** | 1 | View & download public documents |

**Note**: Default role untuk user baru adalah `Guest`.

---

## 📚 Dokumentasi

### File Dokumentasi Utama
- **`CHANGELOG.md`** ⭐ - **WAJIB BACA** untuk update terbaru
- **`CREDENTIALS.md`** - Akses credentials sistem
- **`README.md`** - Dokumentasi utama (file ini)

### Role Change Notification
Sistem notifikasi cerdas yang mendeteksi perubahan role:
- ✅ **Login pertama**: Save role, no popup
- ✅ **Login berikutnya (role sama)**: No popup
- ✅ **Login berikutnya (role berubah)**: Show popup dengan old → new role
- ✅ **Auto-hide**: 15 detik dengan progress bar
- ✅ **In-session detection**: Polling setiap 10 detik

Lihat `CHANGELOG.md` v1.5.0 untuk detail lengkap.

### API Documentation
Base URL: `http://localhost:8000/api`

**Authentication**
```bash
POST /api/login
POST /api/register
POST /api/logout
```

**Users** (Admin only)
```bash
GET    /api/users
POST   /api/users
PUT    /api/users/{id}
DELETE /api/users/{id}
```

**Roles**
```bash
GET    /api/roles
POST   /api/roles
PUT    /api/roles/{id}
DELETE /api/roles/{id}
```

**Documents**
```bash
GET    /api/documents
POST   /api/documents
GET    /api/documents/{id}
PUT    /api/documents/{id}
DELETE /api/documents/{id}
```

---

## 👨‍💻 Development

### Branch Strategy
```
main          → Production-ready code
development   → Active development (default branch)
feature/*     → New features
fix/*         → Bug fixes
```

### Workflow
1. Checkout `development` branch
2. Create feature branch: `git checkout -b feature/your-feature`
3. Make changes & commit
4. Push & create Pull Request ke `development`
5. After review → merge ke `development`
6. Testing di `development`
7. Merge ke `main` untuk production

### Commit Message Convention
```
feat: Add role change notification system
fix: Fix empty role dropdown
docs: Update CHANGELOG.md
refactor: Remove unused files
style: Update modal styling
```

### Update CHANGELOG
**PENTING**: Setiap kali ada update, tambahkan entry di `CHANGELOG.md`:
```markdown
## [1.X.0] - YYYY-MM-DD

### ✨ Added
- Feature baru yang ditambahkan

### 🔧 Modified
- File yang dimodifikasi

### 🐛 Fixed
- Bug yang diperbaiki

### 🗑️ Removed
- File/feature yang dihapus
```

---

## 🧪 Testing

### Frontend Testing
```bash
npm run test              # Run unit tests
npm run test:e2e         # Run E2E tests (coming soon)
npm run lint             # Check linting
```

### Backend Testing
```bash
php artisan test         # Run PHPUnit tests
```

### Manual Testing Checklist
- [ ] Login dengan semua role
- [ ] Test role change notification
- [ ] Test user CRUD
- [ ] Test document upload (berbagai ukuran file)
- [ ] Test PDF preview untuk dokumen kecil (<100KB) dan besar (>100KB)
- [ ] Test responsive design
- [ ] Check browser console errors
- [ ] Verify file storage consistency

---

## 🚀 Deployment

### Build Frontend
```bash
cd elibrary-brida-fe
npm run build            # Output: dist/
```

### Production Checklist
- [ ] Update `.env` dengan production credentials
- [ ] Run migrations di production database
- [ ] Build frontend assets
- [ ] Set proper file permissions
- [ ] Configure web server (Nginx/Apache)
- [ ] Enable HTTPS
- [ ] Setup backup schedule

---

## 📝 License

This project is proprietary and confidential.  
© 2024 BRIDA Sulawesi Tenggara. All rights reserved.

---

## 👤 Contact

**Developer**: MohammadAvirzaR  
**Project**: E-Library BRIDA  
**Version**: 1.7.0  
**Last Updated**: 2026-03-11

---

## 📌 Important Links

- 📖 [CHANGELOG.md](CHANGELOG.md) - **Update history & release notes**
- 🔑 [CREDENTIALS.md](CREDENTIALS.md) - System credentials
- 🐛 [Issues](https://github.com/MohammadAvirzaR/elibrary-brida/issues) - Bug reports
- 🎯 [Project Board](https://github.com/MohammadAvirzaR/elibrary-brida/projects) - Development tracking

---

**⭐ Untuk informasi update terbaru, selalu cek [CHANGELOG.md](CHANGELOG.md)!**
