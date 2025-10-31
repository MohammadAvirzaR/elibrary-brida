<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation Bar -->
    <NavigationBar />

    <!-- Main Content -->
    <main class="pt-20">
      <slot />
    </main>

    <!-- Footer -->
    <FooterSection />
  </div>
</template>

<script lang="ts" setup>
import NavigationBar from '@/components/NavigationBar.vue'
import FooterSection from '@/components/FooterSection.vue'
</script>

<style scoped>
/* Additional styling if needed */
</style>
