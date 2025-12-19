<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside
      :class="[
        'fixed left-0 top-0 h-screen bg-gradient-to-b from-blue-700 to-blue-900 text-white shadow-xl transition-all duration-300 ease-in-out z-20',
        isSidebarOpen ? 'w-60' : 'w-20'
      ]"
    >
      <!-- Toggle Button -->
      <div class="flex items-center justify-center p-6 border-b border-blue-600">
        <button
          @click="toggleSidebar"
          class="p-2 hover:bg-blue-600 rounded-lg transition"
          :title="isSidebarOpen ? 'Tutup Sidebar' : 'Buka Sidebar'"
        >
          <i-lucide-panel-left-close v-if="isSidebarOpen" class="w-6 h-6 text-white" />
          <i-lucide-panel-left-open v-else class="w-6 h-6 text-white" />
        </button>
      </div>

      <!-- Menu -->
      <nav class="mt-6">
        <router-link to="/dashboard" class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group">
          <i-lucide-layout-dashboard class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Dashboard
          </span>
        </router-link>

        <!-- User Management - Super Admin Only -->
        <router-link
          v-if="userRole === 'super_admin'"
          to="/users"
          class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group"
        >
          <i-lucide-users class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            User Management
          </span>
        </router-link>

        <!-- Role Management - Super Admin Only -->
        <router-link
          v-if="userRole === 'super_admin'"
          to="/roles"
          class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group"
        >
          <i-lucide-shield-check class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Role Management
          </span>
        </router-link>

        <!-- Profile Management - Admin & Super Admin -->
        <router-link
          to="/profile-management"
          class="flex items-center gap-4 px-6 py-3 bg-blue-800 border-l-4 border-white group"
        >
          <i-lucide-user-cog class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Profile Management
          </span>
        </router-link>

        <!-- Contributor Requests - Super Admin Only -->
        <router-link
          v-if="userRole === 'super_admin'"
          to="/contributor-requests"
          class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group"
        >
          <i-lucide-file-check class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Contributor Requests
          </span>
        </router-link>

        <button @click="logout" class="w-full flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition mt-4 group">
          <i-lucide-log-out class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Log Out
          </span>
        </button>
      </nav>
    </aside>

    <!-- Main Content -->
    <div
      :class="[
        'transition-all duration-300 ease-in-out',
        isSidebarOpen ? 'ml-60' : 'ml-20'
      ]"
    >
      <!-- Top Bar -->
      <header class="bg-gradient-to-r from-blue-400 to-blue-300 shadow-md sticky top-0 z-10">
        <div class="flex items-center justify-between px-8 py-4">
          <!-- Search -->
          <div class="relative w-96">
            <i-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" />
            <input
              v-model="topSearchQuery"
              type="text"
              placeholder="Search users..."
              class="w-full pl-10 pr-4 py-2 rounded-lg border-0 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <!-- User Info -->
          <div class="flex items-center gap-4">
            <div class="text-right">
              <p class="font-bold text-gray-800">{{ username || 'Admin' }}</p>
              <p class="text-sm text-gray-700 capitalize">{{ userRole || 'admin' }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
              {{ username ? username.charAt(0).toUpperCase() : 'A' }}
            </div>
          </div>
        </div>
      </header>

      <!-- Profile Management Content -->
      <main class="p-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Profile Management</h1>
          <p class="text-gray-600 mt-2">Manage user profiles, update information, and reset passwords</p>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
          <div class="flex items-center gap-4">
            <!-- Search -->
            <div class="relative flex-1 max-w-md">
              <i-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by name, email, or institution..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>

            <!-- Role Filter -->
            <select
              v-model="roleFilter"
              class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Roles</option>
              <option value="super_admin">Super Admin</option>
              <option value="admin">Admin</option>
              <option value="contributor">Contributor</option>
              <option value="reviewer">Reviewer</option>
              <option value="guest">Guest</option>
            </select>
          </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
          <div v-if="isLoading" class="p-8 text-center text-gray-500">
            Loading users...
          </div>

          <div v-else-if="loadError" class="p-8 text-center text-red-500">
            {{ loadError }}
          </div>

          <table v-else class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                  User
                </th>
                <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                  Email
                </th>
                <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                  Institution
                </th>
                <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                  Role
                </th>
                <th scope="col" class="w-32 px-6 py-4 text-center text-sm font-medium text-gray-500">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="user in filteredUsers"
                :key="user.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ user.name }}
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ user.email }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ user.institution || '-' }}
                </td>
                <td class="px-6 py-4">
                  <span
                    :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      getRoleBadgeClass(user.role)
                    ]"
                  >
                    {{ formatRole(user.role) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2">
                    <button
                      @click="openEditModal(user)"
                      class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                      title="Edit Profile"
                    >
                      <i-lucide-pencil class="w-4 h-4" />
                    </button>
                    <button
                      @click="openResetPasswordModal(user)"
                      class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                      title="Reset Password"
                    >
                      <i-lucide-key class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-600">
                Showing {{ filteredUsers.length }} of {{ users.length }} users
              </div>
              <div class="text-sm text-gray-600">
                {{ rowsPerPage }} rows per page
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Edit Profile Modal -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeEditModal"
    >
      <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto no-scrollbar">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">Edit User Profile</h2>
          <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600">
            <i-lucide-x class="h-6 w-6" />
          </button>
        </div>

        <!-- Modal Body -->
        <form @submit.prevent="handleUpdateProfile" class="p-6">
          <!-- Name -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Full Name <span class="text-red-500">*</span>
            </label>
            <input
              v-model="editForm.name"
              type="text"
              required
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Enter full name"
            />
          </div>

          <!-- Email -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Email <span class="text-red-500">*</span>
            </label>
            <input
              v-model="editForm.email"
              type="email"
              required
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="user@example.com"
            />
          </div>

          <!-- Institution -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Institution
            </label>
            <input
              v-model="editForm.institution"
              type="text"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Enter institution name"
            />
          </div>

          <!-- Phone -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Phone Number
            </label>
            <input
              v-model="editForm.phone"
              type="tel"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="+62 xxx xxxx xxxx"
            />
          </div>

          <!-- Address -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Address
            </label>
            <textarea
              v-model="editForm.address"
              rows="3"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Enter address"
            ></textarea>
          </div>

          <!-- Modal Actions -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeEditModal"
              class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
            >
              Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Reset Password Modal -->
    <div
      v-if="showResetPasswordModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeResetPasswordModal"
    >
      <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900">Reset Password</h2>
          <button @click="closeResetPasswordModal" class="text-gray-400 hover:text-gray-600">
            <i-lucide-x class="h-6 w-6" />
          </button>
        </div>

        <!-- Modal Body -->
        <form @submit.prevent="handleResetPassword" class="p-6">
          <div class="mb-6">
            <div class="flex items-center gap-3 p-4 bg-orange-50 border border-orange-200 rounded-lg mb-4">
              <i-lucide-alert-triangle class="w-5 h-5 text-orange-600 flex-shrink-0" />
              <p class="text-sm text-orange-800">
                Reset password for <strong>{{ resetPasswordUser?.name }}</strong>
              </p>
            </div>

            <label class="block text-sm font-medium text-gray-700 mb-2">
              New Password <span class="text-red-500">*</span>
            </label>
            <input
              v-model="resetPasswordForm.password"
              type="password"
              required
              minlength="8"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Enter new password (min 8 characters)"
            />
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Confirm Password <span class="text-red-500">*</span>
            </label>
            <input
              v-model="resetPasswordForm.password_confirmation"
              type="password"
              required
              minlength="8"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Confirm new password"
            />
          </div>

          <!-- Modal Actions -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeResetPasswordModal"
              class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-6 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-medium"
            >
              Reset Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()

// Types
interface User {
  id: number
  name: string
  email: string
  institution: string | null
  phone: string | null
  address: string | null
  role: string
}

// State
const isSidebarOpen = ref(true)
const username = ref('')
const userRole = ref('')
const topSearchQuery = ref('')
const searchQuery = ref('')
const roleFilter = ref('')
const isLoading = ref(false)
const loadError = ref('')
const users = ref<User[]>([])
const rowsPerPage = ref(10)

// Modals
const showEditModal = ref(false)
const showResetPasswordModal = ref(false)
const resetPasswordUser = ref<User | null>(null)

// Forms
const editForm = ref({
  id: 0,
  name: '',
  email: '',
  institution: '',
  phone: '',
  address: ''
})

const resetPasswordForm = ref({
  password: '',
  password_confirmation: ''
})

// Computed
const filteredUsers = computed(() => {
  let filtered = users.value

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(
      user =>
        user.name.toLowerCase().includes(query) ||
        user.email.toLowerCase().includes(query) ||
        (user.institution && user.institution.toLowerCase().includes(query))
    )
  }

  // Role filter
  if (roleFilter.value) {
    filtered = filtered.filter(user => user.role === roleFilter.value)
  }

  return filtered
})

// Methods
const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const logout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  localStorage.removeItem('last_known_role')
  router.push('/login')
}

const formatRole = (role: string): string => {
  const roleMap: Record<string, string> = {
    super_admin: 'Super Admin',
    admin: 'Admin',
    contributor: 'Contributor',
    reviewer: 'Reviewer',
    guest: 'Guest'
  }
  return roleMap[role] || role
}

const getRoleBadgeClass = (role: string): string => {
  const classMap: Record<string, string> = {
    super_admin: 'bg-purple-100 text-purple-800',
    admin: 'bg-blue-100 text-blue-800',
    contributor: 'bg-green-100 text-green-800',
    reviewer: 'bg-yellow-100 text-yellow-800',
    guest: 'bg-gray-100 text-gray-800'
  }
  return classMap[role] || 'bg-gray-100 text-gray-800'
}

const loadUsers = async () => {
  isLoading.value = true
  loadError.value = ''
  console.log('Loading users...') // Debug log
  try {
    console.log('Calling api.users.getAll()') // Debug log
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const response: any = await api.users.getAll()
    console.log('API Response:', response) // Debug log
    if (response.success && response.data) {
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      users.value = response.data.map((user: any) => ({
        id: user.id,
        name: user.name,
        email: user.email,
        institution: user.institution || null,
        phone: user.phone || null,
        address: user.address || null,
        role: user.role
      }))
      console.log('Users loaded successfully:', users.value.length, 'users') // Debug log
    } else {
      console.warn('API response missing success or data:', response) // Debug log
      loadError.value = 'Invalid API response'
    }
  } catch (error) {
    console.error('Failed to load users:', error)
    loadError.value = 'Gagal memuat data profil. Silakan coba lagi.'
  } finally {
    console.log('Loading complete. isLoading:', isLoading.value) // Debug log
    isLoading.value = false
  }
}

const openEditModal = (user: User) => {
  editForm.value = {
    id: user.id,
    name: user.name,
    email: user.email,
    institution: user.institution || '',
    phone: user.phone || '',
    address: user.address || ''
  }
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  editForm.value = {
    id: 0,
    name: '',
    email: '',
    institution: '',
    phone: '',
    address: ''
  }
}

const handleUpdateProfile = async () => {
  try {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const response: any = await api.users.update(editForm.value.id, {
      name: editForm.value.name,
      email: editForm.value.email,
      institution: editForm.value.institution || undefined,
      phone: editForm.value.phone || undefined,
      address: editForm.value.address || undefined
    })

    if (response.success) {
      await loadUsers()
      closeEditModal()
      alert('Profile updated successfully!')
    }
  } catch (error) {
    console.error('Failed to update profile:', error)
    alert('Gagal memperbarui profil. Silakan coba lagi.')
  }
}

const openResetPasswordModal = (user: User) => {
  resetPasswordUser.value = user
  resetPasswordForm.value = {
    password: '',
    password_confirmation: ''
  }
  showResetPasswordModal.value = true
}

const closeResetPasswordModal = () => {
  showResetPasswordModal.value = false
  resetPasswordUser.value = null
  resetPasswordForm.value = {
    password: '',
    password_confirmation: ''
  }
}

const handleResetPassword = async () => {
  if (!resetPasswordUser.value) return

  if (resetPasswordForm.value.password !== resetPasswordForm.value.password_confirmation) {
    alert('Passwords do not match!')
    return
  }

  try {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const response: any = await api.users.update(resetPasswordUser.value.id, {
      password: resetPasswordForm.value.password
    })

    if (response.success) {
      closeResetPasswordModal()
      alert('Password reset successfully!')
    }
  } catch (error) {
    console.error('Failed to reset password:', error)
    alert('Gagal mereset password. Silakan coba lagi.')
  }
}

// Initialize
onMounted(() => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    try {
      const user = JSON.parse(userStr)
      username.value = user.name || 'Admin'
      userRole.value = user.role || ''
    } catch {
      username.value = 'Admin'
      userRole.value = ''
    }
  }

  loadUsers()
})
</script>
