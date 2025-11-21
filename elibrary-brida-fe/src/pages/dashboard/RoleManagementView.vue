<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside
      :class="[
        'fixed left-0 top-0 h-screen bg-gradient-to-b from-blue-700 to-blue-900 text-white shadow-xl transition-all duration-300 ease-in-out z-20',
        isSidebarOpen ? 'w-64' : 'w-20'
      ]"
    >
      <div class="flex items-center justify-between p-6 border-b border-blue-600">
        <h1 v-if="isSidebarOpen" class="text-xl font-bold">Admin Panel</h1>
        <button @click="toggleSidebar" class="p-2 hover:bg-blue-600 rounded-lg transition">
          <i-lucide-panel-left-close v-if="isSidebarOpen" class="w-6 h-6" />
          <i-lucide-panel-left-open v-else class="w-6 h-6" />
        </button>
      </div>

      <nav class="mt-6">
        <router-link to="/dashboard" class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition">
          <i-lucide-layout-dashboard class="w-5 h-5 flex-shrink-0" />
          <span v-if="isSidebarOpen">Dashboard</span>
        </router-link>
        <router-link to="/users" class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition">
          <i-lucide-users class="w-5 h-5 flex-shrink-0" />
          <span v-if="isSidebarOpen">Users</span>
        </router-link>
        <router-link to="/role-management" class="flex items-center gap-4 px-6 py-3 bg-blue-800 border-l-4 border-white">
          <i-lucide-shield class="w-5 h-5 flex-shrink-0" />
          <span v-if="isSidebarOpen">Roles</span>
        </router-link>
        <button @click="logout" class="w-full flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition mt-auto">
          <i-lucide-log-out class="w-5 h-5 flex-shrink-0" />
          <span v-if="isSidebarOpen">Logout</span>
        </button>
      </nav>
    </aside>

    <!-- Main Content -->
    <main :class="['transition-all duration-300', isSidebarOpen ? 'ml-64' : 'ml-20']">
      <!-- Header -->
      <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
        <div class="px-8 py-6">
          <h1 class="text-3xl font-bold text-gray-900">Role Management</h1>
          <p class="text-gray-600 mt-1">Manage roles and permissions like Discord</p>
        </div>
      </header>

      <div class="p-8">
        <div class="grid grid-cols-12 gap-6">
          <!-- Left Panel - Role List -->
          <div class="col-span-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
              <!-- Header -->
              <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between mb-4">
                  <h2 class="text-xl font-bold text-gray-900">Roles</h2>
                  <button
                    @click="createNewRole"
                    class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                    title="Create Role"
                  >
                    <i-lucide-plus class="w-5 h-5" />
                  </button>
                </div>
                <p class="text-sm text-gray-500">
                  Roles are ordered by hierarchy. Drag to reorder.
                </p>
              </div>

              <!-- Role List -->
              <div class="p-4 space-y-2 max-h-[calc(100vh-300px)] overflow-y-auto">
                <div
                  v-for="role in sortedRoles"
                  :key="role.id"
                  @click="selectRole(role)"
                  draggable="true"
                  @dragstart="handleDragStart($event, role)"
                  @dragover.prevent
                  @drop="handleDrop($event, role)"
                  :class="[
                    'flex items-center gap-3 p-4 rounded-lg cursor-pointer transition-all',
                    selectedRole?.id === role.id
                      ? 'bg-blue-50 border-2 border-blue-500'
                      : 'bg-gray-50 border-2 border-transparent hover:bg-gray-100'
                  ]"
                >
                  <i-lucide-grip-vertical class="w-5 h-5 text-gray-400 cursor-grab" />
                  <div
                    class="w-4 h-4 rounded-full flex-shrink-0"
                    :style="{ backgroundColor: role.color }"
                  ></div>
                  <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate">{{ role.name }}</p>
                    <p class="text-xs text-gray-500">{{ role.members_count }} members</p>
                  </div>
                  <i-lucide-chevron-right class="w-5 h-5 text-gray-400" />
                </div>
              </div>
            </div>
          </div>

          <!-- Right Panel - Role Details -->
          <div class="col-span-8">
            <div v-if="selectedRole" class="bg-white rounded-xl shadow-sm border border-gray-200">
              <!-- Role Header -->
              <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-4">
                    <div
                      class="w-12 h-12 rounded-full flex items-center justify-center"
                      :style="{ backgroundColor: selectedRole.color }"
                    >
                      <i-lucide-shield class="w-6 h-6 text-white" />
                    </div>
                    <div>
                      <h2 class="text-2xl font-bold text-gray-900">{{ selectedRole.name }}</h2>
                      <p class="text-sm text-gray-500">{{ selectedRole.members_count }} members with this role</p>
                    </div>
                  </div>
                  <div class="flex gap-2">
                    <button
                      @click="saveRole"
                      class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2"
                    >
                      <i-lucide-save class="w-4 h-4" />
                      Save Changes
                    </button>
                    <button
                      v-if="!selectedRole.is_default"
                      @click="deleteRole"
                      class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2"
                    >
                      <i-lucide-trash class="w-4 h-4" />
                      Delete
                    </button>
                  </div>
                </div>
              </div>

              <!-- Role Settings -->
              <div class="p-6 space-y-6 max-h-[calc(100vh-280px)] overflow-y-auto">
                <!-- Display Section -->
                <div>
                  <h3 class="text-lg font-bold text-gray-900 mb-4">Display</h3>

                  <!-- Role Name -->
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role Name</label>
                    <input
                      v-model="selectedRole.name"
                      type="text"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      :disabled="selectedRole.is_default"
                    />
                  </div>

                  <!-- Role Color -->
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role Color</label>
                    <div class="flex gap-4">
                      <input
                        v-model="selectedRole.color"
                        type="color"
                        class="w-20 h-10 rounded border border-gray-300 cursor-pointer"
                      />
                      <input
                        v-model="selectedRole.color"
                        type="text"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="#000000"
                      />
                    </div>
                  </div>

                  <!-- Description -->
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea
                      v-model="selectedRole.description"
                      rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="What does this role do?"
                    ></textarea>
                  </div>

                  <!-- Display Options -->
                  <div class="space-y-3">
                    <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                      <input
                        v-model="selectedRole.display_separately"
                        type="checkbox"
                        class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                      <div>
                        <p class="font-medium text-gray-900">Display role members separately</p>
                        <p class="text-sm text-gray-500">Show members with this role in a separate section</p>
                      </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                      <input
                        v-model="selectedRole.mentionable"
                        type="checkbox"
                        class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                      <div>
                        <p class="font-medium text-gray-900">Allow anyone to @mention this role</p>
                        <p class="text-sm text-gray-500">Members will be notified when this role is mentioned</p>
                      </div>
                    </label>
                  </div>
                </div>

                <!-- Permissions Section -->
                <div>
                  <h3 class="text-lg font-bold text-gray-900 mb-4">Permissions</h3>

                  <!-- Permission Groups -->
                  <div class="space-y-4">
                    <div v-for="group in permissionGroups" :key="group.name">
                      <h4 class="font-semibold text-gray-700 mb-3">{{ group.name }}</h4>
                      <div class="space-y-2">
                        <label
                          v-for="permission in group.permissions"
                          :key="permission.key"
                          class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition"
                        >
                          <input
                            v-model="selectedRole.permissions"
                            :value="permission.key"
                            type="checkbox"
                            class="mt-1 w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                          />
                          <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ permission.name }}</p>
                            <p class="text-sm text-gray-500">{{ permission.description }}</p>
                          </div>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12">
              <div class="text-center">
                <i-lucide-shield class="w-20 h-20 text-gray-300 mx-auto mb-4" />
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Select a role to edit</h3>
                <p class="text-gray-500">Choose a role from the list or create a new one</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// UI State
const isSidebarOpen = ref(true)
const selectedRole = ref<Role | null>(null)
const draggedRole = ref<Role | null>(null)

// Role Interface
interface Role {
  id: number
  name: string
  color: string
  description: string
  hierarchy: number
  members_count: number
  permissions: string[]
  display_separately: boolean
  mentionable: boolean
  is_default: boolean
}

// Roles Data
const roles = ref<Role[]>([
  {
    id: 1,
    name: 'Super Admin',
    color: '#FF0000',
    description: 'Full access to all system features',
    hierarchy: 1,
    members_count: 2,
    permissions: ['*'], // All permissions
    display_separately: true,
    mentionable: false,
    is_default: true
  },
  {
    id: 2,
    name: 'Admin',
    color: '#FFA500',
    description: 'Manage documents and users',
    hierarchy: 2,
    members_count: 5,
    permissions: ['manage_users', 'manage_documents', 'view_analytics'],
    display_separately: true,
    mentionable: true,
    is_default: false
  },
  {
    id: 3,
    name: 'Reviewer',
    color: '#9B59B6',
    description: 'Review and approve documents',
    hierarchy: 3,
    members_count: 8,
    permissions: ['review_documents', 'approve_documents'],
    display_separately: false,
    mentionable: true,
    is_default: false
  },
  {
    id: 4,
    name: 'Contributor',
    color: '#3498DB',
    description: 'Upload and edit documents',
    hierarchy: 4,
    members_count: 15,
    permissions: ['upload_documents', 'edit_own_documents'],
    display_separately: false,
    mentionable: true,
    is_default: false
  },
  {
    id: 5,
    name: 'Guest',
    color: '#95A5A6',
    description: 'View public documents',
    hierarchy: 5,
    members_count: 50,
    permissions: ['view_documents'],
    display_separately: false,
    mentionable: false,
    is_default: true
  }
])

// Permission Groups (Discord-style)
const permissionGroups = [
  {
    name: 'General Permissions',
    permissions: [
      { key: 'administrator', name: 'Administrator', description: 'Full access to all features' },
      { key: 'view_analytics', name: 'View Analytics', description: 'Access to analytics and reports' }
    ]
  },
  {
    name: 'User Management',
    permissions: [
      { key: 'manage_users', name: 'Manage Users', description: 'Create, edit, and delete users' },
      { key: 'manage_roles', name: 'Manage Roles', description: 'Create and modify roles' },
      { key: 'view_users', name: 'View Users', description: 'View user list and details' }
    ]
  },
  {
    name: 'Document Management',
    permissions: [
      { key: 'upload_documents', name: 'Upload Documents', description: 'Upload new documents to the system' },
      { key: 'edit_own_documents', name: 'Edit Own Documents', description: 'Edit documents you uploaded' },
      { key: 'edit_all_documents', name: 'Edit All Documents', description: 'Edit any document' },
      { key: 'delete_documents', name: 'Delete Documents', description: 'Remove documents from the system' },
      { key: 'view_documents', name: 'View Documents', description: 'Access and read documents' }
    ]
  },
  {
    name: 'Review & Approval',
    permissions: [
      { key: 'review_documents', name: 'Review Documents', description: 'Review submitted documents' },
      { key: 'approve_documents', name: 'Approve Documents', description: 'Approve or reject documents' }
    ]
  },
  {
    name: 'Content Management',
    permissions: [
      { key: 'manage_categories', name: 'Manage Categories', description: 'Create and edit categories' },
      { key: 'manage_tags', name: 'Manage Tags', description: 'Create and edit tags' }
    ]
  }
]

// Computed
const sortedRoles = computed(() => {
  return [...roles.value].sort((a, b) => a.hierarchy - b.hierarchy)
})

// Methods
const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const selectRole = (role: Role) => {
  selectedRole.value = { ...role }
}

const createNewRole = () => {
  const newRole: Role = {
    id: Date.now(),
    name: 'New Role',
    color: '#' + Math.floor(Math.random()*16777215).toString(16),
    description: '',
    hierarchy: roles.value.length + 1,
    members_count: 0,
    permissions: [],
    display_separately: false,
    mentionable: true,
    is_default: false
  }
  roles.value.push(newRole)
  selectedRole.value = newRole
}

const saveRole = () => {
  if (!selectedRole.value) return

  const index = roles.value.findIndex(r => r.id === selectedRole.value!.id)
  if (index !== -1) {
    roles.value[index] = { ...selectedRole.value }
    alert('Role saved successfully!')
  }
}

const deleteRole = () => {
  if (!selectedRole.value) return

  if (confirm(`Are you sure you want to delete "${selectedRole.value.name}"?`)) {
    roles.value = roles.value.filter(r => r.id !== selectedRole.value!.id)
    selectedRole.value = null
    alert('Role deleted successfully!')
  }
}

// Drag and Drop
const handleDragStart = (event: DragEvent, role: Role) => {
  draggedRole.value = role
  event.dataTransfer!.effectAllowed = 'move'
}

const handleDrop = (event: DragEvent, targetRole: Role) => {
  event.preventDefault()

  if (!draggedRole.value || draggedRole.value.id === targetRole.id) return

  const draggedIndex = roles.value.findIndex(r => r.id === draggedRole.value!.id)
  const targetIndex = roles.value.findIndex(r => r.id === targetRole.id)

  // Guard against undefined values
  if (draggedIndex === -1 || targetIndex === -1) return

  // Swap hierarchy
  const tempHierarchy = roles.value[draggedIndex]!.hierarchy
  roles.value[draggedIndex]!.hierarchy = roles.value[targetIndex]!.hierarchy
  roles.value[targetIndex]!.hierarchy = tempHierarchy

  draggedRole.value = null
}

const logout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  router.push('/login')
}

onMounted(() => {
  // Load roles from API
  console.log('Role Management loaded')
})
</script>

<style scoped>
/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}
</style>
