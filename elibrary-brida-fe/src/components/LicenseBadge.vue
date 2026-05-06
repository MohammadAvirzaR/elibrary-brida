<template>
  <span
    :class="[
      'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
      isCreativeCommons(licenseType) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700',
    ]"
  >
    {{ getLicenseLabel(licenseType) }}
  </span>
</template>

<script setup lang="ts">
import { getLicenseLabel, isCreativeCommons } from '@/utils/license'

defineProps<{
  licenseType?: string | null
}>()
</script>
