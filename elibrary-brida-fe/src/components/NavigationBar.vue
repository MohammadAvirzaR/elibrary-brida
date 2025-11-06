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
          <li><router-link to="/unggah-mandiri" class="hover:text-neutral-900 transition-colors">Unggah Mandiri</router-link></li>
        </ul>
        <ul
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
      </div>
    </div>
  </nav>
</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useSearch } from '@/composables/useSearch'

const router = useRouter()
const { searchQuery, setSearchQuery } = useSearch()
const localSearch = ref(searchQuery.value)

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
</script>
