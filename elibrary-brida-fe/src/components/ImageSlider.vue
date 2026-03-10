<!-- filepath: d:\PAD_PROJECT\elibrary-brida\elibrary-brida-fe\src\components\ImageSlider.vue -->
<template>
  <div class="flex items-center justify-center mt-8">
    <div class="border-2 border-gray-300 rounded-lg p-4 shadow-lg w-full max-w-7xl">
      <Swiper
      :modules="[Autoplay]"
      :autoplay="{ delay: 3000, disableOnInteraction: false }"
      :loop="true"
    >
      <SwiperSlide><img src="@/assets/land1.jpg" alt="Slide 1" class="w-full h-auto rounded" /></SwiperSlide>
      <SwiperSlide><img src="@/assets/land2.jpg" alt="Slide 2" class="w-full h-auto rounded" /></SwiperSlide>
      <SwiperSlide><img src="@/assets/land3.jpg" alt="Slide 3" class="w-full h-auto rounded" /></SwiperSlide>
    </Swiper>
    </div>
  </div>

</template>

<script setup lang="ts">
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay } from 'swiper/modules'
import 'swiper/css'
</script>
