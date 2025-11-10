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
        <router-link to="/users" class="flex items-center gap-4 px-6 py-3 bg-blue-800 border-l-4 border-white group">
          <i-lucide-user class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            User
          </span>
        </router-link>
        <router-link to="/roles" class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group">
          <i-lucide-shield-check class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Role
          </span>
        </router-link>
        <a href="#" class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group">
          <i-lucide-user-circle class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Profile
          </span>
        </a>
        <a href="#" class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group">
          <i-lucide-settings class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Setting
          </span>
        </a>
        <a href="#" class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group">
          <i-lucide-bell class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Notification
          </span>
        </a>
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
              placeholder="Search"
              class="w-full pl-10 pr-4 py-2 rounded-lg border-0 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <!-- User Info -->
          <div class="flex items-center gap-4">
            <div class="text-right">
              <p class="font-bold text-gray-800">{{ username || 'Moni Roy' }}</p>
              <p class="text-sm text-gray-700">Admin</p>
            </div>
            <img
              src="https://i.pravatar.cc/150?img=5"
              alt="Profile"
              class="w-12 h-12 rounded-full border-2 border-white shadow-md"
            />
          </div>
        </div>
      </header>

      <!-- Users Content -->
      <main class="p-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Daftar Pengguna</h1>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
          <div class="border-b border-gray-200">
            <nav class="-mb-px flex gap-8">
              <button
                @click="activeTab = 'list'"
                :class="[
                  'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                  activeTab === 'list'
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                Daftar Pengguna
              </button>
              <button
                @click="activeTab = 'new'"
                :class="[
                  'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                  activeTab === 'new'
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                Pengguna Baru
              </button>
            </nav>
          </div>
        </div>

        <!-- Search and Actions Bar -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
          <div class="flex items-center justify-between">
            <!-- Search -->
            <div class="flex items-center gap-4 flex-1">
              <div class="relative max-w-md flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i-lucide-menu class="h-5 w-5 text-gray-400" />
                </div>
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Hinted search text"
                  class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                  <i-lucide-search class="h-5 w-5 text-gray-400" />
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4">
              <div v-if="selectedUsers.length > 0" class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-700">
                  Selected <span class="ml-2 font-bold">{{ selectedUsers.length }}</span>
                </span>
                <button
                  @click="handleBulkDelete"
                  class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
                >
                  Delete
                </button>
              </div>
              <button
                @click="openAddModal"
                class="px-6 py-2.5 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors font-medium inline-flex items-center gap-2"
              >
                <i-lucide-plus class="h-5 w-5" />
                Tambah
              </button>
            </div>
          </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="w-12 px-6 py-4">
                  <input
                    type="checkbox"
                    :checked="isAllSelected"
                    @change="toggleSelectAll"
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                </th>
                <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                  Username
                </th>
                <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                  Email
                </th>
                <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                  Unit/Instansi
                </th>
                <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                  Role
                </th>
                <th scope="col" class="w-32 px-6 py-4"></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="user in filteredUsers"
                :key="user.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-6 py-4">
                  <input
                    type="checkbox"
                    :checked="selectedUsers.includes(user.id)"
                    @change="toggleUser(user.id)"
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                  {{ user.name }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ user.email }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ user.institution }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                  {{ user.role }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3 justify-end">
                    <button
                      @click="viewUser(user)"
                      class="text-green-500 hover:text-green-600 transition-colors"
                      title="View"
                    >
                      <i-lucide-eye class="h-5 w-5" />
                    </button>
                    <button
                      @click="openEditModal(user)"
                      class="text-orange-500 hover:text-orange-600 transition-colors"
                      title="Edit"
                    >
                      <i-lucide-pencil class="h-5 w-5" />
                    </button>
                    <button
                      @click="handleDelete(user)"
                      class="text-red-500 hover:text-red-600 transition-colors"
                      title="Delete"
                    >
                      <i-lucide-trash-2 class="h-5 w-5" />
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
                {{ rowsPerPage }} row per page
              </div>
              <div class="flex items-center gap-2">
                <button
                  @click="previousPage"
                  :disabled="currentPage === 1"
                  :class="[
                    'px-3 py-1 text-sm rounded transition-colors inline-flex items-center gap-1',
                    currentPage === 1
                      ? 'text-gray-400 cursor-not-allowed'
                      : 'text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  <i-lucide-chevron-left class="h-4 w-4" />
                  Previous
                </button>

                <button
                  v-for="page in displayPages"
                  :key="page"
                  @click="goToPage(page)"
                  :class="[
                    'px-3 py-1 text-sm rounded transition-colors',
                    page === currentPage
                      ? 'bg-black text-white'
                      : 'text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  {{ page }}
                </button>

                <span v-if="totalPages > 5" class="px-2 text-gray-400">...</span>

                <button
                  v-if="totalPages > 5"
                  @click="goToPage(totalPages)"
                  :class="[
                    'px-3 py-1 text-sm rounded transition-colors',
                    totalPages === currentPage
                      ? 'bg-black text-white'
                      : 'text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  {{ totalPages }}
                </button>

                <button
                  @click="nextPage"
                  :disabled="currentPage === totalPages"
                  :class="[
                    'px-3 py-1 text-sm rounded transition-colors inline-flex items-center gap-1',
                    currentPage === totalPages
                      ? 'text-gray-400 cursor-not-allowed'
                      : 'text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  Next
                  <i-lucide-chevron-right class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Add/Edit User Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900">
            {{ isEditMode ? 'Edit User' : 'Tambah User Baru' }}
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <i-lucide-x class="h-6 w-6" />
          </button>
        </div>

        <!-- Modal Body -->
        <form @submit.prevent="handleSubmit" class="p-6">
          <!-- Name -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nama Lengkap <span v-if="!isEditMode" class="text-red-500">*</span>
            </label>
            <input
              v-model="formData.name"
              type="text"
              :required="!isEditMode"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="John Doe"
            />
            <p v-if="isEditMode && !formData.name" class="mt-1 text-xs text-gray-500">
              Kosongkan jika tidak ingin mengubah
            </p>
          </div>

          <!-- Email -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Email <span v-if="!isEditMode" class="text-red-500">*</span>
            </label>
            <input
              v-model="formData.email"
              type="email"
              :required="!isEditMode"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="john@example.com"
            />
            <p v-if="isEditMode && !formData.email" class="mt-1 text-xs text-gray-500">
              Kosongkan jika tidak ingin mengubah
            </p>
          </div>

          <!-- Institution -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Unit/Instansi <span v-if="!isEditMode" class="text-red-500">*</span>
            </label>
            <input
              v-model="formData.institution"
              type="text"
              :required="!isEditMode"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="BRIDA Sulawesi Tenggara"
            />
            <p v-if="isEditMode && !formData.institution" class="mt-1 text-xs text-gray-500">
              Kosongkan jika tidak ingin mengubah
            </p>
          </div>

          <!-- Role Selection -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Role <span class="text-red-500">*</span>
            </label>
            <select
              v-model="formData.role"
              required
              :disabled="isEditingSuperAdmin && currentUserRole !== 'super_admin'"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
            >
              <option value="">Pilih Role</option>
              <option v-for="role in roles" :key="role.id" :value="role.name">
                {{ getRoleLabel(role.name) }}
              </option>
            </select>
            <p v-if="isEditingSuperAdmin && currentUserRole !== 'super_admin'" class="mt-2 text-xs text-amber-600">
              ⚠️ Hanya Super Admin yang dapat mengubah role Super Admin
            </p>
            <p v-else-if="isEditMode" class="mt-2 text-xs text-gray-500">
              💡 Role menentukan hak akses user di sistem
            </p>
          </div>

          <!-- Change Password Toggle (only for edit mode) -->
          <div v-if="isEditMode" class="mb-6">
            <label class="flex items-center cursor-pointer">
              <input
                v-model="changePassword"
                type="checkbox"
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              />
              <span class="ml-2 text-sm font-medium text-gray-700">
                Ubah Password
              </span>
            </label>
          </div>

          <!-- Password (for new user or when changing password) -->
          <div v-if="!isEditMode || changePassword" class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Password <span class="text-red-500">*</span>
            </label>
            <input
              v-model="formData.password"
              type="password"
              :required="!isEditMode || changePassword"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Minimal 8 karakter"
            />
          </div>

          <!-- Password Confirmation (for new user or when changing password) -->
          <div v-if="!isEditMode || changePassword" class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Konfirmasi Password <span class="text-red-500">*</span>
            </label>
            <input
              v-model="formData.password_confirmation"
              type="password"
              :required="!isEditMode || changePassword"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Ulangi password"
            />
          </div>

          <!-- Modal Actions -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
            >
              {{ isEditMode ? 'Simpan Perubahan' : 'Tambah User' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- View User Modal -->
    <div
      v-if="showViewModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeViewModal"
    >
      <div class="bg-white rounded-xl shadow-xl max-w-lg w-full">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900">Detail User</h2>
          <button @click="closeViewModal" class="text-gray-400 hover:text-gray-600">
            <i-lucide-x class="h-6 w-6" />
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6" v-if="viewedUser">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
              <p class="text-base text-gray-900">{{ viewedUser.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
              <p class="text-base text-gray-900">{{ viewedUser.email }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">Unit/Instansi</label>
              <p class="text-base text-gray-900">{{ viewedUser.institution }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">Role</label>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                {{ viewedUser.role }}
              </span>
            </div>
          </div>

          <div class="mt-6 pt-4 border-t border-gray-200">
            <button
              @click="closeViewModal"
              class="w-full px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeDeleteModal"
    >
      <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <div class="text-center mb-6">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <i-lucide-alert-triangle class="h-6 w-6 text-red-600" />
          </div>
          <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus User</h3>
          <p class="text-sm text-gray-600">
            Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.
          </p>
        </div>
        <div class="flex items-center gap-3">
          <button
            @click="closeDeleteModal"
            class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
          >
            Batal
          </button>
          <button
            @click="confirmDelete"
            class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
          >
            Hapus
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()

// Types
interface User {
  id: number
  name: string
  email: string
  institution: string
  role: string
}

// Sidebar & User State
const isSidebarOpen = ref(true)
const username = ref('')
const topSearchQuery = ref('')

// Auto-refresh interval
let refreshInterval: number | null = null

// Get user from localStorage and load data
onMounted(() => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    try {
      const user = JSON.parse(userStr)
      username.value = user.name || 'Admin'
    } catch {
      username.value = 'Admin'
    }
  }

  // Get current user role for permission checking
  getCurrentUserRole()

  // Load users and roles from API
  loadUsers()
  loadRoles()

  // Setup auto-refresh every 30 seconds to detect new users
  refreshInterval = window.setInterval(() => {
    loadUsers()
  }, 30000) // 30 seconds
})

// Cleanup interval on unmount
onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
  }
})

// Sidebar Methods
const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const logout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  router.push('/login')
}

// Data - Will be loaded from API
const users = ref<User[]>([])
const roles = ref<{ id: number; name: string }[]>([])
const isLoading = ref(false)
const loadError = ref('')

// State
const activeTab = ref('list')
const searchQuery = ref('')
const selectedUsers = ref<number[]>([])
const showModal = ref(false)
const showViewModal = ref(false)
const showDeleteModal = ref(false)
const isEditMode = ref(false)
const currentPage = ref(1)
const rowsPerPage = ref(5)
const userToDelete = ref<User | null>(null)
const viewedUser = ref<User | null>(null)

// Form Data
const formData = ref({
  id: 0,
  name: '',
  email: '',
  institution: '',
  role: '',
  password: '',
  password_confirmation: ''
})

// Additional state for flexible role editing
const changePassword = ref(false)
const currentUserRole = ref<string>('')

// Get current user role from localStorage
const getCurrentUserRole = () => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    try {
      const user = JSON.parse(userStr)
      currentUserRole.value = user.role || ''
    } catch {
      currentUserRole.value = ''
    }
  }
}

// Check if editing a super_admin user
const isEditingSuperAdmin = computed(() => {
  return isEditMode.value && formData.value.role === 'super_admin'
})

// Get user-friendly role labels
const getRoleLabel = (roleName: string): string => {
  const roleLabels: Record<string, string> = {
    'super_admin': 'Super Admin',
    'admin': 'Admin',
    'contributor': 'Contributor',
    'reviewer': 'Reviewer',
    'guest': 'Guest'
  }
  return roleLabels[roleName] || roleName
}

// Computed
const filteredUsers = computed(() => {
  let filtered = users.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(
      user =>
        user.name.toLowerCase().includes(query) ||
        user.email.toLowerCase().includes(query) ||
        user.institution.toLowerCase().includes(query) ||
        user.role.toLowerCase().includes(query)
    )
  }

  const start = (currentPage.value - 1) * rowsPerPage.value
  const end = start + rowsPerPage.value
  return filtered.slice(start, end)
})

const totalPages = computed(() => {
  const total = users.value.length
  return Math.ceil(total / rowsPerPage.value)
})

const displayPages = computed(() => {
  const pages = []
  const maxDisplay = 3

  for (let i = 1; i <= Math.min(maxDisplay, totalPages.value); i++) {
    pages.push(i)
  }

  return pages
})

const isAllSelected = computed(() => {
  return filteredUsers.value.length > 0 &&
    filteredUsers.value.every(user => selectedUsers.value.includes(user.id))
})

// Methods
const toggleSelectAll = () => {
  if (isAllSelected.value) {
    const currentIds = filteredUsers.value.map(u => u.id)
    selectedUsers.value = selectedUsers.value.filter(id => !currentIds.includes(id))
  } else {
    const currentIds = filteredUsers.value.map(u => u.id)
    selectedUsers.value = [...new Set([...selectedUsers.value, ...currentIds])]
  }
}

const toggleUser = (id: number) => {
  const index = selectedUsers.value.indexOf(id)
  if (index > -1) {
    selectedUsers.value.splice(index, 1)
  } else {
    selectedUsers.value.push(id)
  }
}

// API Functions
const loadUsers = async () => {
  isLoading.value = true
  loadError.value = ''
  try {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const response: any = await api.users.getAll()
    if (response.success && response.data) {
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      users.value = response.data.map((user: any) => ({
        id: user.id,
        name: user.name,
        email: user.email,
        institution: user.institution || '',
        role: user.role || 'Guest'
      }))
    }
  } catch (error) {
    console.error('Failed to load users:', error)
    loadError.value = error instanceof Error ? error.message : 'Failed to load users'
  } finally {
    isLoading.value = false
  }
}

const loadRoles = async () => {
  try {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const response: any = await api.roles.getAll()

    if (response.success && response.data) {
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      roles.value = response.data.map((role: any) => ({
        id: role.id,
        name: role.name
      }))
    }
  } catch (error) {
    console.error('Failed to load roles:', error)
  }
}

const openAddModal = () => {
  isEditMode.value = false
  formData.value = {
    id: 0,
    name: '',
    email: '',
    institution: '',
    role: '',
    password: '',
    password_confirmation: ''
  }
  showModal.value = true
}

const openEditModal = (user: User) => {
  isEditMode.value = true
  changePassword.value = false // Reset password change flag
  formData.value = {
    id: user.id,
    name: user.name,
    email: user.email,
    institution: user.institution,
    role: user.role,
    password: '',
    password_confirmation: ''
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  changePassword.value = false
  formData.value = {
    id: 0,
    name: '',
    email: '',
    institution: '',
    role: '',
    password: '',
    password_confirmation: ''
  }
}

const viewUser = (user: User) => {
  viewedUser.value = user
  showViewModal.value = true
}

const closeViewModal = () => {
  showViewModal.value = false
  viewedUser.value = null
}

const handleSubmit = async () => {
  // Validate password match for new user or when changing password
  if (!isEditMode.value || changePassword.value) {
    if (formData.value.password !== formData.value.password_confirmation) {
      alert('Password tidak cocok!')
      return
    }
    if (formData.value.password.length < 8) {
      alert('Password minimal 8 karakter!')
      return
    }
  }

  try {
    if (isEditMode.value) {
      // Find role_id from role name
      const role = roles.value.find(r => r.name === formData.value.role)
      if (!role) {
        alert('Role tidak valid!')
        return
      }

      // Prepare update payload - hanya kirim field yang diisi
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const updatePayload: any = {}

      // Hanya tambahkan field yang diisi/diubah
      if (formData.value.name && formData.value.name.trim()) {
        updatePayload.name = formData.value.name
      }
      if (formData.value.email && formData.value.email.trim()) {
        updatePayload.email = formData.value.email
      }
      if (formData.value.institution && formData.value.institution.trim()) {
        updatePayload.institution = formData.value.institution
      }

      // Role selalu dikirim karena wajib
      updatePayload.role_id = role.id

      // Include password only if changing
      if (changePassword.value && formData.value.password) {
        updatePayload.password = formData.value.password
      }

      // Update existing user via API
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const response: any = await api.users.update(formData.value.id, updatePayload)

      if (response.success) {
        await loadUsers()
        alert('User berhasil diupdate!')
      }
    } else {
      // Validasi untuk add user baru - semua field wajib
      if (!formData.value.name || !formData.value.email || !formData.value.institution) {
        alert('Semua field wajib diisi untuk user baru!')
        return
      }

      // Find role_id from role name
      const role = roles.value.find(r => r.name === formData.value.role)
      if (!role) {
        alert('Role tidak valid!')
        return
      }

      // Add new user via API
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const response: any = await api.users.create({
        name: formData.value.name,
        email: formData.value.email,
        institution: formData.value.institution,
        password: formData.value.password,
        role_id: role.id
      })

      if (response.success) {
        await loadUsers()
        alert('User berhasil ditambahkan!')
      }
    }

    closeModal()
  } catch (error) {
    console.error('Failed to save user:', error)
    alert(error instanceof Error ? error.message : 'Failed to save user')
  }
}

const handleDelete = (user: User) => {
  userToDelete.value = user
  showDeleteModal.value = true
}

const handleBulkDelete = () => {
  if (selectedUsers.value.length > 0) {
    showDeleteModal.value = true
  }
}

const confirmDelete = async () => {
  try {
    if (userToDelete.value) {
      // Delete single user via API
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const response: any = await api.users.delete(userToDelete.value.id)
      if (response.success) {
        await loadUsers()
        selectedUsers.value = selectedUsers.value.filter(id => id !== userToDelete.value!.id)
      }
    } else if (selectedUsers.value.length > 0) {
      // Delete multiple users - call API for each
      await Promise.all(
        selectedUsers.value.map(id => api.users.delete(id))
      )
      await loadUsers()
      selectedUsers.value = []
    }

    closeDeleteModal()
  } catch (error) {
    console.error('Failed to delete user:', error)
    alert(error instanceof Error ? error.message : 'Failed to delete user')
  }
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  userToDelete.value = null
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const goToPage = (page: number) => {
  currentPage.value = page
}
</script>
