# 📋 Testing Checklist - User Registration & Admin Dashboard Integration

## ✅ System Architecture Verification

### Backend (Laravel)
- ✅ User Model - RelationshipTo Role
- ✅ Role Model - HasMany Users
- ✅ AuthController - Register endpoint creates user with role_id=5 (Guest)
- ✅ UserController - GetAll endpoint returns all users with roles
- ✅ RoleController - GetAll endpoint returns all roles
- ✅ Database migrations - All tables created
- ✅ API routes - All endpoints registered with proper middleware

### Frontend (Vue)
- ✅ RegisterView - Submit form → Call api.auth.register()
- ✅ UsersView - Auto-load users on mount, Auto-refresh every 30s
- ✅ RolesView - Auto-load roles on mount, Auto-refresh every 30s
- ✅ API Client - All endpoints defined in src/services/api.ts

---

## 🧪 Complete Testing Flow

### Test 1: Database Check
```sql
-- Check roles table
SELECT * FROM roles;
-- Expected: At least 5 roles (Super Admin, Admin, Reviewer, Contributor, Guest)

-- Check users table (before registration)
SELECT u.id, u.full_name, u.email, u.role_id, r.name as role_name 
FROM users u 
LEFT JOIN roles r ON u.role_id = r.id;
```

### Test 2: Backend API - Register Endpoint
**Endpoint:** `POST http://127.0.0.1:8000/api/register`

**Request:**
```json
{
  "name": "Test User Baru",
  "email": "testbaru123@example.com",
  "institution": "Testing Institution",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Expected Response (200):**
```json
{
  "status": "success",
  "message": "User registered successfully",
  "user": {
    "id": 10,
    "name": "Test User Baru",
    "email": "testbaru123@example.com",
    "institution": "Testing Institution",
    "role": "Guest"
  },
  "token": "1|xyz..."
}
```

**Check Database:**
```sql
SELECT * FROM users WHERE email = 'testbaru123@example.com';
-- Expected: 1 row with role_id = 5
```

### Test 3: Backend API - Get Users Endpoint
**Endpoint:** `GET http://127.0.0.1:8000/api/users`

**Headers:**
```
Authorization: Bearer [token_dari_super_admin]
Accept: application/json
```

**Expected Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Super Admin Name",
      "email": "admin@example.com",
      "institution": "BRIDA",
      "role": "Super Admin",
      "role_id": 1,
      "created_at": "2024-01-01T00:00:00"
    },
    {
      "id": 10,
      "name": "Test User Baru",
      "email": "testbaru123@example.com",
      "institution": "Testing Institution",
      "role": "Guest",
      "role_id": 5,
      "created_at": "2024-01-10T12:34:56"
    }
    // ... other users
  ]
}
```

### Test 4: Frontend - Registration Form
**Steps:**
1. Go to `http://localhost:5173/register`
2. Fill form:
   ```
   Nama Lengkap: Test Frontend User
   Email: testfrontend@example.com
   Institution: Frontend Test
   Password: password123
   Confirm Password: password123
   ```
3. Click "Daftar"
4. **Expected:**
   - ✅ Success message appears
   - ✅ Redirect to landing page after 1.5s
   - ✅ User automatically logged in (navbar shows profile)
   - ✅ Role badge shows "Guest"

**Check LocalStorage:**
```javascript
// In browser console:
localStorage.getItem('auth_token')  // Should have token
localStorage.getItem('user')        // Should have user data
// Output example:
// {id: 11, name: "Test Frontend User", email: "testfrontend@example.com", role: "Guest"}
```

**Check Backend Database:**
```sql
SELECT * FROM users WHERE email = 'testfrontend@example.com';
-- Expected: User exists with role_id = 5
```

### Test 5: Admin Dashboard - View New Users
**Setup:**
1. Login as Super Admin: `http://localhost:5173/login`
2. Navigate to: `http://localhost:5173/users`

**Expected Behavior:**
- ✅ List loads all users from database
- ✅ New registered users appear in the list
- ✅ Role column shows "Guest" for new users
- ✅ Action buttons available (View, Edit, Delete)

**Manual Refresh Test:**
1. Open dashboard in Tab 1
2. Register new user in Tab 2
3. Wait max 30 seconds in Tab 1
4. **Expected:** New user appears automatically (auto-refresh)

### Test 6: Admin Dashboard - Edit User Role
**Steps:**
1. In `/users` dashboard
2. Find the newly registered user
3. Click "Edit" button
4. Change role from "Guest" → "Admin" (or any other)
5. Click "Simpan Perubahan"

**Expected Response:**
```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 11,
    "name": "Test Frontend User",
    "email": "testfrontend@example.com",
    "role": "Admin",
    "role_id": 2
  }
}
```

**Check Database:**
```sql
SELECT * FROM users WHERE email = 'testfrontend@example.com';
-- Expected: role_id changed to 2 (Admin)
```

**User Experience Check:**
- If that user is logged in elsewhere, wait 30 seconds
- Role badge should update automatically
- Dashboard menu should appear (if Admin has that permission)

### Test 7: Admin Dashboard - Delete User
**Steps:**
1. In `/users` dashboard
2. Find user to delete
3. Click "Delete" button
4. Confirm deletion

**Expected:**
```json
{
  "success": true,
  "message": "User deleted successfully"
}
```

**Check Database:**
```sql
SELECT * FROM users WHERE email = 'testfrontend@example.com';
-- Expected: No results (user deleted)
```

### Test 8: Roles Management Dashboard
**Steps:**
1. Login as Super Admin
2. Navigate to: `http://localhost:5173/roles`

**Expected:**
- ✅ All roles load from database
- ✅ List shows: Super Admin, Admin, Reviewer, Contributor, Guest
- ✅ Can add new role
- ✅ Can edit existing role
- ✅ Can delete role (if not in use)
- ✅ Auto-refresh every 30 seconds

---

## 🚀 Full End-to-End Testing Scenario

### Scenario: Complete User Lifecycle

**Timeline:**

1. **T=0s** - Register New User
   - Browser: `http://localhost:5173/register`
   - Fill & submit form
   - Wait for redirect

2. **T=2s** - Verify Registration
   - Check localStorage has token & user data
   - Check backend database has user with role_id=5

3. **T=5s** - Super Admin Views Users
   - Login as Super Admin
   - Navigate to `/users`
   - **Should see:** New user in list with role "Guest"

4. **T=10s** - Super Admin Edits Role
   - Click "Edit" on new user
   - Change role to "Admin"
   - Save changes

5. **T=12s** - Verify Role Change
   - Check database: user's role_id = 2
   - If new user still logged in elsewhere, wait 30s
   - **Should see:** Role badge updates automatically

6. **T=40s** - Auto-Refresh Test
   - Register another new user in different tab
   - Dashboard should detect it within 30s
   - **Should see:** New user appears without manual refresh

7. **T=60s** - Delete User
   - Click delete on one of test users
   - Confirm
   - **Should see:** User removed from list & database

---

## 🔍 Debugging Guide

### Issue: New user not appearing in admin dashboard

**Check List:**
1. ✅ Backend is running: `php artisan serve`
2. ✅ Frontend is running: `npm run dev`
3. ✅ User registered successfully (check response message)
4. ✅ Super Admin has proper token with role
5. ✅ Auto-refresh interval is active (check browser console)
6. ✅ Database query returns user: `SELECT * FROM users WHERE email = '...'`

**Debug Steps:**
```javascript
// In browser console at /users page
console.log('Auth token:', localStorage.getItem('auth_token'))
console.log('Current user:', JSON.parse(localStorage.getItem('user')))

// Check if API calls are working
// DevTools → Network → Filter by Fetch/XHR
// Look for: GET /api/users
// Status should be 200
```

### Issue: User doesn't appear after registration

**Backend Check:**
```bash
# Check if registration endpoint works
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name":"Test",
    "email":"test@test.com",
    "institution":"Test",
    "password":"password123",
    "password_confirmation":"password123"
  }'

# Check database directly
php artisan tinker
>>> User::all()->count()
>>> User::where('email', 'test@test.com')->first()
```

### Issue: 401 Unauthorized when accessing /users

**Check:**
1. Super Admin user exists: `User::where('email', 'admin@example.com')->first()`
2. Super Admin has role_id=1: `User::find(1)->role_id`
3. Role exists: `Role::find(1)->name` should return "Super Admin"
4. Token is valid and not expired
5. Middleware is checking role correctly

---

## ✅ Final Verification Checklist

Before considering implementation complete, verify:

- [ ] User can register via frontend form
- [ ] Registered user data saved in backend database
- [ ] User can login immediately after registration
- [ ] User appears in admin users list
- [ ] Super Admin can view all users
- [ ] Super Admin can edit user (including role change)
- [ ] Super Admin can delete user
- [ ] Auto-refresh detects new users
- [ ] Auto-refresh detects role changes
- [ ] Role change notifies user (if logged in)
- [ ] All API responses have correct format
- [ ] All database records are properly created
- [ ] No errors in backend logs
- [ ] No errors in browser console
- [ ] No errors in API responses

---

## 📊 Key Files Involved

**Backend:**
- `app/Models/User.php` - User model with role relation
- `app/Models/Role.php` - Role model
- `app/Http/Controllers/Api/AuthController.php` - Register endpoint
- `app/Http/Controllers/Api/UserController.php` - Get users endpoint
- `routes/api.php` - API route definitions

**Frontend:**
- `src/pages/auth/RegisterView.vue` - Registration form
- `src/pages/dashboard/UsersView.vue` - Users management dashboard
- `src/pages/dashboard/RolesView.vue` - Roles management dashboard
- `src/services/api.ts` - API client
- `src/composables/useAuth.ts` - Auth composable

**Database:**
- `migrations/2025_10_14_142604_create_roles_table.php`
- `migrations/2025_10_14_143245_create_users_table.php`

---

## 🎯 Expected Architecture Flow

```
User Registration (Frontend)
  ↓
POST /api/register (Backend)
  ↓
Create User record in database (role_id = 5)
  ↓
Return token & user data
  ↓
Frontend stores token & user in localStorage
  ↓
User redirected to landing page (auto-logged in)
  ↓
Super Admin opens /users dashboard
  ↓
GET /api/users endpoint
  ↓
Backend fetches all users with roles from database
  ↓
Dashboard displays user list including newly registered user
  ↓
Auto-refresh every 30s keeps list up-to-date
```

---

## ⏱️ Expected Timings

| Action | Duration |
|--------|----------|
| Register user | 1-2 seconds |
| Redirect after register | 1.5 seconds |
| Load users list | 1-2 seconds |
| Auto-refresh interval | 30 seconds |
| Role change detection | 30 seconds max |
| User deletion | 1-2 seconds |

---

**Status:** ✅ Ready for Complete Testing  
**Date:** 2024  
**Version:** 1.0.0

