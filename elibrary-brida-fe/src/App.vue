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
</template>

<script setup lang="ts">
import { RouterView } from 'vue-router'
import RoleChangeNotification from '@/components/RoleChangeNotification.vue'
import { useRoleChangeDetector } from '@/composables/useRoleChangeDetector'

const { notification: roleNotification, dismissNotification: dismissRoleNotification } = useRoleChangeDetector()
</script>

<style>
/* Global styles sudah ada di main.css dan base.css */
</style>
