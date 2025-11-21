# ✅ OTP System - Ready for Testing

## What is OTP?

**OTP (One-Time Password) verifies email addresses during registration.**

Purpose:
- ✅ Confirms users own the email they register with
- ✅ Prevents fake/spam registrations  
- ✅ Ensures legitimate accounts

**Note**: This is NOT two-factor authentication. It's just email verification.

---

## Current Setup (Development Mode)

✅ **Mail Driver**: `log` (for testing)
- Emails are saved to: `storage/logs/laravel.log`
- No actual emails sent (good for development)
- You can see OTP codes in the log file

✅ **Laravel Server**: Running on http://127.0.0.1:8000
✅ **Vue Frontend**: Running on http://localhost:5173

---

## How to Test Registration

### 1. Register a New Account

Go to: http://localhost:5173/register

Fill in the form:
- **Nama Lengkap**: Your Name
- **Email**: test@example.com (any email)
- **Unit/Instansi**: Your Organization (optional)
- **Password**: 123456
- **Konfirmasi Password**: 123456

Click **"Daftar"**

### 2. Get the OTP Code

**Option A - Using PowerShell Script:**
```powershell
cd elibrary-brida-be
.\view-otp.ps1
```

**Option B - Manually check log:**
```powershell
cd elibrary-brida-be
Get-Content storage\logs\laravel.log -Tail 20 | Select-String "Kode OTP"
```

You'll see something like:
```
Kode OTP Anda adalah: 123456
```

### 3. Enter OTP Code

- Copy the 6-digit code
- Paste it in the OTP modal on the website
- Click **"Verifikasi"**
- ✅ Account created!

---

## Switching to Real Email (Production)

When ready to send real emails via Gmail:

### 1. Get Gmail App Password

1. Go to: https://myaccount.google.com/apppasswords
2. Enable 2-Step Verification (Gmail requirement for app passwords)
3. Generate App Password for "Mail"
4. Copy the 16-character code

### 2. Update `.env`

Edit: `elibrary-brida-be\.env`

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-gmail@gmail.com"
MAIL_FROM_NAME="E-Library BRIDA"
```

### 3. Clear Cache & Restart

```powershell
cd elibrary-brida-be
php artisan config:clear
php artisan cache:clear

# Restart server (Ctrl+C then):
php artisan serve
```

---

## Quick Commands

**View latest OTP:**
```powershell
cd elibrary-brida-be
.\view-otp.ps1
```

**Clear all caches:**
```powershell
cd elibrary-brida-be
php artisan config:clear
php artisan cache:clear
```

**Restart Laravel:**
```powershell
# Stop: Ctrl+C in the server terminal
# Start:
cd elibrary-brida-be
php artisan serve
```

**Check if server running:**
```powershell
Test-NetConnection -ComputerName localhost -Port 8000
```

---

## Troubleshooting

### "Gagal mengirim OTP"

**Solution**: Server is using `log` driver now, so this shouldn't happen anymore. The OTP is saved to the log file.

If it still happens:
1. Check Laravel server is running on port 8000
2. Clear config cache: `php artisan config:clear`
3. Check logs: `Get-Content storage\logs\laravel.log -Tail 50`

### Can't find OTP code in logs

```powershell
cd elibrary-brida-be
Get-Content storage\logs\laravel.log | Select-String "Kode OTP" -Context 0,2
```

### Server not responding

```powershell
# Kill any process on port 8000
Get-NetTCPConnection -LocalPort 8000 -State Listen | 
    Select-Object -ExpandProperty OwningProcess | 
    ForEach-Object { Stop-Process -Id $_ -Force }

# Start fresh
cd elibrary-brida-be
php artisan serve
```

---

## Registration Flow

```
User fills form → Click "Daftar"
    ↓
Backend generates 6-digit OTP
    ↓
OTP saved in cache (10 minutes)
    ↓
OTP logged to file (development mode)
    ↓
Modal appears → User checks log for OTP
    ↓
User enters OTP → Click "Verifikasi"
    ↓
Backend validates OTP
    ↓
✅ Account created with email verified!
    ↓
Auto-login → Redirect to dashboard
```

---

## Files Modified

✅ `.env` - Set to `log` mailer for testing
✅ `view-otp.ps1` - Script to view OTP from logs
✅ `AuthController.php` - Handles OTP generation and verification
✅ `RegisterView.vue` - 2-step registration with OTP modal
✅ `OtpVerificationModal.vue` - OTP input component

---

## Summary

✅ **Current Status**: OTP system is working in development mode
✅ **Testing**: Use `.\view-otp.ps1` to see OTP codes
✅ **Production**: Update `.env` with Gmail credentials when ready

**The system is ready to test!** Just register and check the log file for the OTP code.
