<template>
  <header
    class="bg-surface/80 dark:bg-surface-dim/80 docked full-width top-0 sticky backdrop-blur-md border-b border-outline-variant dark:border-outline shadow-sm z-50"
  >
    <div class="flex justify-between items-center w-full px-lg max-w-container-max mx-auto h-16">
      <div class="flex items-center gap-xl">
        <RouterLink
          class="text-headline-md font-headline-md font-black text-primary dark:text-primary-fixed-dim"
          to="/"
        >
          TechVina
        </RouterLink>
        <nav class="hidden md:flex items-center gap-lg">
          <RouterLink
            class="text-on-surface-variant hover:text-primary transition-colors py-2 text-label-md font-label-md"
            to="/products"
          >
            Tất cả sản phẩm
          </RouterLink>
        </nav>
      </div>

      <div class="flex items-center gap-md">
        <div
          class="hidden lg:flex items-center bg-surface-container rounded-full px-md py-xs w-64 border border-outline-variant"
        >
          <span class="material-symbols-outlined text-on-surface-variant text-[20px]">search</span>
          <input
            v-model="headerSearch"
            @keyup.enter="handleHeaderSearch"
            class="bg-transparent border-none focus:ring-0 text-body-sm font-body-sm w-full placeholder:text-outline ml-xs"
            placeholder="Tìm kiếm sản phẩm..."
            type="text"
          />
        </div>

        <div class="flex items-center gap-sm">
          <RouterLink
            to="/cart"
            class="relative p-xs text-on-surface-variant hover:bg-surface-container-low transition-all rounded-full active:scale-95 duration-150"
            title="Giỏ hàng"
          >
            <span class="material-symbols-outlined">shopping_cart</span>
            <span
              v-if="cartStore.totalItems > 0"
              class="absolute -top-1 -right-1 bg-primary text-on-primary text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full shadow-sm"
            >
              {{ cartStore.totalItems > 99 ? '99+' : cartStore.totalItems }}
            </span>
          </RouterLink>

          <div v-if="user" class="flex items-center gap-sm">
            <RouterLink
              v-if="user.role === 'admin'"
              to="/admin/dashboard"
              class="px-2.5 py-1 rounded-lg bg-purple-600/10 text-purple-600 text-xs font-bold border border-purple-600/20 hover:bg-purple-600 hover:text-white transition flex items-center gap-1"
            >
              <span class="material-symbols-outlined text-[15px]">admin_panel_settings</span>
              CMS Admin
            </RouterLink>
            <span class="text-label-md text-on-surface font-medium">{{ user.name }}</span>
            <button @click="handleLogout" class="text-label-md text-primary hover:underline">
              Đăng xuất
            </button>
          </div>
          <div v-else class="flex items-center gap-sm">
            <RouterLink to="/login" class="text-label-md text-on-surface-variant hover:text-primary">
              Đăng nhập
            </RouterLink>
            <RouterLink
              to="/register"
              class="text-label-md bg-primary text-on-primary px-md py-xs rounded-lg hover:bg-primary-container transition-colors"
            >
              Đăng ký
            </RouterLink>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/modules/auth/stores/authStore'
import { useCartStore } from '@/modules/cart/stores/cartStore'

const authStore = useAuthStore()
const cartStore = useCartStore()
const router = useRouter()
const headerSearch = ref('')

const { user } = storeToRefs(authStore)

const handleHeaderSearch = () => {
  if (headerSearch.value.trim()) {
    router.push({ path: '/products', query: { search: headerSearch.value.trim() } })
    headerSearch.value = ''
  }
}

const handleLogout = () => {
  authStore.logout()
  router.push('/login')
}
</script>
