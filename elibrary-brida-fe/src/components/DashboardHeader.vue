<template>
  <header
    :class="[
      'bg-white shadow-sm border-b border-gray-200 transition-all duration-300',
      isSidebarOpen ? openMarginClass : closedMarginClass,
      headerClass,
    ]"
  >
    <div class="flex items-center justify-between px-8 py-4">
      <h1 class="text-2xl font-bold text-gray-900">{{ title }}</h1>
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
</template>

<script setup lang="ts">
withDefaults(
  defineProps<{
    title: string
    isSidebarOpen: boolean
    username?: string
    userRole?: string
    openMarginClass?: string
    closedMarginClass?: string
    headerClass?: string
  }>(),
  {
    openMarginClass: 'ml-60',
    closedMarginClass: 'ml-20',
    headerClass: '',
  },
)
</script>
