# 🔐 Akun Login E-Library BRIDA

## ✅ CREDENTIALS SUDAH DIPERBAIKI!

Password sekarang sudah benar dan bisa digunakan untuk login.

## Akun Admin (Super Admin)

**Email:** `admin@brida.com`  
**Password:** `admin123`

**Role:** Super Admin  
**Access:** Full dashboard access dengan semua fitur admin  
**User ID:** 2

---

## Akun User Demo

**Email:** `user@brida.com`  
**Password:** `user123`

**Role:** User  
**Access:** User biasa untuk testing

---

## API Endpoints

### Login
```
POST http://127.0.0.1:8000/api/login
```

**Request Body:**
```json
{
  "email": "admin@brida.com",
  "password": "admin123"
}
```

**Response:**
```json
{
  "token": "xxx...xxx",
  "user": {
    "id": 1,
    "email": "admin@brida.com",
    "full_name": "Admin BRIDA",
    "role": "super_admin"
  }
}
```

### Register
```
POST http://127.0.0.1:8000/api/register
```

---

## Testing Login

1. **Start Backend:**
   ```bash
   cd elibrary-brida-be
   php artisan serve
   ```

2. **Start Frontend:**
   ```bash
   cd elibrary-brida-fe
   npm run dev
   ```

3. **Login:**
   - Buka `http://localhost:5173/login`
   - Masukkan email: `admin@brida.com`
   - Masukkan password: `admin123`
   - Klik "Log in"
   - Akan redirect ke `/dashboard`

---

## Notes

- Password sudah di-hash menggunakan bcrypt
- Token disimpan di localStorage dengan key `auth_token`
- User data disimpan di localStorage dengan key `user`
- Semua route `/dashboard/*` dilindungi dan memerlukan autentikasi
