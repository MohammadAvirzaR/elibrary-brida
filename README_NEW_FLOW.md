# E-Library BRIDA - Role-Based Access Control & Contributor System

## 📋 Alur Sistem Terbaru

Sistem telah diperbarui dengan alur role-based access control dan contributor request system yang lebih jelas.

---

## 🎭 Roles & Permissions

### 1. **Guest (Unregistered)**
- **Akses:** Browse dan search dokumen
- **Tidak bisa:** Download dokumen, upload dokumen
- **Aksi:** Perlu register untuk bisa download

### 2. **Guest (Registered)**
- **Akses:** Browse, search, dan **download dokumen**
- **Tidak bisa:** Upload dokumen
- **Aksi:** Bisa ajukan request menjadi kontributor untuk upload
- **Dashboard:** `/my-dashboard` - lihat aktivitas, tapi tidak bisa upload

### 3. **Contributor**
- **Akses:** Browse, search, download, dan **upload dokumen**
- **Dashboard:** `/my-dashboard` - full access termasuk upload modal
- **Cara Mendapat:** Approve dari admin melalui contributor request

### 4. **Reviewer**
- **Akses:** Browse, search, download, dan **review dokumen**
- **Dashboard:** Khusus untuk review dokumen yang diupload

### 5. **Admin**
- **Akses:** Profile management, manage users (tapi tidak create/delete)
- **Dashboard:** `/dashboard` - admin interface

### 6. **Super Admin**
- **Akses:** Full access - user management, role management, contributor requests
- **Dashboard:** `/dashboard` - full admin interface

---

## 🔄 Alur Contributor Request

### 1. User Registrasi
```
Register → Role: guest (registered)
```

### 2. User Ingin Upload Dokumen
```
Guest → Klik "Jadi Kontributor" → Form Request → Submit
```

### 3. Admin Review Request
```
Super Admin → Dashboard → Contributor Requests → Approve/Reject
```

### 4. User Menjadi Contributor
```
Approved → Role: contributor → Bisa upload dokumen
```

---

## 🚪 Frontend Routes

### Public Routes (Tidak Perlu Login)
- `/` - Homepage
- `/catalog` - Katalog dokumen
- `/search` - Search results
- `/detail/:id` - Detail dokumen
- `/login` - Login page
- `/register` - Register page

### User Dashboard (Perlu Login)
- `/my-dashboard` - User dashboard (Guest, Contributor, Reviewer)

### Contributor Request (Guest Only)
- `/become-contributor` - Form request menjadi kontributor

### Admin Dashboard (Admin & Super Admin Only)
- `/dashboard` - Admin dashboard
- `/profile-management` - Manage user profiles (Admin + Super Admin)

### Super Admin Only
- `/users` - User management (CRUD users)
- `/roles` - Role management (CRUD roles)
- `/contributor-requests` - Manage contributor requests

---

## 🔐 API Endpoints

### Contributor Requests
```bash
# Submit contributor request (User)
POST /api/contributor-requests
{
  "message": "Alasan ingin jadi kontributor..."
}

# Check pending request (User)
GET /api/contributor-requests/check-pending

# Get all requests (Super Admin)
GET /api/contributor-requests

# Approve request (Super Admin)
POST /api/contributor-requests/{id}/approve
{
  "admin_notes": "Optional notes..."
}

# Reject request (Super Admin)
POST /api/contributor-requests/{id}/reject
{
  "admin_notes": "Alasan ditolak..."
}
```

### Users (Updated)
```bash
# Get users (includes phone, address, institution)
GET /api/users

# Update user (includes phone, address, password reset)
PUT /api/users/{id}
{
  "name": "Updated Name",
  "email": "new@email.com",
  "institution": "New Institution",
  "phone": "081234567890",
  "address": "New Address",
  "password": "newpassword" // Optional untuk reset password
}
```

### Roles (Updated)
```bash
# Create/Update role with permissions
POST /api/roles
{
  "name": "Content Manager",
  "description": "Manage content",
  "permissions": [
    "documents.view",
    "documents.create",
    "documents.edit"
  ]
}
```

---

## 🎯 Key Features

### 1. **Role-Based Upload Restrictions**
- Guest (registered): Tidak bisa akses upload modal
- Guest (registered): Tombol "Jadi Kontributor" muncul
- Contributor: Bisa akses upload modal
- Pending request: Tampil status "Menunggu Persetujuan"

### 2. **Contributor Request Workflow**
- Form request dengan alasan detail (min 10 karakter)
- Admin dapat approve/reject dengan notes
- Auto role change saat approved
- Email notification (future enhancement)

### 3. **Enhanced User Management**
- Super Admin: Full CRUD users
- Admin: Hanya edit profile + reset password
- Support phone, address, institution fields
- Password reset melalui admin

### 4. **Sidebar Organization**
- Dashboard - All admins
- User Management - Super Admin only
- Role Management - Super Admin only
- Contributor Requests - Super Admin only (NEW)
- Profile Management - Admin + Super Admin

---

## 🛠 Database Changes

### New Table: contributor_requests
```sql
CREATE TABLE contributor_requests (
  id BIGINT PRIMARY KEY,
  user_id BIGINT (FK to users),
  status ENUM('pending', 'approved', 'rejected'),
  message TEXT,
  admin_notes TEXT,
  reviewed_by BIGINT (FK to users),
  reviewed_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Updated Table: users
```sql
ALTER TABLE users ADD COLUMN phone VARCHAR(20);
ALTER TABLE users ADD COLUMN address TEXT;
ALTER TABLE users ADD COLUMN institution VARCHAR(255);
ALTER TABLE users ADD COLUMN name VARCHAR(255); -- For frontend compatibility
```

### Updated Table: roles
```sql
ALTER TABLE roles ADD COLUMN permissions JSON;
```

---

## 🎨 UI/UX Improvements

### 1. **User Dashboard**
- Role-based button rendering:
  - Contributor: "Unggah Dokumen" (blue)
  - Guest: "Jadi Kontributor" (green)
  - Pending: "Menunggu Persetujuan" (yellow)

### 2. **Contributor Request Form**
- Informational cards explaining benefits
- Requirements checklist
- Character counter for message
- Success/error states
- Auto-redirect after submission

### 3. **Admin Request Management**
- Stats cards (Pending, Approved, Rejected)
- Searchable table
- Approve/Reject modals with notes
- Status badges and timestamps

---

## 🚀 Getting Started

### 1. Backend Setup
```bash
cd elibrary-brida-be
php artisan migrate  # Run new migrations
php artisan serve    # Start server on :8000
```

### 2. Frontend Setup
```bash
cd elibrary-brida-fe
npm install
npm run dev  # Start on :5173
```

### 3. Test Flow
1. Register as new user (becomes Guest)
2. Login → redirected to `/my-dashboard`
3. Click "Jadi Kontributor" → fill form → submit
4. Login as Super Admin → go to Contributor Requests → approve
5. User now becomes Contributor → can upload documents

---

## 📱 Responsive Design

All new pages are fully responsive:
- Mobile-first approach
- Collapsible sidebars on mobile
- Touch-friendly buttons and forms
- Adaptive layouts for tables

---

## 🔮 Future Enhancements

1. **Email Notifications**
   - Request submitted notification to admins
   - Approval/rejection notifications to users

2. **Advanced Permissions**
   - Granular document permissions
   - Department-based access control

3. **Contributor Analytics**
   - Upload statistics
   - Contribution metrics
   - Leaderboards

4. **Request Management**
   - Bulk approve/reject
   - Request categories
   - Auto-approval based on criteria

---

## ⚠️ Breaking Changes

### Route Changes
- `/upload` (public) removed - now requires Contributor role
- Upload functionality moved to `/my-dashboard` for Contributors only

### API Response Changes
- User endpoints now include `phone`, `address`, `institution`
- Role endpoints now include `permissions` array
- New contributor request endpoints added

### Permission Changes
- Guest (registered) cannot upload documents anymore
- Upload requires explicit Contributor role
- Admin permissions separated from Super Admin

---

## 👥 Roles Summary

| Role | Browse | Download | Upload | Admin Panel | Contributor Requests |
|------|---------|----------|--------|-------------|---------------------|
| Guest (unreg) | ✅ | ❌ | ❌ | ❌ | ❌ |
| Guest (reg) | ✅ | ✅ | ❌ | ❌ | ✅ Submit |
| Contributor | ✅ | ✅ | ✅ | ❌ | ❌ |
| Reviewer | ✅ | ✅ | ❌ | ✅ (review) | ❌ |
| Admin | ✅ | ✅ | ❌ | ✅ (profiles) | ❌ |
| Super Admin | ✅ | ✅ | ❌ | ✅ (full) | ✅ Manage |

---

**Version:** 2.0  
**Date:** November 14, 2025  
**Status:** ✅ Production Ready