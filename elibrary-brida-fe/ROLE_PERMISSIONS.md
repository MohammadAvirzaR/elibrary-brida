# Role-Based Access Control (RBAC) Documentation

## Roles Overview

Sistem E-Library BRIDA memiliki 5 role berbeda dengan permissions yang berbeda:

### 1. Super Admin (super_admin)
**ID:** 1  
**Deskripsi:** Memiliki akses penuh ke seluruh sistem, termasuk pengaturan user, dokumen, dan role lainnya.

**Permissions:**
- ✅ Full access ke seluruh sistem
- ✅ Manage users (create, read, update, delete)
- ✅ Manage roles (create, read, update, delete)
- ✅ Manage documents (create, read, update, delete)
- ✅ Review documents (approve/reject)
- ✅ Upload documents
- ✅ View dashboard
- ✅ View all statistics

**Routes Access:**
- `/dashboard` ✅
- `/users` ✅
- `/roles` ✅
- `/profile` ✅
- `/settings` ✅
- `/notifications` ✅

---

### 2. Admin (admin)
**ID:** 2  
**Deskripsi:** Bertanggung jawab mengelola data dokumen dan pengguna dalam lingkup sistem tertentu.

**Permissions:**
- ✅ Manage users (create, read, update, delete)
- ✅ Manage documents (create, read, update, delete)
- ✅ Review documents (approve/reject)
- ✅ Upload documents
- ✅ View dashboard
- ✅ View statistics
- ❌ Manage roles

**Routes Access:**
- `/dashboard` ✅
- `/users` ✅
- `/roles` ❌
- `/profile` ✅
- `/settings` ✅
- `/notifications` ✅

---

### 3. Contributor (contributor)
**ID:** 3  
**Deskripsi:** Menyumbang dokumen baru atau mengajukan perubahan pada dokumen yang ada.

**Permissions:**
- ✅ Upload documents
- ✅ Edit own documents
- ✅ View own documents
- ✅ View public documents
- ❌ Review documents
- ❌ Manage users
- ❌ View dashboard

**Routes Access:**
- `/dashboard` ❌
- `/users` ❌
- `/roles` ❌
- `/profile` ✅
- `/settings` ✅
- `/notifications` ✅
- `/upload` ✅

---

### 4. Reviewer (reviewer)
**ID:** 4  
**Deskripsi:** Memeriksa dan meninjau dokumen sebelum disetujui atau ditolak.

**Permissions:**
- ✅ Review documents (approve/reject)
- ✅ View documents
- ✅ Comment on documents
- ❌ Upload documents
- ❌ Manage users
- ❌ View dashboard (admin)

**Routes Access:**
- `/dashboard` ❌
- `/users` ❌
- `/roles` ❌
- `/profile` ✅
- `/settings` ✅
- `/notifications` ✅
- `/review` ✅

---

### 5. Guest (guest)
**ID:** 5  
**Deskripsi:** Pengguna umum yang dapat melihat dan mengunduh dokumen publik.

**Permissions:**
- ✅ View public documents
- ✅ Download public documents
- ✅ Search documents
- ❌ Upload documents
- ❌ Review documents
- ❌ Manage users
- ❌ View dashboard

**Routes Access:**
- `/dashboard` ❌
- `/users` ❌
- `/roles` ❌
- `/profile` ✅
- `/settings` ✅
- `/notifications` ✅
- `/catalog` ✅
- `/search` ✅

---

## Implementation

### Frontend

#### 1. Route Protection
Routes dilindungi menggunakan meta field `roles`:

```typescript
{
  path: '/dashboard',
  name: 'dashboard',
  component: () => import('@/pages/dashboard/DashboardView.vue'),
  meta: {
    requiresAuth: true,
    roles: [ROLES.SUPER_ADMIN, ROLES.ADMIN], // Only super_admin and admin
    title: 'Dashboard'
  }
}
```

#### 2. Navigation Guard
Router memiliki navigation guard yang mengecek:
1. Authentication status
2. Role requirements
3. Redirect ke `/unauthorized` jika tidak memiliki akses

#### 3. Composable: useAuth()
Gunakan composable untuk cek permissions di component:

```vue
<script setup>
import { useAuth } from '@/composables/useAuth'

const { canManageUsers, isAdmin, userRole } = useAuth()
</script>

<template>
  <div v-if="canManageUsers">
    <!-- Only visible for super_admin and admin -->
  </div>
</template>
```

#### 4. Helper Functions
```typescript
import { hasRole, hasAnyRole, ROLES } from '@/middleware/roleGuard'

// Check single role
if (hasRole(ROLES.SUPER_ADMIN)) {
  // Do something
}

// Check multiple roles
if (hasAnyRole([ROLES.SUPER_ADMIN, ROLES.ADMIN])) {
  // Do something
}
```

### Backend

#### 1. Middleware: RoleMiddleware
```php
Route::middleware(['auth:sanctum', 'role:super_admin,admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});
```

#### 2. Policy-based Authorization
```php
class DocumentPolicy
{
    public function update(User $user, Document $document)
    {
        return $user->role->name === 'super_admin' 
            || $user->role->name === 'admin'
            || $user->id === $document->user_id;
    }
}
```

---

## Usage Examples

### Example 1: Conditional Rendering Based on Role
```vue
<template>
  <div>
    <!-- Only for super_admin -->
    <button v-if="isSuperAdmin" @click="deleteAllData">
      Delete All Data
    </button>

    <!-- For super_admin and admin -->
    <button v-if="canManageUsers" @click="manageUsers">
      Manage Users
    </button>

    <!-- For contributor -->
    <button v-if="canUploadDocuments" @click="uploadDoc">
      Upload Document
    </button>
  </div>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth'

const { 
  isSuperAdmin, 
  canManageUsers, 
  canUploadDocuments 
} = useAuth()
</script>
```

### Example 2: Programmatic Navigation with Role Check
```typescript
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const { canManageUsers } = useAuth()

function goToUserManagement() {
  if (canManageUsers.value) {
    router.push('/users')
  } else {
    router.push('/unauthorized')
  }
}
```

### Example 3: API Request with Role
```typescript
const { user } = useAuth()

async function fetchData() {
  const response = await fetch('/api/documents', {
    headers: {
      'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
      'X-User-Role': user.value?.role || 'guest'
    }
  })
  
  return response.json()
}
```

---

## Security Best Practices

1. **Never trust frontend checks alone** - Always validate on backend
2. **Use middleware** on API routes to enforce role-based access
3. **Implement policies** for fine-grained permissions
4. **Log unauthorized access attempts** for security monitoring
5. **Keep role definitions consistent** between frontend and backend
6. **Use environment variables** for role IDs in production

---

## Testing Role-Based Access

### Test User Accounts

| Email | Password | Role | Purpose |
|-------|----------|------|---------|
| admin@brida.com | admin123 | super_admin | Full access testing |
| admin2@brida.com | admin123 | admin | Admin access testing |
| contributor@brida.com | contributor123 | contributor | Upload testing |
| reviewer@brida.com | reviewer123 | reviewer | Review testing |
| guest@brida.com | guest123 | guest | Public access testing |

### Test Scenarios

1. ✅ Login as different roles
2. ✅ Attempt to access restricted routes
3. ✅ Verify unauthorized redirects
4. ✅ Check UI element visibility
5. ✅ Test API endpoint permissions
6. ✅ Verify role-based data filtering

---

## Troubleshooting

### Issue: User can't access expected routes
**Solution:** 
- Check if user role is correctly stored in localStorage
- Verify route meta.roles array includes user's role
- Clear browser cache and localStorage

### Issue: Unauthorized page keeps showing
**Solution:**
- Ensure token is valid in localStorage
- Check if getCurrentUser() returns correct data
- Verify role name matches ROLES constant

### Issue: Role check not working in component
**Solution:**
- Import useAuth() composable correctly
- Use computed properties for reactive checks
- Ensure user data is loaded before checking

---

## Future Enhancements

- [ ] Permission-based access (finer-grained than roles)
- [ ] Role hierarchy (role inheritance)
- [ ] Dynamic role assignment
- [ ] Role expiration dates
- [ ] Audit log for role changes
- [ ] Multi-role support per user
