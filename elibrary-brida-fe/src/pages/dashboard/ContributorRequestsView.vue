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

        <router-link to="/users" class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group">
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

        <router-link to="/roles" class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group">
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



        <router-link to="/profile-management" class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group">
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

        <router-link to="/contributor-requests" class="flex items-center gap-4 px-6 py-3 bg-blue-800 border-l-4 border-white group">
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

    <!-- Header -->
    <header
      :class="[
        'bg-white shadow-sm border-b border-gray-200 transition-all duration-300',
        isSidebarOpen ? 'ml-60' : 'ml-20'
      ]"
    >
      <div class="flex items-center justify-between px-8 py-4">
        <h1 class="text-2xl font-bold text-gray-900">Contributor Requests</h1>
        <div class="flex items-center gap-4">
          <div class="text-right">
            <p class="font-bold text-gray-800">{{ username || 'Admin' }}</p>
            <p class="text-sm text-gray-700 capitalize">{{ userRole || 'super_admin' }}</p>
          </div>
          <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
            {{ username ? username.charAt(0).toUpperCase() : 'A' }}
          </div>
        </div>
      </div>
    </header>

    <!-- Content -->
    <main
      :class="[
        'transition-all duration-300 p-8',
        isSidebarOpen ? 'ml-60' : 'ml-20'
      ]"
    >
      <div class="space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Pending Requests</p>
                <p class="text-3xl font-bold text-gray-900">{{ pendingCount }}</p>
              </div>
              <i-lucide-clock class="w-8 h-8 text-yellow-500" />
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Approved</p>
                <p class="text-3xl font-bold text-gray-900">{{ approvedCount }}</p>
              </div>
              <i-lucide-check-circle class="w-8 h-8 text-green-500" />
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Rejected</p>
                <p class="text-3xl font-bold text-gray-900">{{ rejectedCount }}</p>
              </div>
              <i-lucide-x-circle class="w-8 h-8 text-red-500" />
            </div>
          </div>
        </div>

        <!-- Requests Table -->
        <div class="bg-white rounded-lg shadow">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">User</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Message</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="request in requests" :key="request.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                      <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-sm">
                        {{ request.user.name.charAt(0).toUpperCase() }}
                      </div>
                      <span class="font-medium text-gray-900">{{ request.user.name }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ request.user.email }}</td>
                  <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" :title="request.message">
                    {{ request.message }}
                  </td>
                  <td class="px-6 py-4">
                    <span
                      :class="[
                        'inline-block px-3 py-1 rounded-full text-xs font-semibold',
                        request.status === 'pending' && 'bg-yellow-100 text-yellow-800',
                        request.status === 'approved' && 'bg-green-100 text-green-800',
                        request.status === 'rejected' && 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ request.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(request.created_at) }}</td>
                  <td class="px-6 py-4 text-center">
                    <div v-if="request.status === 'pending'" class="flex justify-center gap-2">
                      <button
                        @click="openApproveModal(request)"
                        class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition"
                      >
                        Approve
                      </button>
                      <button
                        @click="openRejectModal(request)"
                        class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition"
                      >
                        Reject
                      </button>
                    </div>
                    <span v-else class="text-xs text-gray-500">Completed</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="requests.length === 0" class="text-center py-8 text-gray-500">
            No contributor requests found
          </div>
        </div>
      </div>

      <!-- Approve Modal -->
      <div v-if="showApproveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Approve Request</h2>
          <p class="text-gray-600 mb-4">
            Approve contributor request from <strong>{{ selectedRequest?.user.name }}</strong>?
          </p>
          <p class="text-sm text-gray-500 mb-6 bg-gray-50 p-3 rounded">
            They will be able to upload documents immediately after approval.
          </p>

          <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Admin Notes (Optional)</label>
            <textarea
              v-model="approveNotes"
              class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500"
              rows="3"
              placeholder="Add any notes..."
            />
          </div>

          <div class="flex gap-3">
            <button
              @click="confirmApprove"
              :disabled="isProcessing"
              class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-400 transition"
            >
              {{ isProcessing ? 'Processing...' : 'Approve' }}
            </button>
            <button
              @click="showApproveModal = false"
              :disabled="isProcessing"
              class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>

      <!-- Reject Modal -->
      <div v-if="showRejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Reject Request</h2>
          <p class="text-gray-600 mb-4">
            Reject contributor request from <strong>{{ selectedRequest?.user.name }}</strong>?
          </p>

          <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Reason for Rejection <span class="text-red-500">*</span>
            </label>
            <textarea
              v-model="rejectNotes"
              class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500"
              rows="4"
              placeholder="Please provide a reason for rejection..."
              required
            />
          </div>

          <div class="flex gap-3">
            <button
              @click="confirmReject"
              :disabled="isProcessing || !rejectNotes.trim()"
              class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-gray-400 transition"
            >
              {{ isProcessing ? 'Processing...' : 'Reject' }}
            </button>
            <button
              @click="showRejectModal = false"
              :disabled="isProcessing"
              class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()

interface ContributorRequest {
  id: number
  user: {
    id: number
    name: string
    email: string
  }
  status: 'pending' | 'approved' | 'rejected'
  message: string
  admin_notes: string | null
  reviewed_by: {
    id: number
    name: string
  } | null
  reviewed_at: string | null
  created_at: string
}

// States
const isSidebarOpen = ref(true)
const username = ref('')
const userRole = ref('')
const requests = ref<ContributorRequest[]>([])
const selectedRequest = ref<ContributorRequest | null>(null)
const showApproveModal = ref(false)
const showRejectModal = ref(false)
const approveNotes = ref('')
const rejectNotes = ref('')
const isProcessing = ref(false)

// Computed
const pendingCount = computed(() => requests.value.filter(r => r.status === 'pending').length)
const approvedCount = computed(() => requests.value.filter(r => r.status === 'approved').length)
const rejectedCount = computed(() => requests.value.filter(r => r.status === 'rejected').length)

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

const loadRequests = async () => {
  try {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const response: any = await api.contributorRequests.getAll()
    if (response.success) {
      requests.value = response.data
    }
  } catch (error) {
    console.error('Failed to load requests:', error)
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const openApproveModal = (request: ContributorRequest) => {
  selectedRequest.value = request
  approveNotes.value = ''
  showApproveModal.value = true
}

const openRejectModal = (request: ContributorRequest) => {
  selectedRequest.value = request
  rejectNotes.value = ''
  showRejectModal.value = true
}

const confirmApprove = async () => {
  if (!selectedRequest.value) return

  isProcessing.value = true
  try {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const response: any = await api.contributorRequests.approve(selectedRequest.value.id, approveNotes.value)
    if (response.success) {
      alert('Request approved successfully!')
      showApproveModal.value = false
      await loadRequests()
    }
  } catch (error) {
    console.error('Failed to approve request:', error)
    alert('Gagal menyetujui permintaan. Silakan coba lagi.')
  } finally {
    isProcessing.value = false
  }
}

const confirmReject = async () => {
  if (!selectedRequest.value || !rejectNotes.value.trim()) return

  isProcessing.value = true
  try {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const response: any = await api.contributorRequests.reject(selectedRequest.value.id, rejectNotes.value)
    if (response.success) {
      alert('Request rejected successfully!')
      showRejectModal.value = false
      await loadRequests()
    }
  } catch (error) {
    console.error('Failed to reject request:', error)
    alert('Gagal menolak permintaan. Silakan coba lagi.')
  } finally {
    isProcessing.value = false
  }
}

// Lifecycle
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

  loadRequests()
})
</script>
