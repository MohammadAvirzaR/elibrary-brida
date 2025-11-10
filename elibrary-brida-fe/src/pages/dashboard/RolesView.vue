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
        <a href="/users" class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group">
          <i-lucide-user class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            User
          </span>
        </a>
        <router-link to="/roles" class="flex items-center gap-4 px-6 py-3 bg-blue-800 border-l-4 border-white group">
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

      <!-- Roles Content -->
      <main class="p-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Roles</h1>
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
              <div v-if="selectedRoles.length > 0" class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-700">
                  Selected <span class="ml-2 font-bold">{{ selectedRoles.length }}</span>
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

        <!-- Roles Table -->
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
                Name
              </th>
              <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                Description
              </th>
              <th scope="col" class="w-24 px-6 py-4"></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="role in filteredRoles"
              :key="role.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-6 py-4">
                <input
                  type="checkbox"
                  :checked="selectedRoles.includes(role.id)"
                  @change="toggleRole(role.id)"
                  class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
              </td>
              <td class="px-6 py-4 text-sm text-gray-900">
                {{ role.name }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ role.description }}
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3 justify-end">
                  <button
                    @click="openEditModal(role)"
                    class="text-orange-500 hover:text-orange-600 transition-colors"
                  >
                    <i-lucide-pencil class="h-5 w-5" />
                  </button>
                  <button
                    @click="handleDelete(role)"
                    class="text-red-500 hover:text-red-600 transition-colors"
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
                  'px-3 py-1 text-sm rounded transition-colors',
                  currentPage === 1
                    ? 'text-gray-400 cursor-not-allowed'
                    : 'text-gray-700 hover:bg-gray-200'
                ]"
              >
                <span class="inline-flex items-center gap-1">
                  <i-lucide-chevron-left class="h-4 w-4" />
                  Previous
                </span>
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
                  'px-3 py-1 text-sm rounded transition-colors',
                  currentPage === totalPages
                    ? 'text-gray-400 cursor-not-allowed'
                    : 'text-gray-700 hover:bg-gray-200'
                ]"
              >
                <span class="inline-flex items-center gap-1">
                  Next
                  <i-lucide-chevron-right class="h-4 w-4" />
                </span>
              </button>
            </div>
          </div>
        </div>
      </div>
      </main>
    </div>

    <!-- Add/Edit Role Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900">
            {{ isEditMode ? 'Edit Role' : 'Tambah Role Baru' }}
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <i-lucide-x class="h-6 w-6" />
          </button>
        </div>

        <!-- Modal Body -->
        <form @submit.prevent="handleSubmit" class="p-6">
          <!-- Role Name -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Role Name <span class="text-red-500">*</span>
            </label>
            <input
              v-model="formData.name"
              type="text"
              required
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="e.g., Super Admin, Admin, Contributor"
            />
          </div>

          <!-- Description -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Description <span class="text-red-500">*</span>
            </label>
            <textarea
              v-model="formData.description"
              required
              rows="3"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Describe the role and its responsibilities"
            ></textarea>
          </div>

          <!-- Permissions -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">
              Permissions
            </label>
            <div class="space-y-3 bg-gray-50 p-4 rounded-lg">
              <div
                v-for="permission in availablePermissions"
                :key="permission.key"
                class="flex items-start"
              >
                <input
                  :id="`permission-${permission.key}`"
                  v-model="formData.permissions"
                  :value="permission.key"
                  type="checkbox"
                  class="h-4 w-4 mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <label
                  :for="`permission-${permission.key}`"
                  class="ml-3 cursor-pointer"
                >
                  <div class="text-sm font-medium text-gray-900">
                    {{ permission.label }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ permission.description }}
                  </div>
                </label>
              </div>
            </div>
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
              {{ isEditMode ? 'Simpan Perubahan' : 'Tambah Role' }}
            </button>
          </div>
        </form>
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
          <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Role</h3>
          <p class="text-sm text-gray-600">
            Apakah Anda yakin ingin menghapus role ini? Tindakan ini tidak dapat dibatalkan.
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
interface Role {
  id: number
  name: string
  description: string
  permissions: string[]
}

interface Permission {
  key: string
  label: string
  description: string
}

// Sidebar & User State
const isSidebarOpen = ref(true)
const username = ref('')
const topSearchQuery = ref('')

// Get user from localStorage
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

// Available Permissions
const availablePermissions: Permission[] = [
  {
    key: 'manage_users',
    label: 'Kelola User',
    description: 'Dapat membuat, mengedit, dan menghapus user'
  },
  {
    key: 'manage_roles',
    label: 'Kelola Roles',
    description: 'Dapat mengelola role dan permissions'
  },
  {
    key: 'upload_documents',
    label: 'Upload Dokumen',
    description: 'Dapat mengunggah dokumen baru ke sistem'
  },
  {
    key: 'review_documents',
    label: 'Review Dokumen',
    description: 'Dapat mereview dan menyetujui dokumen'
  },
  {
    key: 'approve_documents',
    label: 'Approve Dokumen',
    description: 'Dapat menyetujui atau menolak dokumen'
  },
  {
    key: 'delete_documents',
    label: 'Hapus Dokumen',
    description: 'Dapat menghapus dokumen dari sistem'
  },
  {
    key: 'view_analytics',
    label: 'Lihat Analytics',
    description: 'Dapat melihat analytics dan statistik sistem'
  },
  {
    key: 'manage_categories',
    label: 'Kelola Kategori',
    description: 'Dapat mengelola kategori dokumen'
  }
]

// State - Data will be loaded from API
const roles = ref<Role[]>([])
const isLoading = ref(false)
const loadError = ref('')

// State
const searchQuery = ref('')
const selectedRoles = ref<number[]>([])
const showModal = ref(false)
const showDeleteModal = ref(false)
const isEditMode = ref(false)
const currentPage = ref(1)
const rowsPerPage = ref(5)
const roleToDelete = ref<Role | null>(null)

// Form Data
const formData = ref({
  id: 0,
  name: '',
  description: '',
  permissions: [] as string[]
})

// Computed
const filteredRoles = computed(() => {
  let filtered = roles.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(
      role =>
        role.name.toLowerCase().includes(query) ||
        role.description.toLowerCase().includes(query)
    )
  }

  const start = (currentPage.value - 1) * rowsPerPage.value
  const end = start + rowsPerPage.value
  return filtered.slice(start, end)
})

const totalPages = computed(() => {
  const total = roles.value.length
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
  return filteredRoles.value.length > 0 &&
    filteredRoles.value.every(role => selectedRoles.value.includes(role.id))
})

// Methods
const toggleSelectAll = () => {
  if (isAllSelected.value) {
    const currentIds = filteredRoles.value.map(r => r.id)
    selectedRoles.value = selectedRoles.value.filter(id => !currentIds.includes(id))
  } else {
    const currentIds = filteredRoles.value.map(r => r.id)
    selectedRoles.value = [...new Set([...selectedRoles.value, ...currentIds])]
  }
}

const toggleRole = (id: number) => {
  const index = selectedRoles.value.indexOf(id)
  if (index > -1) {
    selectedRoles.value.splice(index, 1)
  } else {
    selectedRoles.value.push(id)
  }
}

// API Functions
const loadRoles = async () => {
  isLoading.value = true
  loadError.value = ''
  try {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const response: any = await api.roles.getAll()
    if (response.success && response.data) {
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      roles.value = response.data.map((role: any) => ({
        id: role.id,
        name: role.name,
        description: role.description || '',
        permissions: role.permissions || []
      }))
    }
  } catch (error) {
    console.error('Failed to load roles:', error)
    loadError.value = error instanceof Error ? error.message : 'Failed to load roles'
  } finally {
    isLoading.value = false
  }
}

const openAddModal = () => {
  isEditMode.value = false
  formData.value = {
    id: 0,
    name: '',
    description: '',
    permissions: []
  }
  showModal.value = true
}

const openEditModal = (role: Role) => {
  isEditMode.value = true
  formData.value = {
    id: role.id,
    name: role.name,
    description: role.description,
    permissions: [...role.permissions]
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  formData.value = {
    id: 0,
    name: '',
    description: '',
    permissions: []
  }
}

const handleSubmit = async () => {
  try {
    if (isEditMode.value) {
      // Update existing role via API
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const response: any = await api.roles.update(formData.value.id, {
        name: formData.value.name,
        description: formData.value.description,
        permissions: formData.value.permissions
      })

      if (response.success) {
        // Reload roles to get updated data
        await loadRoles()
      }
    } else {
      // Add new role via API
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const response: any = await api.roles.create({
        name: formData.value.name,
        description: formData.value.description,
        permissions: formData.value.permissions
      })

      if (response.success) {
        // Reload roles to get new data
        await loadRoles()
      }
    }

    closeModal()
  } catch (error) {
    console.error('Failed to save role:', error)
    alert(error instanceof Error ? error.message : 'Failed to save role')
  }
}

const handleDelete = (role: Role) => {
  roleToDelete.value = role
  showDeleteModal.value = true
}

const handleBulkDelete = () => {
  if (selectedRoles.value.length > 0) {
    // For bulk delete, show confirmation with count
    showDeleteModal.value = true
  }
}

const confirmDelete = async () => {
  try {
    if (roleToDelete.value) {
      // Delete single role via API
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const response: any = await api.roles.delete(roleToDelete.value.id)
      if (response.success) {
        await loadRoles()
        selectedRoles.value = selectedRoles.value.filter(id => id !== roleToDelete.value!.id)
      }
    } else if (selectedRoles.value.length > 0) {
      // Delete multiple roles - call API for each
      await Promise.all(
        selectedRoles.value.map(id => api.roles.delete(id))
      )
      await loadRoles()
      selectedRoles.value = []
    }

    closeDeleteModal()
  } catch (error) {
    console.error('Failed to delete role:', error)
    alert(error instanceof Error ? error.message : 'Failed to delete role')
  }
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  roleToDelete.value = null
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

// Auto-refresh interval
let refreshInterval: number | null = null

// Load roles on component mount
onMounted(() => {
  loadRoles()

  // Setup auto-refresh every 30 seconds
  refreshInterval = window.setInterval(() => {
    loadRoles()
  }, 30000) // 30 seconds
})

// Cleanup interval on unmount
onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
  }
})
</script>
