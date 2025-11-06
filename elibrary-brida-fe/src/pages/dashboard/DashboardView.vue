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
        <a href="#" class="flex items-center gap-4 px-6 py-3 bg-blue-800 border-l-4 border-white group">
          <i-lucide-layout-dashboard class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Dashboard
          </span>
        </a>
        <a href="#" class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group">
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
        <a href="#" class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group">
          <i-lucide-shield-check class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Role
          </span>
        </a>
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

      <!-- Dashboard Content -->
      <main class="p-8">
        <!-- Summary Section -->
        <section class="mb-12">
          <h1 class="text-4xl font-bold text-gray-900 mb-8">Summary</h1>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl">
            <!-- Queue Review Card -->
            <div class="bg-gradient-to-br from-orange-400 to-orange-500 rounded-xl p-8 shadow-lg text-white">
            <h2 class="text-5xl font-bold mb-2">{{ queueCount }}</h2>
              <p class="text-xl">Queue Review</p>
            </div>

            <!-- Top Contributor Card -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-8 shadow-lg text-white">
              <h2 class="text-4xl font-bold mb-2">{{ topContributor }}</h2>
              <p class="text-xl">Top Contributor</p>
            </div>

            <!-- Top Viewed Card -->
            <div class="bg-gradient-to-br from-orange-400 to-orange-500 rounded-xl p-8 shadow-lg text-white">
              <h2 class="text-4xl font-bold mb-2">{{ topArticle }}</h2>
              <p class="text-xl">Top Viewed Today</p>
            </div>

            <!-- Total Papers Card -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-8 shadow-lg text-white">
              <h2 class="text-5xl font-bold mb-2">{{ totalPapers }}</h2>
              <p class="text-xl">Total of Papers</p>
            </div>
          </div>
        </section>

        <!-- Queue Review Section -->
        <section class="mb-12">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-3xl font-bold text-gray-900">Queue Review</h2>
              <p class="text-gray-600 mt-1">*Klik judul dokumen untuk melihat detail dokumennya</p>
            </div>
            <div class="flex items-center gap-4">
              <span class="text-gray-700">Selected</span>
              <span class="font-bold text-gray-900">{{ selectedQueue.length }}</span>
              <button
                @click="approveSelected"
                :disabled="selectedQueue.length === 0"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                Approve
              </button>
              <button
                @click="rejectSelected"
                :disabled="selectedQueue.length === 0"
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                Reject
              </button>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Table Header -->
            <div class="flex items-center gap-4 p-4 border-b">
              <button class="p-2 hover:bg-gray-100 rounded-lg">
                <i-lucide-filter class="w-5 h-5 text-gray-600" />
              </button>
              <div class="relative flex-1 max-w-md">
                <i-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input
                  v-model="queueSearchQuery"
                  type="text"
                  placeholder="Search..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 border-b">
                  <tr>
                    <th class="px-6 py-3 text-left">
                      <input
                        type="checkbox"
                        @change="toggleSelectAllQueue"
                        :checked="selectedQueue.length === queueReviews.length && queueReviews.length > 0"
                        class="rounded border-gray-300"
                      />
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Last Update</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">More</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="(item, index) in filteredQueueReviews" :key="item.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                      <input
                        type="checkbox"
                        :value="item.id"
                        v-model="selectedQueue"
                        class="rounded border-gray-300"
                      />
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ index + 1 }}</td>
                    <td class="px-6 py-4">
                      <div class="text-sm font-semibold text-gray-900">{{ item.name }}</div>
                      <div class="text-xs text-gray-500">{{ item.email }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <a href="#" class="text-sm text-blue-600 hover:text-blue-800 hover:underline line-clamp-2">
                        {{ item.title }}
                      </a>
                    </td>
                    <td class="px-6 py-4">
                      <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        {{ item.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                      <div>{{ item.lastUpdate.date }}</div>
                      <div class="text-xs text-gray-500">{{ item.lastUpdate.time }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex gap-2">
                        <button
                          @click="approveItem(item.id)"
                          class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg text-sm font-semibold transition"
                        >
                          Approve
                        </button>
                        <button
                          @click="rejectItem(item.id)"
                          class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded-lg text-sm font-semibold transition"
                        >
                          Reject
                        </button>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <button class="p-2 hover:bg-gray-200 rounded-lg transition">
                        <i-lucide-mail class="w-5 h-5 text-blue-600" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between px-6 py-4 border-t bg-gray-50">
              <div class="text-sm text-gray-600">
                1-{{ Math.min(queueRowsPerPage, queueReviews.length) }} of {{ queueReviews.length }}
              </div>
              <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                  <span class="text-sm text-gray-600">Rows per page:</span>
                  <select
                    v-model="queueRowsPerPage"
                    class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="20">20</option>
                  </select>
                </div>
                <div class="flex items-center gap-2">
                  <button class="p-1 hover:bg-gray-200 rounded disabled:opacity-50">
                    <i-lucide-chevron-left class="w-5 h-5" />
                  </button>
                  <span class="text-sm text-gray-600">1/{{ Math.ceil(queueReviews.length / queueRowsPerPage) }}</span>
                  <button class="p-1 hover:bg-gray-200 rounded disabled:opacity-50">
                    <i-lucide-chevron-right class="w-5 h-5" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- History Section -->
        <section>
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-3xl font-bold text-gray-900">History</h2>
              <p class="text-gray-600 mt-1">*Klik judul dokumen untuk melihat detail dokumennya</p>
            </div>
            <div class="flex items-center gap-4">
              <span class="text-gray-700">Selected</span>
              <span class="font-bold text-gray-900">{{ selectedHistory.length }}</span>
              <button
                @click="deleteSelected"
                :disabled="selectedHistory.length === 0"
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                Delete
              </button>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Table Header -->
            <div class="flex items-center gap-4 p-4 border-b">
              <button class="p-2 hover:bg-gray-100 rounded-lg">
                <i-lucide-filter class="w-5 h-5 text-gray-600" />
              </button>
              <div class="relative flex-1 max-w-md">
                <i-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input
                  v-model="historySearchQuery"
                  type="text"
                  placeholder="Search..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 border-b">
                  <tr>
                    <th class="px-6 py-3 text-left">
                      <input
                        type="checkbox"
                        @change="toggleSelectAllHistory"
                        :checked="selectedHistory.length === histories.length && histories.length > 0"
                        class="rounded border-gray-300"
                      />
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Last Update</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">More</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="(item, index) in filteredHistories" :key="item.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                      <input
                        type="checkbox"
                        :value="item.id"
                        v-model="selectedHistory"
                        class="rounded border-gray-300"
                      />
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ index + 1 }}</td>
                    <td class="px-6 py-4">
                      <div class="text-sm font-semibold text-gray-900">{{ item.name }}</div>
                      <div class="text-xs text-gray-500">{{ item.email }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <a href="#" class="text-sm text-blue-600 hover:text-blue-800 hover:underline line-clamp-2">
                        {{ item.title }}
                      </a>
                    </td>
                    <td class="px-6 py-4">
                      <span
                        :class="[
                          'inline-flex px-3 py-1 text-xs font-semibold rounded-full',
                          item.status === 'Accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                        ]"
                      >
                        {{ item.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                      <div>{{ item.lastUpdate.date }}</div>
                      <div class="text-xs text-gray-500">{{ item.lastUpdate.time }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <button
                        @click="deleteItem(item.id)"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded-lg text-sm font-semibold transition"
                      >
                        Delete
                      </button>
                    </td>
                    <td class="px-6 py-4">
                      <button class="p-2 hover:bg-gray-200 rounded-lg transition">
                        <i-lucide-mail class="w-5 h-5 text-blue-600" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between px-6 py-4 border-t bg-gray-50">
              <div class="text-sm text-gray-600">
                1-{{ Math.min(historyRowsPerPage, histories.length) }} of {{ histories.length }}
              </div>
              <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                  <span class="text-sm text-gray-600">Rows per page:</span>
                  <select
                    v-model="historyRowsPerPage"
                    class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="20">20</option>
                  </select>
                </div>
                <div class="flex items-center gap-2">
                  <button class="p-1 hover:bg-gray-200 rounded disabled:opacity-50">
                    <i-lucide-chevron-left class="w-5 h-5" />
                  </button>
                  <span class="text-sm text-gray-600">1/{{ Math.ceil(histories.length / historyRowsPerPage) }}</span>
                  <button class="p-1 hover:bg-gray-200 rounded disabled:opacity-50">
                    <i-lucide-chevron-right class="w-5 h-5" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const username = ref('')
const isSidebarOpen = ref(true)

// Summary Stats
const queueCount = ref(5)
const topContributor = ref('User x')
const topArticle = ref('Artikel x')
const totalPapers = ref(100)

// Queue Review Data
const selectedQueue = ref<number[]>([])
const queueSearchQuery = ref('')
const queueRowsPerPage = ref(5)

interface QueueItem {
  id: number
  name: string
  email: string
  title: string
  status: string
  lastUpdate: {
    date: string
    time: string
  }
}

const queueReviews = ref<QueueItem[]>([
  {
    id: 1,
    name: 'Alexandra Harry',
    email: 'alexandraharry37@gmail.com',
    title: 'Role of AI in Education',
    status: 'Waiting',
    lastUpdate: { date: '9/18/2025', time: '10:45 WIB' }
  },
  {
    id: 2,
    name: 'Ahmad Rosser',
    email: 'ahm@mail.com',
    title: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla...',
    status: 'Waiting',
    lastUpdate: { date: '9/18/2025', time: '10:45 WIB' }
  },
  {
    id: 3,
    name: 'Zain Calzoni',
    email: 'zain@mail.com',
    title: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla...',
    status: 'Waiting',
    lastUpdate: { date: '9/18/2025', time: '10:45 WIB' }
  },
  {
    id: 4,
    name: 'Leo Stanton',
    email: 'leon@mail.com',
    title: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla...',
    status: 'Waiting',
    lastUpdate: { date: '9/18/2025', time: '10:45 WIB' }
  },
  {
    id: 5,
    name: 'Kaiya Vetrovs',
    email: 'kaiya@mail.com',
    title: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla...',
    status: 'Waiting',
    lastUpdate: { date: '9/18/2025', time: '10:45 WIB' }
  }
])

const filteredQueueReviews = computed(() => {
  if (!queueSearchQuery.value) return queueReviews.value
  const query = queueSearchQuery.value.toLowerCase()
  return queueReviews.value.filter(item =>
    item.name.toLowerCase().includes(query) ||
    item.email.toLowerCase().includes(query) ||
    item.title.toLowerCase().includes(query)
  )
})

// History Data
const selectedHistory = ref<number[]>([])
const historySearchQuery = ref('')
const historyRowsPerPage = ref(5)

interface HistoryItem {
  id: number
  name: string
  email: string
  title: string
  status: 'Accepted' | 'Rejected'
  lastUpdate: {
    date: string
    time: string
  }
}

const histories = ref<HistoryItem[]>([
  {
    id: 1,
    name: 'Alexandra Harry',
    email: 'alexandraharry37@gmail.com',
    title: 'Role of AI in Education',
    status: 'Accepted',
    lastUpdate: { date: '9/18/2025', time: '10:45 WIB' }
  },
  {
    id: 2,
    name: 'Ahmad Rosser',
    email: 'ahm@mail.com',
    title: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla...',
    status: 'Rejected',
    lastUpdate: { date: '9/18/2025', time: '10:45 WIB' }
  },
  {
    id: 3,
    name: 'Zain Calzoni',
    email: 'zain@mail.com',
    title: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla...',
    status: 'Accepted',
    lastUpdate: { date: '9/18/2025', time: '10:45 WIB' }
  },
  {
    id: 4,
    name: 'Leo Stanton',
    email: 'leon@mail.com',
    title: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla...',
    status: 'Rejected',
    lastUpdate: { date: '9/18/2025', time: '10:45 WIB' }
  },
  {
    id: 5,
    name: 'Kaiya Vetrovs',
    email: 'kaiya@mail.com',
    title: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla...',
    status: 'Accepted',
    lastUpdate: { date: '9/18/2025', time: '10:45 WIB' }
  }
])

const filteredHistories = computed(() => {
  if (!historySearchQuery.value) return histories.value
  const query = historySearchQuery.value.toLowerCase()
  return histories.value.filter(item =>
    item.name.toLowerCase().includes(query) ||
    item.email.toLowerCase().includes(query) ||
    item.title.toLowerCase().includes(query)
  )
})

// Functions
onMounted(() => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    username.value = JSON.parse(storedUser).username
  } else {
    router.push('/login')
  }
})

const logout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  router.push('/login')
}

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

// Queue Review Actions
const toggleSelectAllQueue = (e: Event) => {
  const checked = (e.target as HTMLInputElement).checked
  selectedQueue.value = checked ? queueReviews.value.map(item => item.id) : []
}

const approveItem = (id: number) => {
  const item = queueReviews.value.find(q => q.id === id)
  if (item) {
    histories.value.unshift({
      ...item,
      status: 'Accepted'
    })
    queueReviews.value = queueReviews.value.filter(q => q.id !== id)
    queueCount.value--
  }
}

const rejectItem = (id: number) => {
  const item = queueReviews.value.find(q => q.id === id)
  if (item) {
    histories.value.unshift({
      ...item,
      status: 'Rejected'
    })
    queueReviews.value = queueReviews.value.filter(q => q.id !== id)
    queueCount.value--
  }
}

const approveSelected = () => {
  selectedQueue.value.forEach(id => approveItem(id))
  selectedQueue.value = []
}

const rejectSelected = () => {
  selectedQueue.value.forEach(id => rejectItem(id))
  selectedQueue.value = []
}

// History Actions
const toggleSelectAllHistory = (e: Event) => {
  const checked = (e.target as HTMLInputElement).checked
  selectedHistory.value = checked ? histories.value.map(item => item.id) : []
}

const deleteItem = (id: number) => {
  histories.value = histories.value.filter(h => h.id !== id)
}

const deleteSelected = () => {
  selectedHistory.value.forEach(id => deleteItem(id))
  selectedHistory.value = []
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
