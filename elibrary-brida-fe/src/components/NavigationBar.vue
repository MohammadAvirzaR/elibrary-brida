<template>
  <nav class="bg-white/95 backdrop-blur-sm shadow-sm fixed w-full top-0 left-0 z-50 border-b border-neutral-100">
    <div class="container mx-auto flex justify-between items-center py-3.5 px-6">
      <!-- Logo + Search -->
      <div class="flex items-center space-x-10">
        <img src="@/assets/brin-logo-trans.png" alt="Logo BRIN" class="h-16" />

        <!-- Search bar -->
        <div class="relative">
          <!-- Ikon search -->
          <i-lucide-search
            class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400"
          />
          <!-- Input -->
          <input
            type="text"
            v-model="localSearch"
            @input="handleSearch"
            @keyup.enter="searchSubmit"
            placeholder="Cari buku digital..."
            class="bg-neutral-50 rounded-full pl-4 pr-11 py-2 text-sm placeholder-neutral-400 text-neutral-950 border border-neutral-200 hover:bg-neutral-100 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all w-64 focus:outline-none"
          />
        </div>
      </div>

      <!-- Navigation Links -->
      <div class="flex items-center space-x-16">
        <ul
          class="flex items-center space-x-8 font-heading font-semibold text-neutral-700 text-sm"
        >
          <li><router-link to="/" class="hover:text-neutral-900 transition-colors">Home</router-link></li>
          <li><router-link to="/catalog" class="hover:text-neutral-900 transition-colors">Katalog</router-link></li>
          <li><router-link to="/faq" class="hover:text-neutral-900 transition-colors">FAQ</router-link></li>
          <li>
            <button @click="handleUploadClick" class="hover:text-neutral-900 transition-colors">
              Unggah Mandiri
            </button>
          </li>
        </ul>

        <!-- Show Login/Register if NOT logged in -->
        <ul
          v-if="!isAuthenticated"
          class="flex items-center space-x-4 font-heading font-semibold text-sm"
        >
          <li>
            <router-link
              to="/login"
              class="text-neutral-700 hover:text-neutral-900 transition-colors px-4 py-2"
            >
              Login
            </router-link>
          </li>
          <li>
            <router-link
              to="/register"
              class="bg-neutral-900 text-white hover:bg-neutral-800 transition-colors px-5 py-2 rounded-full"
            >
              Register
            </router-link>
          </li>
        </ul>

        <!-- Show Profile if logged in -->
        <div v-else class="flex items-center gap-3">
          <div class="text-right">
            <p class="text-sm font-semibold text-neutral-900">{{ userName }}</p>
            <p class="text-xs text-neutral-500">{{ userRole }}</p>
          </div>
          <div class="relative profile-dropdown">
            <button
              @click="toggleProfileMenu"
              class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white font-semibold flex items-center justify-center hover:from-blue-600 hover:to-blue-700 transition-all shadow-md"
            >
              {{ userInitials }}
            </button>

            <!-- Dropdown Menu -->
            <transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <div
                v-if="showProfileMenu"
                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-neutral-200 py-2 z-50"
              >
                <router-link
                  :to="dashboardLink"
                  class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i-lucide-layout-dashboard class="w-4 h-4 inline mr-2" />
                  Dashboard
                </router-link>
                <router-link
                  to="/profile"
                  class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i-lucide-user class="w-4 h-4 inline mr-2" />
                  Profile
                </router-link>
                <hr class="my-2 border-neutral-200" />
                <button
                  @click="handleLogout"
                  class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                >
                  <i-lucide-log-out class="w-4 h-4 inline mr-2" />
                  Logout
                </button>
              </div>
            </transition>
          </div>

          <!-- Logout Button (Always Visible) -->
          <button
            @click="handleLogout"
            class="ml-2 px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 border-2 border-red-600 hover:bg-red-50 rounded-full transition-all inline-flex items-center gap-2"
          >
            <i-lucide-log-out class="w-4 h-4" />
            Logout
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script lang="ts" setup>
import { ref, watch, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useSearch } from '@/composables/useSearch'
import api from '@/services/api'

const router = useRouter()
const route = useRoute()
const { searchQuery, setSearchQuery } = useSearch()
const localSearch = ref(searchQuery.value)

// Authentication state
const isAuthenticated = ref(false)
const userName = ref('')
const userRole = ref('')
const userInitials = ref('')
const showProfileMenu = ref(false)

watch(() => route.path, () => {
  checkAuth()
})

onMounted(() => {
  checkAuth()

  document.addEventListener('click', handleClickOutside)
  window.addEventListener('storage', handleStorageChange)
  window.addEventListener('auth-changed', checkAuth as EventListener)

  const refreshInterval = setInterval(async () => {
    if (isAuthenticated.value) {
      await refreshUserData()
    }
  }, 30000)

  ;(window as Window & { __authRefreshInterval?: ReturnType<typeof setInterval> }).__authRefreshInterval = refreshInterval
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  window.removeEventListener('storage', handleStorageChange)
  window.removeEventListener('auth-changed', checkAuth as EventListener)

  const interval = (window as Window & { __authRefreshInterval?: ReturnType<typeof setInterval> }).__authRefreshInterval
  if (interval) {
    clearInterval(interval)
  }
})

const handleStorageChange = (event: StorageEvent) => {
  if (event.key === 'auth_token' || event.key === 'user') {
    checkAuth()
  }
}

const checkAuth = () => {
  const token = localStorage.getItem('auth_token')
  const userStr = localStorage.getItem('user')

  if (token && userStr) {
    try {
      const user = JSON.parse(userStr)
      isAuthenticated.value = true
      userName.value = user.name || user.username || 'User'
      userRole.value = formatRole(user.role || 'Guest')

      const names = userName.value.split(' ')
      if (names.length >= 2 && names[0]?.[0] && names[1]?.[0]) {
        userInitials.value = (names[0][0] + names[1][0]).toUpperCase()
      } else if (names[0] && names[0].length >= 2) {
        userInitials.value = names[0].substring(0, 2).toUpperCase()
      } else {
        userInitials.value = userName.value.substring(0, 2).toUpperCase()
      }
    } catch (error) {
      console.error('Error parsing user data:', error)
      isAuthenticated.value = false
    }
  } else {
    isAuthenticated.value = false
  }
}

const formatRole = (role: string) => {
  const roleMap: Record<string, string> = {
    'super_admin': 'Super Admin',
    'admin': 'Admin',
    'kontributor': 'Kontributor',
    'reviewer': 'Reviewer',
    'guest': 'Guest'
  }
  return roleMap[role.toLowerCase()] || role
}

const dashboardLink = computed(() => {
  const userStr = localStorage.getItem('user')
  if (!userStr) return '/my-dashboard'

  try {
    const user = JSON.parse(userStr)
    const role = user.role?.toLowerCase()

    if (role === 'super_admin' || role === 'admin') {
      return '/dashboard'
    }

    return '/my-dashboard'
  } catch {
    return '/my-dashboard'
  }
})

// Toggle profile menu
const toggleProfileMenu = (event: Event) => {
  event.stopPropagation()
  showProfileMenu.value = !showProfileMenu.value
}

// Close dropdown when clicking outside
const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement
  const profileDropdown = target.closest('.profile-dropdown')

  if (showProfileMenu.value && !profileDropdown) {
    showProfileMenu.value = false
  }
}

const handleLogout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  isAuthenticated.value = false
  showProfileMenu.value = false
  router.push('/')
}

interface UserApiResponse {
  user: {
    id: number
    name?: string
    full_name?: string
    username?: string
    email: string
    institution?: string
    unit_name?: string
    role?: { name?: string } | string
  }
}

const refreshUserData = async () => {
  const token = localStorage.getItem('auth_token')
  if (!token) return

  try {
    const data = await api.auth.me() as UserApiResponse

    if (data.user) {
      const oldRole = localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')!).role : null
      const newRole = typeof data.user.role === 'object' ? data.user.role?.name || 'guest' : data.user.role || 'guest'

      localStorage.setItem('user', JSON.stringify({
        id: data.user.id,
        name: data.user.name || data.user.full_name,
        username: data.user.username || data.user.name || data.user.full_name,
        email: data.user.email,
        institution: data.user.institution || data.user.unit_name,
        role: newRole
      }))

      if (oldRole && oldRole !== newRole) {
        checkAuth()
        // Optional: Show notification or redirect
        console.log(`Role changed from ${oldRole} to ${newRole}`)
      }
    }
  } catch (error) {
    console.error('Failed to refresh user data:', error)
  }
}

// Sinkronisasi dengan global search state
watch(searchQuery, (newValue) => {
  localSearch.value = newValue
})

const handleSearch = () => {
  setSearchQuery(localSearch.value)
}

const searchSubmit = () => {
  if (localSearch.value.trim()) {
    setSearchQuery(localSearch.value)
    router.push({
      name: 'search',
      query: {
        q: localSearch.value,
        page: 1
      }
    })
  }
}

const handleUploadClick = () => {
  if (!isAuthenticated.value) {
    // Guest: redirect to unauthorized kontributor page
    router.push('/unauthorized-kontributor')
  } else {
    // Logged in: redirect to upload document
    router.push('/upload-document')
  }
}
</script>
