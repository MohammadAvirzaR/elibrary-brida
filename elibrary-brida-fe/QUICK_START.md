# Quick Start Guide - Frontend-Backend Integration

## 🚀 Getting Started

### Prerequisites
- ✅ Laravel backend running on `http://127.0.0.1:8000`
- ✅ MySQL database configured
- ✅ Node.js installed (v16+)

---

## 📦 Installation & Setup

### 1. Install Dependencies
```powershell
npm install
```

### 2. Environment Configuration
The `.env` file is already configured with:
```env
VITE_API_BASE_URL=http://127.0.0.1:8000/api
```

If your backend runs on a different URL, update this file.

### 3. Start Development Server
```powershell
npm run dev
```

The frontend should start on `http://localhost:5173`

---

## 🧪 Testing the Integration

### Test 1: User Registration
1. Navigate to `http://localhost:5173`
2. Click "**Register**" in the navbar
3. Fill in the registration form:
   - Name: `Test User`
   - Email: `test@example.com`
   - Institution: `Test Institution`
   - Password: `password123`
   - Confirm Password: `password123`
4. Click "**Daftar**"

**Expected Result:**
- ✅ Success message: "Registrasi berhasil! Mengalihkan ke halaman utama..."
- ✅ Auto-redirect to landing page (`/`)
- ✅ Navbar shows profile (name: "Test User", role badge: "guest")
- ✅ Login/Register buttons hidden
- ✅ Logout button visible
- ✅ Backend creates user with `role_id = 5` (guest)

**Check Backend:**
```sql
SELECT * FROM users WHERE email = 'test@example.com';
-- Should show: role_id = 5, name = Test User
```

---

### Test 2: User Login (Different Roles)

#### A. Login as Guest
1. Logout if logged in
2. Click "**Login**"
3. Enter credentials:
   - Email: `test@example.com`
   - Password: `password123`
4. Click "**Log in**"

**Expected Result:**
- ✅ Redirect to landing page (`/`)
- ✅ Navbar shows profile with "guest" badge

#### B. Login as Admin
1. Create admin user in backend:
```sql
INSERT INTO users (name, email, password, role_id, created_at, updated_at)
VALUES ('Admin User', 'admin@example.com', '$2y$12$[hashed_password]', 2, NOW(), NOW());
-- role_id = 2 for admin
```
2. Login with admin credentials

**Expected Result:**
- ✅ Redirect to welcome page (`/welcome`)
- ✅ Navbar shows profile with "admin" badge
- ✅ "Dashboard" link visible in profile dropdown

#### C. Login as Super Admin
1. Create super admin user:
```sql
UPDATE users SET role_id = 1 WHERE email = 'admin@example.com';
-- role_id = 1 for super_admin
```
2. Login with super admin credentials

**Expected Result:**
- ✅ **Direct redirect to dashboard** (`/dashboard`)
- ✅ Navbar shows profile with "super_admin" badge
- ✅ Full dashboard access

---

### Test 3: Auto-Refresh (Role Change Detection)

**Setup:**
1. Login as guest
2. Stay on the landing page
3. In backend, change user role:
```sql
UPDATE users SET role_id = 2 WHERE email = 'test@example.com';
-- Change from guest (5) to admin (2)
```

**Expected Result:**
- ⏱️ Wait up to 30 seconds
- ✅ Role badge in navbar automatically updates to "admin"
- ✅ "Dashboard" link appears in profile dropdown
- ✅ Console shows: `Role changed from guest to admin`

---

### Test 4: Logout
1. While logged in, click "**Logout**" button (red border in navbar)
2. Or click profile dropdown → "Logout"

**Expected Result:**
- ✅ Redirect to landing page (`/`)
- ✅ Navbar shows "Login" and "Register" buttons
- ✅ Profile section hidden
- ✅ `localStorage` cleared (auth_token, user)

---

### Test 5: Cross-Tab Synchronization
1. Open the app in **two browser tabs**
2. Login in **Tab 1**
3. Observe **Tab 2**

**Expected Result:**
- ✅ Tab 2 automatically detects login
- ✅ Navbar updates to show profile
- ✅ No page refresh needed

4. Logout in **Tab 1**
5. Observe **Tab 2**

**Expected Result:**
- ✅ Tab 2 automatically detects logout
- ✅ Navbar updates to show Login/Register
- ✅ No page refresh needed

---

## 🔍 Debugging Tips

### Check API Calls
Open **DevTools** → **Network** tab:
- Filter by: `Fetch/XHR`
- Look for calls to `http://127.0.0.1:8000/api/*`

**Successful Registration:**
```
POST /api/register → 201 Created
Response: { user: {...}, token: "..." }
```

**Successful Login:**
```
POST /api/login → 200 OK
Response: { user: {...}, token: "..." }
```

**Auto-Refresh:**
```
GET /api/me → 200 OK (every 30 seconds)
Response: { user: {...} }
```

### Check LocalStorage
Open **DevTools** → **Application** → **Local Storage**:
- `auth_token`: Bearer token string
- `user`: JSON object with `{ id, name, email, role, ... }`

### Check Console Logs
API calls log to console:
```
[API] POST /api/register
[API] POST /api/login
[API] GET /api/me
Role changed from guest to admin
```

---

## ❌ Common Issues

### Issue 1: CORS Error
**Error:** `Access to fetch at 'http://127.0.0.1:8000/api/...' has been blocked by CORS policy`

**Fix:**
Check Laravel `config/cors.php`:
```php
'allowed_origins' => ['http://localhost:5173'],
```

### Issue 2: 401 Unauthorized on /api/me
**Cause:** Invalid or expired token

**Fix:**
- Logout and login again
- Check token in localStorage
- Verify backend token validation

### Issue 3: Registration/Login Not Working
**Cause:** Backend not running or database issue

**Fix:**
```powershell
# Check Laravel backend
cd ../elibrary-brida-be
php artisan serve

# Check logs
tail -f storage/logs/laravel.log
```

### Issue 4: Navbar Doesn't Update After Login
**Cause:** Event listener not working

**Fix:**
- Hard refresh the page (`Ctrl + Shift + R`)
- Check console for errors
- Verify `auth-changed` event is dispatched

---

## 📋 Testing Checklist

Authentication Flow:
- [ ] Register new user → auto-login → redirect to `/`
- [ ] Login as guest → redirect to `/`
- [ ] Login as admin → redirect to `/welcome`
- [ ] Login as super admin → redirect to `/dashboard`
- [ ] Logout → redirect to `/`, navbar updates
- [ ] Auto-refresh detects role changes (30s)
- [ ] Cross-tab login/logout synchronization

UI/UX:
- [ ] Loading states visible during API calls
- [ ] Error messages display correctly
- [ ] Success messages display correctly
- [ ] Profile dropdown works
- [ ] Logout button visible and functional

Backend Validation:
- [ ] User created in database with correct role_id
- [ ] Token stored in `personal_access_tokens` table
- [ ] Role relationships loaded correctly
- [ ] API responses match expected format

---

## 🎯 Next Steps

After authentication works:

1. **Test Backend API Endpoints**
   ```powershell
   # Using curl or Postman
   POST http://127.0.0.1:8000/api/register
   POST http://127.0.0.1:8000/api/login
   GET http://127.0.0.1:8000/api/me (with Bearer token)
   ```

2. **Integrate Document Search**
   - Update `HeroSearch.vue` to use `api.documents.search()`
   - Test search functionality

3. **Create Backend Endpoints for Dashboard**
   - Roles CRUD: `/api/roles/*`
   - Users CRUD: `/api/users/*`

4. **Integrate Dashboard Pages**
   - RolesView.vue
   - UsersView.vue

---

## 📞 Support

If you encounter issues:
1. Check the console for errors
2. Check the Network tab for failed requests
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify database state

---

**Happy Testing! 🎉**
