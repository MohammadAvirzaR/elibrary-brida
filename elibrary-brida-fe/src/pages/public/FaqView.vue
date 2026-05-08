<template>
  <PublicLayout>
    <section class="container mx-auto px-4 md:px-6 pt-10 md:pt-14 pb-14 md:pb-20 min-h-screen">
      <h1 class="text-3xl md:text-4xl font-heading font-bold text-neutral-950 text-center mb-10 md:mb-12">
        Pertanyaan umum
      </h1>

      <div class="mx-auto max-w-5xl border-t border-b border-neutral-200">
        <div v-for="item in faqItems" :key="item.id" class="border-b border-neutral-200 last:border-b-0">
          <button
            type="button"
            class="w-full flex items-center justify-between gap-4 px-4 md:px-5 py-4 text-left"
            @click="toggleItem(item.id)"
          >
            <span class="text-sm md:text-base font-semibold text-neutral-950">
              {{ item.question }}
            </span>
            <i-lucide-chevron-down
              class="w-5 h-5 text-neutral-900 transition-transform duration-200"
              :class="openedItems.includes(item.id) ? 'rotate-0' : '-rotate-90'"
            />
          </button>

          <div
            v-if="openedItems.includes(item.id)"
            class="px-4 md:px-5 pb-4 text-xs md:text-sm leading-relaxed text-neutral-600"
          >
            {{ item.answer }}
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import PublicLayout from '@/layout/PublicLayout.vue'

type FaqItem = {
  id: number
  question: string
  answer: string
}

const faqItems: FaqItem[] = [
  {
    id: 1,
    question: 'Bagaimana cara mencari dokumen berdasarkan judul, penulis, atau kata kunci?',
    answer:
      'Anda dapat menggunakan fitur pencarian di halaman katalog atau homepage. Ketikkan judul, nama penulis, atau kata kunci relevan di kolom pencarian. Sistem akan menampilkan hasil yang sesuai dengan kriteria pencarian Anda. Anda juga dapat memfilter hasil berdasarkan jenis dokumen, tahun publikasi, atau subjek untuk mempersempit pencarian.'
  },
  {
    id: 2,
    question: 'Apakah saya harus login untuk melihat detail dokumen?',
    answer:
      'Tidak, Anda tidak perlu login untuk melihat informasi dasar dan preview terbatas dari dokumen yang telah disetujui. Namun, jika Anda ingin mengunduh dokumen secara lengkap atau melakukan aktivitas lainnya seperti mengajukan permintaan download, Anda perlu membuat akun dan login terlebih dahulu. Login juga diperlukan jika Anda ingin menjadi kontributor (pengunggah dokumen).'
  },
  {
    id: 3,
    question: 'Mengapa saya tidak bisa mengunduh dokumen secara langsung?',
    answer:
      'E-Library BRIDA menerapkan sistem permintaan download untuk melindungi hak kekayaan intelektual dokumen. Untuk mengunduh dokumen, Anda perlu mengajukan permintaan terlebih dahulu dengan menyertakan informasi Anda (nama, email, institusi) dan tujuan penggunaan. Pemilik dokumen akan meninjau permintaan Anda dan memberikan akses jika disetujui. Fitur ini memastikan dokumen digunakan sesuai dengan lisensi dan ketentuan yang berlaku.'
  },
  {
    id: 4,
    question: 'Bagaimana cara mengunggah dokumen sebagai kontributor?',
    answer:
      'Pertama, daftarkan akun Anda dan ajukan permohonan untuk menjadi kontributor. Setelah permohonan disetujui oleh admin, Anda dapat masuk ke dashboard kontributor. Di sana, klik tombol "Upload Dokumen" dan ikuti panduan wizard untuk mengisi metadata (judul, penulis, tahun publikasi, subjek, dll), memilih lisensi yang sesuai, serta mengunggah file PDF dokumen Anda. Dokumen akan diproses dan ditinjau oleh reviewer sebelum dipublikasikan di katalog.'
  }
]

const openedItems = ref<number[]>(faqItems.map((item) => item.id))

const toggleItem = (id: number) => {
  if (openedItems.value.includes(id)) {
    openedItems.value = openedItems.value.filter((itemId) => itemId !== id)
    return
  }

  openedItems.value = [...openedItems.value, id]
}
</script>
