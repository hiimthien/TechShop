<template>
  <div v-if="isAdminRoute" class="min-h-screen bg-background text-on-background font-body-md">
    <RouterView />
  </div>
  <div v-else-if="isAuthRoute" class="min-h-screen bg-surface text-on-surface">
    <RouterView />
  </div>
  <div v-else class="min-h-screen flex flex-col bg-surface text-on-surface">
    <TheHeader />
    <main class="flex-grow">
      <RouterView v-slot="{ Component }">
        <KeepAlive :include="['ProductListView']">
          <component :is="Component" />
        </KeepAlive>
      </RouterView>
    </main>
    <TheFooter />
  </div>

  <!-- Global Toast & SweetAlert Confirm Dialog -->
  <ToastContainer />
  <ConfirmDialog />
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { RouterView, useRoute } from 'vue-router'
import { useAuthStore } from '@/modules/auth/stores/authStore'
import { useCartStore } from '@/modules/cart/stores/cartStore'
import TheHeader from '@/core/layout/TheHeader.vue'
import TheFooter from '@/core/layout/TheFooter.vue'
import ToastContainer from '@/core/components/ui/ToastContainer.vue'
import ConfirmDialog from '@/core/components/ui/ConfirmDialog.vue'

const route = useRoute()
const authStore = useAuthStore()
const cartStore = useCartStore()

const isAdminRoute = computed(() => route.path.startsWith('/admin'))
const isAuthRoute = computed(() => {
  return (
    Boolean(route.meta.hideHeaderFooter) ||
    ['/login', '/register', '/auth/callback'].includes(route.path)
  )
})

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
