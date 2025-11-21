<template>
  <router-view />

  <!-- Role Change Notification -->
  <RoleChangeNotification
    :show="roleNotification.show"
    :old-role="roleNotification.oldRole"
    :new-role="roleNotification.newRole"
    :message="roleNotification.message"
    @dismiss="dismissRoleNotification"
  />

  <!-- Toast Notifications -->
  <ToastNotification ref="toastRef" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterView } from 'vue-router'
import RoleChangeNotification from '@/components/RoleChangeNotification.vue'
import ToastNotification from '@/components/ToastNotification.vue'
import { useRoleChangeDetector } from '@/composables/useRoleChangeDetector'
import { useToast } from '@/composables/useToast'

const { notification: roleNotification, dismissNotification: dismissRoleNotification } = useRoleChangeDetector()

const toastRef = ref<InstanceType<typeof ToastNotification> | null>(null)
const { setToastInstance } = useToast()

onMounted(() => {
  if (toastRef.value) {
    setToastInstance(toastRef.value)
  }
})
</script>

<style>
/* Global styles sudah ada di main.css dan base.css */
</style>
