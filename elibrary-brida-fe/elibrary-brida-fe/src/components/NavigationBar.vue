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
            placeholder="hinted search text"
            class="bg-neutral-200 rounded-full pl-4 pr-12 py-2 text-sm placeholder-neutral-400 text-neutral-950 hover:bg-neutral-300 transition w-60 focus:outline-none"
          />
        </div>
      </div>

      <!-- Navigation Links -->
      <div class="flex items-center space-x-20">
        <ul
          class="flex items-center space-x-6 font-heading font-bold text-neutral-950"
        >
          <li><a href="#" class="hover-text">Home</a></li>
          <li><a href="#catalog" class="hover-text">Katalog</a></li>
          <li><a href="#" class="hover-text">FAQ</a></li>
          <li><a href="#" class="hover-text">Unggah Mandiri</a></li>
        </ul>
        <ul
          class="flex items-center space-x-6 font-heading font-bold text-neutral-950"
        >
          <li><a href="#" class="hover-text">Login</a></li>
          <li><a href="#" class="hover-text">Register</a></li>
        </ul>
      </div>
    </div>
  </nav>
</template>
