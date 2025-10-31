<template>
  <nav class="bg-neutral-100 shadow-sm fixed w-full top-0 left-0 z-50">
    <div class="container mx-auto flex justify-between items-center py-3 px-6">
      <!-- Logo + Search -->
      <div class="flex items-center space-x-10">
        <img src="@/assets/brin-logo-trans.png" alt="Logo BRIN" class="h-20" />

        <!-- Search bar -->
        <div class="relative">
          <!-- Ikon search -->
          <i-lucide-search
            class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-neutral-400"
          />
          <!-- Input -->
          <input
            type="text"
            v-model="localSearch"
            @input="handleSearch"
            @keyup.enter="navigateToSearch"
            placeholder="Cari buku digital..."
            class="bg-neutral-200 rounded-full pl-4 pr-12 py-2 text-sm placeholder-neutral-400 text-neutral-950 hover:bg-neutral-300 transition w-60 focus:outline-none"
          />
        </div>
      </div>

      <!-- Navigation Links -->
      <div class="flex items-center space-x-20">
        <ul
          class="flex items-center space-x-6 font-heading font-bold text-neutral-950"
        >
          <li><router-link to="/" class="hover-text">Home</router-link></li>
          <li><router-link to="/catalog" class="hover-text">Katalog</router-link></li>
          <li><router-link to="/faq" class="hover-text">FAQ</router-link></li>
          <li><router-link to="/unggah-mandiri" class="hover-text">Unggah Mandiri</router-link></li>
        </ul>
        <ul
          class="flex items-center space-x-6 font-heading font-bold text-neutral-950"
        >
          <li><router-link to="/login" class="hover-text">Login</router-link></li>
          <li><router-link to="/register" class="hover-text">Register</router-link></li>
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

const navigateToSearch = () => {
  if (localSearch.value.trim()) {
    setSearchQuery(localSearch.value)
    router.push({
      name: 'search',
      query: { q: localSearch.value }
    })
  }
}
</script>
