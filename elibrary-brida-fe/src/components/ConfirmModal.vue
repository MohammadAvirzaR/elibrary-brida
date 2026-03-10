<template>
  <div
    v-if="show"
    class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 p-4"
  >
    <div class="w-full max-w-md rounded-xl bg-white shadow-xl border border-neutral-200">
      <div class="p-5">
        <h3 class="text-lg font-semibold text-neutral-900">{{ title }}</h3>
        <p class="mt-2 text-sm text-neutral-600 whitespace-pre-line">{{ message }}</p>
      </div>
      <div class="flex justify-end gap-2 px-5 pb-5">
        <button
          @click="$emit('cancel')"
          class="px-4 py-2 rounded-lg border border-neutral-300 text-neutral-700 hover:bg-neutral-50"
        >
          {{ cancelText }}
        </button>
        <button
          @click="$emit('confirm')"
          class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700"
        >
          {{ confirmText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  show: boolean
  title?: string
  message?: string
  confirmText?: string
  cancelText?: string
}>(), {
  title: 'Konfirmasi',
  message: 'Apakah Anda yakin?',
  confirmText: 'Ya, lanjutkan',
  cancelText: 'Batal'
})

defineEmits<{
  (e: 'confirm'): void
  (e: 'cancel'): void
}>()
</script>
