<template>
  <div v-if="isAdminRoute" class="min-h-screen bg-background text-on-background font-body-md">
    <RouterView />
  </div>
  <div v-else class="min-h-screen flex flex-col bg-surface text-on-surface">
    <TheHeader />
    <main class="flex-grow">
      <RouterView />
    </main>
    <TheFooter />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { RouterView, useRoute } from 'vue-router'
import { useAuthStore } from '@/modules/auth/stores/authStore'
import { useCartStore } from '@/modules/cart/stores/cartStore'
import TheHeader from '@/core/layout/TheHeader.vue'
import TheFooter from '@/core/layout/TheFooter.vue'

const route = useRoute()
const authStore = useAuthStore()
const cartStore = useCartStore()

const isAdminRoute = computed(() => route.path.startsWith('/admin'))

onMounted(async () => {
  if (localStorage.getItem('token')) {
    await Promise.all([
      authStore.fetchUser(),
      cartStore.fetchCart(),
    ])
  }
})
</script>

<style scoped></style>
