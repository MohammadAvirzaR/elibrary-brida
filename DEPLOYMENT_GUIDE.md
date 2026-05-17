# 📚 E-Library BRIDA - Deployment Guide

## Google OAuth Configuration untuk Production

Masalah: Google Login tidak bekerja setelah deployment karena URL hardcoded ke localhost.

### ✅ Fix yang sudah diterapkan:

#### Frontend (`elibrary-brida-fe`)
- ✓ Update `LoginView.vue` dan `RegisterView.vue` untuk membaca `VITE_API_BASE_URL` dari environment
- ✓ URL Google login sekarang dynamis, menggunakan environment variable

#### Backend (`elibrary-brida-be`)
- ✓ Dokumentasi di `.env.example` untuk Google OAuth configuration

---

## 🚀 Deployment Checklist

### 1. Backend Configuration (`.env`)

```bash
# Ubah ke production domain Anda:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api-elibrary.sanskuy.space

# PENTING: URL Frontend harus sesuai dengan domain frontend Anda!
FRONTEND_URL=https://elibrary.sanskuy.space

# Database - update sesuai production server
DB_HOST=your-production-db-host
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
DB_DATABASE=elibrary_brida_prod

# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_FROM_ADDRESS=noreply@elibrary.brida.example.com

# GOOGLE OAUTH - WAJIB untuk Google Login bekerja!
GOOGLE_CLIENT_ID=your_actual_google_client_id
GOOGLE_CLIENT_SECRET=your_actual_google_client_secret
GOOGLE_REDIRECT_URI=https://api-elibrary.sanskuy.space/auth/google/callback

# Cache & Session untuk production
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=your-redis-host
```

### 2. Frontend Configuration (`.env.production`)

File sudah ada dan dikonfigurasi dengan benar:
```
VITE_API_BASE_URL=https://api-elibrary.sanskuy.space/api
```

### 3. Google Cloud Console Setup

1. Buka [Google Cloud Console](https://console.cloud.google.com/credentials)
2. Buat OAuth 2.0 credentials (Web application)
3. **Authorized redirect URIs**, tambahkan:
   ```
   https://your-backend-domain.com/auth/google/callback
   ```
   > Contoh: `https://api-elibrary.sanskuy.space/auth/google/callback`

4. Copy `Client ID` dan `Client Secret` ke `.env` backend

### 4. Sanitasi Domain untuk CORS & Sessions

Update di `config/cors.php` dan environment:
```php
// .env
SANCTUM_STATEFUL_DOMAINS=elibrary.sanskuy.space,api-elibrary.sanskuy.space
```

---

## 🔍 Testing Google Login

### Local Testing:
```bash
# Terminal 1 - Backend
cd elibrary-brida-be
php artisan serve

# Terminal 2 - Frontend
cd elibrary-brida-fe
npm run dev
```

Visit: `http://localhost:5173/login` → Click "Lanjutkan dengan Google"

### Production Testing:
1. Deploy frontend ke `https://elibrary.sanskuy.space`
2. Deploy backend ke `https://api-elibrary.sanskuy.space`
3. Set `.env` dengan production values
4. Test Google login flow

---

## 🐛 Troubleshooting

### Error: "redirect_uri_mismatch"
**Cause:** `GOOGLE_REDIRECT_URI` di `.env` tidak match dengan yang di Google Console  
**Fix:** 
- Pastikan `GOOGLE_REDIRECT_URI` di `.env` = redirect URI di Google Console
- Format harus HTTPS di production

### Error: "FRONTEND_URL not found" / Login callback fails
**Cause:** `FRONTEND_URL` di `.env` backend tidak sesuai  
**Fix:** Update `FRONTEND_URL` di `.env` ke domain frontend production Anda

### Google button masih redirect ke localhost
**Cause:** Frontend masih membaca `.env` development  
**Fix:**
- Pastikan `.env.production` digunakan saat build
- Rebuild frontend: `npm run build`
- Deploy file di `dist/` folder

---

## 📝 Environment Variables Summary

| Variable | Location | Production Value |
|----------|----------|------------------|
| `VITE_API_BASE_URL` | Frontend `.env.production` | `https://api-elibrary.sanskuy.space/api` |
| `APP_URL` | Backend `.env` | `https://api-elibrary.sanskuy.space` |
| `FRONTEND_URL` | Backend `.env` | `https://elibrary.sanskuy.space` |
| `GOOGLE_REDIRECT_URI` | Backend `.env` | `https://api-elibrary.sanskuy.space/auth/google/callback` |
| `GOOGLE_CLIENT_ID` | Backend `.env` | From Google Cloud Console |
| `GOOGLE_CLIENT_SECRET` | Backend `.env` | From Google Cloud Console |

---

## 🔐 Production Security Notes

1. ✓ Set `APP_DEBUG=false` di production
2. ✓ Use HTTPS untuk semua domains
3. ✓ Update `SESSION_SECURE_COOKIES=true` di `.env`
4. ✓ Setup proper database backups
5. ✓ Configure REDIS untuk sessions & cache (lebih scalable)
6. ✓ Setup email configuration untuk production SMTP

---

**Last Updated:** May 14, 2026  
**Status:** ✅ Google Login Fix Applied
