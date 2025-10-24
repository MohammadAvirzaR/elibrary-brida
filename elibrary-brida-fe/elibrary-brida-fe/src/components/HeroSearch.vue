<template>
  <section
    class="flex flex-col items-center gap-[30px] w-full max-w-[720px] mx-auto text-center pt-64 min-h-screen"
  >
    <h2 class="text-[32px] font-heading font-bold text-neutral-950 mb-6">
      E-Library BRIDA Sulawesi Tenggara
    </h2>
    <div class="relative w-full">
      <i-lucide-search
        class="absolute right-6 top-1/2 -translate-y-1/2 w-6 h-6 text-neutral-400"
      />
      <input
        type="text"
        placeholder="Cari buku digital..."
        class="bg-neutral-200 placeholder-neutral-400 text-neutral-950 rounded-full px-6 py-3 w-full focus:outline-none hover:bg-neutral-300 transition"
      />
    </div>

    <button
      class="bg-neutral-800 text-white font-body px-8 py-3 rounded-md hover:bg-neutral-950 transition"
    >
      Advanced Search
    </button>
  </section>
</template>
