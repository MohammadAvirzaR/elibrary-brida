# 🔐 Authentication Guide - eLibrary API

## Problem
Frontend mendapat error **401 Unauthorized** ketika akses endpoints yang protected seperti:
- `GET /api/users`
- `GET /api/roles`

## Root Cause
Endpoints tersebut memerlukan **Sanctum Authentication Token** di Authorization header.

## Solution

### 1️⃣ Login Terlebih Dahulu

**Endpoint:** `POST /api/login`

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@brida.com",
    "password": "admin123"
  }'
```

**Response:**
```json
{
  "status": "success",
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Admin BRIDA",
    "email": "admin@brida.com",
    "username": "Admin BRIDA",
    "institution": null,
    "role": "super_admin"
  },
  "token": "8|WsFXBPLWcLBKbwJl1LU7HPzoXHotMAEUrApBCDkV6e8241a8"
}
```

### 2️⃣ Simpan Token di localStorage

```javascript
// Setelah login berhasil, simpan token:
localStorage.setItem('auth_token', response.token);
localStorage.setItem('user', JSON.stringify(response.user));
```

### 3️⃣ Kirim Token di Authorization Header

```javascript
// Untuk setiap request yang protected:
const token = localStorage.getItem('auth_token');

fetch('http://127.0.0.1:8000/api/users', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
})
```

## 🧪 Test Users (Seeded)

| Email | Password | Role |
|-------|----------|------|
| admin@brida.com | admin123 | super_admin |
| user@brida.com | user123 | guest |

## Quick Test Token (Development Only)

```bash
# Generate test token untuk admin
php artisan make:test-token admin@brida.com

# Token akan di-display, copy ke localStorage
```

## Protected Endpoints

| Method | Endpoint | Required Role |
|--------|----------|----------------|
| GET | /api/users | super_admin, admin |
| POST | /api/users | super_admin, admin |
| PUT | /api/users/{id} | super_admin, admin |
| DELETE | /api/users/{id} | super_admin, admin |
| GET | /api/roles | super_admin, admin |
| POST | /api/roles | super_admin |
| PUT | /api/roles/{id} | super_admin |
| DELETE | /api/roles/{id} | super_admin |

## Public Endpoints (No Auth Required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/register | Register new user |
| POST | /api/login | Login |
| GET | /api/filters | Get filter options |
| GET | /api/documents | Get all documents |
| GET | /api/documents/{id} | Get specific document |
| GET | /api/documents/search | Search documents |
| GET | /api/documents/featured-content | Get featured content |

## Frontend Implementation (Vue.js Example)

```javascript
// api.ts atau api.js

const API_BASE_URL = 'http://127.0.0.1:8000/api';

function getAuthHeaders() {
  const token = localStorage.getItem('auth_token');
  return {
    'Content-Type': 'application/json',
    ...(token && { 'Authorization': `Bearer ${token}` })
  };
}

async function apiCall(endpoint, options = {}) {
  const url = `${API_BASE_URL}${endpoint}`;
  const headers = getAuthHeaders();

  const response = await fetch(url, {
    ...options,
    headers: {
      ...headers,
      ...options.headers
    }
  });

  if (!response.ok) {
    if (response.status === 401) {
      // Token expired or invalid, redirect to login
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }
    throw new Error(`${response.status} - ${response.statusText}`);
  }

  return response.json();
}

// Usage
export const userApi = {
  getUsers: () => apiCall('/users'),
  getUser: (id) => apiCall(`/users/${id}`),
  updateUser: (id, data) => apiCall(`/users/${id}`, {
    method: 'PUT',
    body: JSON.stringify(data)
  })
};
```

## Troubleshooting

### ❌ "401 - Unauthenticated"
- **Cause**: Token tidak di-send atau token invalid
- **Fix**: Pastikan token di-send di Authorization header dengan format `Bearer {token}`

### ❌ "403 - Forbidden"
- **Cause**: User role tidak sesuai dengan requirement
- **Fix**: Login dengan user yang memiliki role yang tepat

### ❌ "404 - Not Found"
- **Cause**: Endpoint tidak exist atau typo di URL
- **Fix**: Check routing dan pastikan URL benar

## ✅ Next Steps

1. Frontend login dengan `admin@brida.com` / `admin123`
2. Simpan token di localStorage
3. Kirim token di setiap request yang protected
4. Implement token refresh mechanism (optional tapi recommended)
