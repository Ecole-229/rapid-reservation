<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import AppAdmin from '@/components/admin/AppAdmin.vue'

const authStore = useAuthStore()

const handleLogout = () => {
  authStore.logout()
}
</script>

<template>
 <AppAdmin>
    users
 </AppAdmin>
</template>



