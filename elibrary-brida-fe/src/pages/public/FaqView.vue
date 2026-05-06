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
    question: 'Bagaimana cara lorem ipsum dolor sit amet',
    answer:
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc fringilla turpis tellus, sed faucibus eros sollicitudin quis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin mollis ipsum sit amet accumsan venenatis. Nam vestibulum molestie ornare. Vivamus vel tempor libero.'
  },
  {
    id: 2,
    question: 'Apakah saya lorem ipsum dolor sit amet',
    answer:
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc fringilla turpis tellus, sed faucibus eros sollicitudin quis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin mollis ipsum sit amet accumsan venenatis. Nam vestibulum molestie ornare. Vivamus vel tempor libero.'
  },
  {
    id: 3,
    question: 'Mengapa lorem ipsum dolor sit amet',
    answer:
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc fringilla turpis tellus, sed faucibus eros sollicitudin quis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin mollis ipsum sit amet accumsan venenatis. Nam vestibulum molestie ornare. Vivamus vel tempor libero.'
  },
  {
    id: 4,
    question: 'Sistem kontribusi lorem ipsum dolor sit amet',
    answer:
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc fringilla turpis tellus, sed faucibus eros sollicitudin quis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin mollis ipsum sit amet accumsan venenatis. Nam vestibulum molestie ornare. Vivamus vel tempor libero.'
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
