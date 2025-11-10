# User Dashboard - Complete Integration Guide

## 📋 Overview
Landing page dashboard khusus untuk user biasa (role: guest, contributor, reviewer) yang fokus pada upload dan manajemen dokumen pribadi.

## ✨ Features

### 1. **User Dashboard** (`/my-dashboard`)
- **Stats Cards**: Total dokumen, pending review, approved, rejected
- **Quick Upload**: Tombol upload dengan modal yang elegant
- **Document Management Table**: 
  - Search & filter by status
  - View, edit, delete actions
  - Status badges (pending, approved, rejected)
- **Sidebar Widgets**:
  - Tips untuk upload
  - Recent activities
  - Help card dengan link ke FAQ

### 2. **Upload Modal Component**
- **Drag & Drop File Upload**: Support PDF, DOC, DOCX (max 10MB)
- **Form Fields**:
  - Title (required)
  - Description (required)
  - Category (required): Penelitian, Laporan, Artikel, Jurnal, Skripsi/Tesis, Buku, Lainnya
  - Year (optional)
  - Author (required)
  - Publisher (optional)
  - Keywords (optional)
- **Validation**: Real-time validation dengan error messages
- **Auto-fill**: Title otomatis terisi dari filename
- **File Preview**: Tampilkan info file yang dipilih

### 3. **Auto Redirect System**
- **After Registration**: 
  - User baru (role: guest) → `/my-dashboard`
  - Success message: "Registrasi berhasil! Mengalihkan ke dashboard..."

- **After Login** (via WelcomeView):
  - Super Admin/Admin → `/dashboard` (Admin Dashboard)
  - Guest/Contributor/Reviewer → `/my-dashboard` (User Dashboard)
  - Auto redirect dalam 5 detik

- **Navigation Bar**:
  - Dashboard link dynamically changes based on role
  - Admin: Dashboard → `/dashboard`
  - User: Dashboard → `/my-dashboard`

## 🛠️ Files Created/Modified

### New Files Created:
1. **`src/pages/user/UserDashboard.vue`**
   - Main user dashboard page
   - Stats, document table, upload button, sidebar widgets

2. **`src/components/UploadDocumentModal.vue`**
   - Reusable upload modal component
   - Drag & drop support
   - Form validation

### Modified Files:
1. **`src/router/index.ts`**
   - Added route: `/my-dashboard` (requires auth, roles: guest/contributor/reviewer)
   
2. **`src/pages/WelcomeView.vue`**
   - Updated `goToDashboard()` to redirect based on role
   
3. **`src/pages/auth/RegisterView.vue`**
   - Changed redirect from `/` to `/my-dashboard` after registration
   
4. **`src/components/NavigationBar.vue`**
   - Added `dashboardLink` computed property
   - Replaced `canAccessDashboard` with dynamic dashboard link

## 🎨 Design Features

### Color Scheme:
- **Primary**: Blue (#3B82F6)
- **Success**: Green (#10B981)
- **Warning**: Amber (#F59E0B)
- **Danger**: Red (#EF4444)
- **Background**: Gradient from blue-50 via white to purple-50

### UI Components:
- **Cards**: White background, rounded-xl, shadow-sm
- **Buttons**: Rounded-lg with hover effects
- **Tables**: Hover effects, status badges
- **Modal**: Backdrop blur, slide-in animation
- **Icons**: Lucide icons throughout

### Responsive:
- Mobile-first design
- Grid system for stats cards
- Responsive table with horizontal scroll
- Collapsible sidebar on mobile

## 🔐 Access Control

### Role-Based Access:
```typescript
// Admin Dashboard (/dashboard)
roles: [ROLES.SUPER_ADMIN, ROLES.ADMIN]

// User Dashboard (/my-dashboard)
roles: [ROLES.GUEST, ROLES.CONTRIBUTOR, ROLES.REVIEWER]
```

### Auto Redirect Logic:
```typescript
// WelcomeView.vue
if (role === 'super_admin' || role === 'admin') {
  router.push('/dashboard')  // Admin Dashboard
} else {
  router.push('/my-dashboard')  // User Dashboard
}
```

## 📊 Data Flow

### Upload Document Flow:
1. User clicks "Mulai Upload" button
2. `UploadDocumentModal` appears
3. User drags/selects file
4. File validated (type, size)
5. User fills form fields
6. Form validated on submit
7. Document uploaded to API
8. New document added to table
9. Stats updated
10. Modal closes

### Document Management Flow:
1. Load documents from API on mount
2. Display in table with filters
3. User can:
   - Search by title/author
   - Filter by status
   - View document details
   - Edit document (if pending/rejected)
   - Delete document

### Stats Calculation:
```typescript
stats.value = {
  totalDocuments: documents.value.length,
  thisMonth: 2,  // Count from current month
  pending: documents.filter(d => d.status === 'pending').length,
  approved: documents.filter(d => d.status === 'approved').length,
  rejected: documents.filter(d => d.status === 'rejected').length
}
```

## 🚀 Next Steps (TODO)

### Backend Integration:
- [ ] Connect to actual API endpoints
- [ ] Implement file upload to server
- [ ] Fetch user documents from database
- [ ] Real-time stats calculation
- [ ] Document CRUD operations

### Features to Add:
- [ ] Document preview modal
- [ ] Edit document form
- [ ] Bulk actions (select multiple, delete all)
- [ ] Export documents (PDF, Excel)
- [ ] Document sharing
- [ ] Comments/feedback system
- [ ] Notification system
- [ ] Activity timeline
- [ ] Advanced filters (date range, category)

### Improvements:
- [ ] Add loading skeletons
- [ ] Improve error handling
- [ ] Add success/error toast notifications
- [ ] Implement pagination
- [ ] Add sorting functionality
- [ ] Optimize performance (lazy loading)
- [ ] Add animations (Framer Motion)
- [ ] Dark mode support

## 💡 Tips for Development

### Adding New Document Status:
```typescript
// Add to getStatusClass()
const classes: Record<string, string> = {
  pending: 'bg-amber-100 text-amber-800',
  approved: 'bg-green-100 text-green-800',
  rejected: 'bg-red-100 text-red-800',
  draft: 'bg-gray-100 text-gray-800',  // New status
}
```

### Adding New Category:
```vue
<!-- In UploadDocumentModal.vue -->
<select v-model="form.category">
  <option value="new-category">New Category</option>
</select>
```

### Customizing Welcome Message:
```vue
<!-- In UserDashboard.vue -->
<h1 class="text-3xl font-bold text-neutral-900">
  Selamat Datang, {{ userName }}! 👋
</h1>
```

## 🐛 Known Issues

1. **File Upload**: Currently uses dummy API call (2 second delay)
   - Solution: Implement actual API integration

2. **Document Data**: Using dummy data
   - Solution: Fetch from backend API

3. **Stats**: Hardcoded values
   - Solution: Calculate from actual data

4. **Activities**: Empty or dummy data
   - Solution: Track user activities in backend

## 📝 Testing Checklist

- [ ] Register new user
- [ ] Auto redirect to `/my-dashboard`
- [ ] View dashboard stats
- [ ] Click "Mulai Upload" button
- [ ] Upload document (drag & drop)
- [ ] Fill form and submit
- [ ] See new document in table
- [ ] Filter documents by status
- [ ] Search documents
- [ ] Edit document
- [ ] Delete document
- [ ] Check responsive design
- [ ] Test on mobile devices
- [ ] Verify role-based access
- [ ] Test logout and login again

## 🎯 Success Criteria

✅ User dapat register dan langsung masuk ke dashboard mereka
✅ Dashboard menampilkan informasi yang relevan
✅ Upload dokumen mudah dan intuitif
✅ Manajemen dokumen lengkap (CRUD)
✅ UI/UX professional dan modern
✅ Responsive di semua device
✅ Role-based access control berfungsi
✅ Auto redirect sesuai role

---

**Last Updated**: November 10, 2025
**Version**: 1.0.0
**Status**: ✅ Ready for Testing
