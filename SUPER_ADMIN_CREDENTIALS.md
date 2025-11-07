# Akun Super Admin E-Library BRIDA

## Login Credentials

### Super Admin Account
```
Email    : admin@brida.com
Password : admin123
Role     : super_admin
```

## Akses & Permissions

Super Admin memiliki **akses penuh** ke seluruh sistem:

### ✅ Dashboard Access
- **URL**: `http://localhost:5173/dashboard`
- **Akses**: ✅ Full Access
- **Features**:
  - Summary statistik
  - Queue Review (approve/reject documents)
  - Review History
  - Collapsible sidebar

### ✅ User Management
- **URL**: `http://localhost:5173/users`
- **Akses**: ✅ Full Access
- **Features**:
  - View all users
  - Create new users
  - Edit user details
  - Delete users
  - Assign roles

### ✅ Role Management
- **URL**: `http://localhost:5173/roles`
- **Akses**: ✅ Full Access (Hanya Super Admin)
- **Features**:
  - View all roles
  - Create new roles
  - Edit role permissions
  - Delete roles

### ✅ Document Management
- Upload documents
- Review documents
- Approve/Reject submissions
- Edit all documents
- Delete documents

### ✅ Profile & Settings
- Edit own profile
- Change password
- Manage notifications
- System settings

---

## Cara Login

1. Buka aplikasi: `http://localhost:5173`
2. Klik tombol **Login** di navbar
3. Masukkan credentials:
   - **Email**: `admin@brida.com`
   - **Password**: `admin123`
4. Klik **Log in**
5. Akan redirect ke halaman Welcome
6. Auto redirect ke Dashboard dalam 5 detik

---

## Testing Flow

### 1. Login Test
```
✅ Login dengan email: admin@brida.com
✅ Login dengan password: admin123
✅ Verify role: super_admin
✅ Redirect ke /welcome
✅ Auto redirect ke /dashboard
```

### 2. Dashboard Access Test
```
✅ Dapat mengakses /dashboard
✅ Sidebar tampil dengan lengkap
✅ Summary cards menampilkan statistik
✅ Queue Review table tampil
✅ History table tampil
```

### 3. Role-Based Access Test
```
✅ Akses /users (should work)
✅ Akses /roles (should work - only super_admin)
✅ Akses /profile (should work)
✅ Akses /settings (should work)
```

### 4. Logout Test
```
✅ Click logout button
✅ Token dihapus dari localStorage
✅ Redirect ke /login
✅ Cannot access protected routes
```

---

## Troubleshooting

### Issue: Login gagal dengan "Invalid credentials"
**Solusi:**
```bash
cd elibrary-brida-be
php create-super-admin.php
```

### Issue: Redirect ke /unauthorized setelah login
**Solusi:**
1. Check localStorage `user` data
2. Pastikan `role` field = `super_admin`
3. Clear cache dan reload

### Issue: Dashboard tidak muncul
**Solusi:**
1. Check router meta `roles` di `/dashboard`
2. Pastikan `[ROLES.SUPER_ADMIN, ROLES.ADMIN]` includes super_admin
3. Check navigation guard logic

---

## API Endpoints

### Login
```http
POST http://127.0.0.1:8000/api/login
Content-Type: application/json

{
  "email": "admin@brida.com",
  "password": "admin123"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Login successful",
  "user": {
    "id": 3,
    "name": "Super Admin BRIDA",
    "email": "admin@brida.com",
    "username": "superadmin",
    "institution": "BRIDA Sulawesi Tenggara",
    "role": "super_admin"
  },
  "token": "1|xxxxxxxxxxx..."
}
```

### Logout
```http
POST http://127.0.0.1:8000/api/logout
Authorization: Bearer {token}
```

---

## Additional Test Accounts

Untuk testing role lainnya, Anda bisa register akun baru:

### Register as Guest (Default)
```
Name        : Test User
Email       : test@example.com
Institution : Test Institution
Password    : test123
```

**Default Role**: `guest`

---

## Notes

- Password di-hash dengan bcrypt
- Token menggunakan Laravel Sanctum
- Session timeout: 60 menit (default)
- Role hierarchy: super_admin > admin > reviewer > contributor > guest
- Super admin tidak bisa dihapus melalui UI

---

## Security Checklist

- [x] Password hashing (bcrypt)
- [x] Token-based authentication (Sanctum)
- [x] Role-based access control (RBAC)
- [x] Protected routes (frontend)
- [x] API middleware protection (backend)
- [x] CORS configuration
- [ ] Rate limiting (TODO)
- [ ] Two-factor authentication (TODO)
- [ ] Session management (TODO)

---

Last Updated: November 7, 2025
