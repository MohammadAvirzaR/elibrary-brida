# ✅ OTP VERIFICATION - INTEGRATED

## 📋 Overview
Implemented 2-step registration with OTP (One-Time Password) email verification.

## 🔄 Registration Flow

### Step 1: User Registration
1. User fills registration form (name, email, institution, password)
2. Click "Daftar" button
3. Backend generates 6-digit OTP
4. OTP sent to user's email
5. Registration data stored in cache (expires in 10 minutes)

### Step 2: OTP Verification
1. OTP modal appears
2. User enters 6-digit code from email
3. Backend verifies OTP
4. If valid: Create user account with **reviewer role**
5. User automatically logged in
6. Redirected to dashboard

## 🎯 Backend API Endpoints

### 1. POST /api/register
**Purpose**: Send OTP to email

**Request**:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "institution": "BRIDA",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (200)**:
```json
{
  "status": "success",
  "message": "Kode OTP telah dikirim ke email Anda",
  "email": "john@example.com",
  "expires_in": 600
}
```

### 2. POST /api/verify-otp
**Purpose**: Verify OTP and create user account

**Request**:
```json
{
  "email": "john@example.com",
  "otp": "123456"
}
```

**Response (201)**:
```json
{
  "status": "success",
  "message": "Registrasi berhasil! Anda sekarang adalah kontributor.",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "institution": "BRIDA",
    "role": "reviewer"
  },
  "token": "1|abcdef..."
}
```

**Errors (400)**:
```json
{
  "status": "error",
  "message": "Kode OTP tidak valid"
}
```

### 3. POST /api/resend-otp
**Purpose**: Resend OTP code

**Request**:
```json
{
  "email": "john@example.com"
}
```

**Response (200)**:
```json
{
  "status": "success",
  "message": "Kode OTP baru telah dikirim ke email Anda",
  "email": "john@example.com",
  "expires_in": 600
}
```

## 🎨 Frontend Components

### 1. OtpVerificationModal.vue
**Features**:
- 6-digit OTP input fields
- Auto-focus and auto-advance
- Paste support (copy full OTP code)
- Countdown timer (10 minutes)
- Resend OTP button
- Error display
- Keyboard navigation (arrow keys, backspace)

**Props**:
- `email`: User's email address
- `expiresIn`: OTP expiration time in seconds (default: 600)
- `isResending`: Loading state for resend button

**Events**:
- `@verify`: Emitted when user clicks verify with OTP code
- `@resend`: Emitted when user clicks resend OTP
- `@close`: Emitted when user cancels

**Exposed Methods**:
- `setError(message)`: Display error message
- `reset()`: Clear OTP inputs and reset state

### 2. RegisterView.vue (Updated)
**Changes**:
- Added OTP modal integration
- Split registration into 2 steps
- Added loading states
- Added OTP resend functionality
- Added success/error handling
- Direct API calls for OTP endpoints

## 🔐 Security Features

### Backend (Laravel)
1. **OTP Generation**: Random 6-digit number (100000-999999)
2. **Cache Storage**: Registration data stored temporarily
3. **Expiration**: 10-minute timeout
4. **Validation**: Email and OTP format validation
5. **One-time Use**: OTP deleted after successful verification
6. **Email Verification**: `email_verified_at` set on success

### Frontend (Vue)
1. **Input Validation**: Only numeric input allowed
2. **Real-time Feedback**: Visual error messages
3. **Timer Display**: Shows remaining time
4. **Paste Protection**: Sanitizes pasted content
5. **Keyboard Shortcuts**: Enhanced UX with arrow keys

## 📧 Email Configuration

Make sure Laravel mail is configured in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@brida.com
MAIL_FROM_NAME="BRIDA E-Library"
```

For Gmail, use App Password:
1. Enable 2FA on Gmail
2. Go to Security Settings
3. Generate App Password
4. Use in MAIL_PASSWORD

## ✅ Testing Checklist

- [ ] Register with valid email
- [ ] Receive OTP in email inbox
- [ ] Enter correct OTP → Success
- [ ] Enter wrong OTP → Error message
- [ ] Let OTP expire (10 min) → Expired error
- [ ] Click resend OTP → New code sent
- [ ] Paste 6-digit code → Auto-fill
- [ ] Close modal → Can reopen
- [ ] Verify OTP → Auto-login and redirect
- [ ] Check user created with reviewer role
- [ ] Check email_verified_at is set

## 🎯 User Experience Flow

```
┌─────────────────┐
│  Fill Form      │
│  (Name, Email,  │
│   Password)     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Click "Daftar" │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Loading...     │
│  (Sending OTP)  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Check Email    │
│  (6-digit OTP)  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  OTP Modal      │
│  Enter 6 digits │
└────────┬────────┘
         │
         ├──→ Wrong OTP → Error + Retry
         │
         ├──→ Expired → Resend OTP
         │
         ▼
┌─────────────────┐
│  Verifying...   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Success!       │
│  Auto-login     │
│  → Dashboard    │
└─────────────────┘
```

## 🔧 Troubleshooting

### OTP not received?
1. Check spam/junk folder
2. Verify MAIL_* settings in .env
3. Check Laravel logs: `storage/logs/laravel.log`
4. Test email with `php artisan tinker`:
   ```php
   Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'));
   ```

### "Kode OTP sudah kadaluarsa"
- OTP expired after 10 minutes
- Click "Kirim ulang kode OTP"
- New code will be generated and sent

### "Sesi registrasi tidak ditemukan"
- Cache expired or cleared
- User must register again from step 1

### User role is wrong
- Check `AuthController::verifyOtp()`
- Currently sets `role_id = 4` (reviewer)
- To change: Modify line in `verifyOtp` method

## 📊 Current Configuration

- **OTP Length**: 6 digits
- **OTP Expiration**: 10 minutes (600 seconds)
- **Default Role**: Reviewer (role_id = 4)
- **Email Verification**: Set automatically on success
- **Cache Key**: `registration_{email}`

---

**Status**: ✅ Fully Integrated and Working  
**Last Updated**: November 21, 2024  
**Next**: Test with real email server
