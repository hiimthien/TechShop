<template>
  <header
    class="bg-surface/90 dark:bg-surface-dim/90 docked full-width top-0 sticky backdrop-blur-md border-b border-outline-variant/40 dark:border-outline shadow-xs z-50"
  >
    <div class="flex justify-between items-center w-full px-4 max-w-7xl mx-auto h-16 gap-3 md:gap-6">
      <!-- Cụm trái: Logo & Mega Menu -->
      <div class="flex items-center gap-3 shrink-0">
        <RouterLink
          class="text-xl md:text-2xl font-black text-primary tracking-tight flex items-center gap-1.5 hover:opacity-90 transition-opacity"
          to="/"
        >
          <span class="material-symbols-outlined text-[26px]">devices</span>
          <span>TechShop</span>
        </RouterLink>

        <!-- Mega Menu Button -->
        <MegaMenu />
      </div>

      <!-- Cụm giữa: Search Bar mở rộng kèm Live Search Preview Dropdown -->
      <div
        ref="searchContainerRef"
        class="flex-1 max-w-2xl hidden sm:flex relative items-center bg-surface-container-low rounded-xl px-3.5 py-2 border border-outline-variant/50 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/15 transition-all"
      >
        <span class="material-symbols-outlined text-outline text-[20px] shrink-0 leading-none select-none">search</span>
        <input
          v-model="headerSearch"
          @focus="isSearchDropdownOpen = true"
          @keyup.enter="handleHeaderSearch"
          @keydown.esc="isSearchDropdownOpen = false"
          class="bg-transparent !border-0 !border-none !ring-0 !shadow-none !outline-none text-xs w-full placeholder:text-outline ml-2 text-on-surface"
          placeholder="Bạn muốn tìm sản phẩm nào hôm nay (iPhone 16, Laptop Gaming, Tai nghe...)?"
          type="text"
        />
        <button
          v-if="headerSearch"
          @click="headerSearch = ''; isSearchDropdownOpen = false"
          type="button"
          class="text-outline hover:text-on-surface text-[14px]"
        >
          ✕
        </button>

        <!-- Live Search Dropdown Component -->
        <SearchPreviewDropdown
          :query="headerSearch"
          :is-open="isSearchDropdownOpen"
          @close="isSearchDropdownOpen = false"
          @select-keyword="handleSelectKeyword"
        />
      </div>

      <!-- Cụm phải: Giỏ hàng & Tài khoản -->
      <div class="flex items-center gap-2 md:gap-3 shrink-0">
        <!-- Giỏ hàng kèm hiệu ứng Bounce khi thêm món -->
        <RouterLink
          to="/cart"
          class="relative p-2 text-on-surface hover:text-primary hover:bg-surface-container transition-all rounded-xl active:scale-95 duration-150 flex items-center gap-1.5"
          :class="{ 'scale-110 text-primary': cartBouncing }"
          title="Giỏ hàng"
        >
          <span
            class="material-symbols-outlined text-[22px] transition-transform duration-300"
            :class="{ 'scale-125 text-primary animate-bounce': cartBouncing }"
          >
            shopping_cart
          </span>
          <span class="text-xs font-bold hidden md:inline">Giỏ hàng</span>
          <span
            v-if="cartStore.totalItems > 0"
            class="absolute -top-1 -right-1 bg-secondary text-on-secondary text-[10px] font-black w-4 h-4 flex items-center justify-center rounded-full shadow-sm transition-transform duration-300"
            :class="{ 'scale-125 bg-primary animate-ping': cartBouncing }"
          >
            {{ cartStore.totalItems > 99 ? '99+' : cartStore.totalItems }}
          </span>
        </RouterLink>

        <!-- Thông báo (khi đã đăng nhập) -->
        <NotificationDropdown v-if="user" />

        <!-- Tài khoản & Auth -->
        <div v-if="user" class="flex items-center gap-2">
          <RouterLink
            v-if="user.role === 'admin'"
            to="/admin/dashboard"
            class="px-2.5 py-1 rounded-lg bg-primary/10 text-primary text-xs font-bold border border-primary/20 hover:bg-primary hover:text-on-primary transition flex items-center gap-1"
          >
            <span class="material-symbols-outlined text-[15px]">admin_panel_settings</span>
            Admin
          </RouterLink>
          <RouterLink
            to="/account"
            class="flex items-center gap-2 py-1 px-2 rounded-xl hover:bg-surface-container-low transition-colors group"
          >
            <img
              v-if="user.avatar"
              :src="user.avatar"
              :alt="user.name"
              class="w-6 h-6 rounded-full object-cover ring-1 ring-primary/30"
            />
            <span
              v-else
              class="w-6 h-6 rounded-full bg-primary/15 text-primary text-xs font-black flex items-center justify-center"
            >
              {{ (user.name || 'U').charAt(0).toUpperCase() }}
            </span>
            <span class="text-xs text-on-surface group-hover:text-primary font-bold truncate max-w-[110px]">{{ user.name }}</span>
          </RouterLink>
          <button
            @click="handleLogout"
            class="text-xs text-outline hover:text-primary hover:bg-primary/5 transition-colors p-1.5 rounded-lg"
            title="Đăng xuất"
          >
            <span class="material-symbols-outlined text-[18px]">logout</span>
          </button>
        </div>
        <div v-else class="flex items-center gap-2">
          <RouterLink
            to="/login"
            class="text-xs font-bold text-on-surface hover:text-primary px-2 py-1"
          >
            Đăng nhập
          </RouterLink>
          <RouterLink
            to="/register"
            class="text-xs font-bold bg-primary hover:bg-primary-container text-on-primary px-3 py-1.5 rounded-xl transition-all shadow-xs"
          >
            Đăng ký
          </RouterLink>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/modules/auth/stores/authStore'
import { useCartStore } from '@/modules/cart/stores/cartStore'
import MegaMenu from './MegaMenu.vue'
import NotificationDropdown from '@/core/components/NotificationDropdown.vue'
import SearchPreviewDropdown from '@/core/components/SearchPreviewDropdown.vue'

const authStore = useAuthStore()
const cartStore = useCartStore()
const router = useRouter()

const headerSearch = ref('')
const isSearchDropdownOpen = ref(false)
const searchContainerRef = ref<HTMLElement | null>(null)
const cartBouncing = ref(false)

const { user } = storeToRefs(authStore)

const handleHeaderSearch = () => {
  if (headerSearch.value.trim()) {
    isSearchDropdownOpen.value = false
    router.push({ path: '/products', query: { search: headerSearch.value.trim() } })
    headerSearch.value = ''
  }
}

const handleSelectKeyword = (kw: string) => {
  headerSearch.value = kw
  handleHeaderSearch()
}

const handleLogout = () => {
  authStore.logout()
  router.push('/login')
}

// Bắt sự kiện giỏ hàng tăng số lượng để kích hoạt bounce effect
watch(
  () => cartStore.totalItems,
  (newVal, oldVal) => {
    if (newVal > oldVal) {
      cartBouncing.value = true
      setTimeout(() => {
        cartBouncing.value = false
      }, 800)
    }
  }
)

// Đóng dropdown khi click ra ngoài
function handleClickOutside(event: MouseEvent) {
  if (searchContainerRef.value && !searchContainerRef.value.contains(event.target as Node)) {
    isSearchDropdownOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
