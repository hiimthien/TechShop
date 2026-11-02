<template>
  <div class="flex min-h-screen bg-background text-on-background font-body-md antialiased selection:bg-primary-fixed selection:text-on-primary-fixed">
    <!-- SideNavBar (Admin) -->
    <aside class="hidden md:flex fixed left-0 h-full w-[280px] flex-col py-md gap-sm bg-surface border-r border-outline-variant/40 z-40">
      <div class="px-lg mb-md">
        <RouterLink to="/admin/dashboard" class="block">
          <h1 class="text-headline-sm font-headline-sm font-bold text-primary">TechVina</h1>
          <p class="text-label-sm text-on-surface-variant">Admin Panel Management</p>
        </RouterLink>
      </div>

      <nav class="flex-1 px-sm space-y-1">
        <!-- Dashboard -->
        <RouterLink
          to="/admin/dashboard"
          class="flex items-center gap-md px-md py-sm transition-transform duration-200 hover:translate-x-1"
          :class="isActive('/admin/dashboard') ? 'bg-primary-fixed text-on-primary-fixed border-l-4 border-primary font-bold rounded-r-lg' : 'text-on-surface-variant hover:bg-surface-container rounded-lg'"
        >
          <span class="material-symbols-outlined" :style="isActive('/admin/dashboard') ? 'font-variation-settings: \'FILL\' 1;' : ''">dashboard</span>
          <span class="text-label-md font-label-md">Dashboard</span>
        </RouterLink>

        <!-- Products -->
        <RouterLink
          to="/admin/products"
          class="flex items-center gap-md px-md py-sm transition-transform duration-200 hover:translate-x-1"
          :class="isActive('/admin/products') ? 'bg-primary-fixed text-on-primary-fixed border-l-4 border-primary font-bold rounded-r-lg' : 'text-on-surface-variant hover:bg-surface-container rounded-lg'"
        >
          <span class="material-symbols-outlined" :style="isActive('/admin/products') ? 'font-variation-settings: \'FILL\' 1;' : ''">inventory_2</span>
          <span class="text-label-md font-label-md">Sản phẩm (Products)</span>
        </RouterLink>

        <!-- Categories -->
        <RouterLink
          to="/admin/categories"
          class="flex items-center gap-md px-md py-sm transition-transform duration-200 hover:translate-x-1"
          :class="isActive('/admin/categories') ? 'bg-primary-fixed text-on-primary-fixed border-l-4 border-primary font-bold rounded-r-lg' : 'text-on-surface-variant hover:bg-surface-container rounded-lg'"
        >
          <span class="material-symbols-outlined" :style="isActive('/admin/categories') ? 'font-variation-settings: \'FILL\' 1;' : ''">category</span>
          <span class="text-label-md font-label-md">Danh mục (Categories)</span>
        </RouterLink>

        <!-- Brands -->
        <RouterLink
          to="/admin/brands"
          class="flex items-center gap-md px-md py-sm transition-transform duration-200 hover:translate-x-1"
          :class="isActive('/admin/brands') ? 'bg-primary-fixed text-on-primary-fixed border-l-4 border-primary font-bold rounded-r-lg' : 'text-on-surface-variant hover:bg-surface-container rounded-lg'"
        >
          <span class="material-symbols-outlined" :style="isActive('/admin/brands') ? 'font-variation-settings: \'FILL\' 1;' : ''">branding_watermark</span>
          <span class="text-label-md font-label-md">Thương hiệu (Brands)</span>
        </RouterLink>

        <!-- Orders -->
        <RouterLink
          to="/admin/orders"
          class="flex items-center gap-md px-md py-sm transition-transform duration-200 hover:translate-x-1"
          :class="isActive('/admin/orders') ? 'bg-primary-fixed text-on-primary-fixed border-l-4 border-primary font-bold rounded-r-lg' : 'text-on-surface-variant hover:bg-surface-container rounded-lg'"
        >
          <span class="material-symbols-outlined" :style="isActive('/admin/orders') ? 'font-variation-settings: \'FILL\' 1;' : ''">shopping_bag</span>
          <span class="text-label-md font-label-md">Đơn hàng (Orders)</span>
        </RouterLink>

        <!-- Coupons / Vouchers -->
        <RouterLink
          to="/admin/coupons"
          class="flex items-center gap-md px-md py-sm transition-transform duration-200 hover:translate-x-1"
          :class="isActive('/admin/coupons') ? 'bg-primary-fixed text-on-primary-fixed border-l-4 border-primary font-bold rounded-r-lg' : 'text-on-surface-variant hover:bg-surface-container rounded-lg'"
        >
          <span class="material-symbols-outlined" :style="isActive('/admin/coupons') ? 'font-variation-settings: \'FILL\' 1;' : ''">loyalty</span>
          <span class="text-label-md font-label-md">Mã giảm giá (Vouchers)</span>
        </RouterLink>

        <!-- Warranties RMA -->
        <RouterLink
          to="/admin/warranties"
          class="flex items-center gap-md px-md py-sm transition-transform duration-200 hover:translate-x-1"
          :class="isActive('/admin/warranties') ? 'bg-primary-fixed text-on-primary-fixed border-l-4 border-primary font-bold rounded-r-lg' : 'text-on-surface-variant hover:bg-surface-container rounded-lg'"
        >
          <span class="material-symbols-outlined" :style="isActive('/admin/warranties') ? 'font-variation-settings: \'FILL\' 1;' : ''">verified_user</span>
          <span class="text-label-md font-label-md">Phiếu bảo hành (RMA)</span>
        </RouterLink>
      </nav>

      <!-- Add Product CTA Button from Stitch design -->
      <div class="px-md mt-auto mb-2">
        <RouterLink
          to="/admin/products?action=create"
          class="w-full flex items-center justify-center gap-xs bg-primary hover:bg-primary-container text-on-primary py-sm rounded-xl font-label-md shadow-md active:scale-95 duration-150 transition-all font-bold"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          Thêm sản phẩm mới
        </RouterLink>
      </div>

      <!-- Bottom actions -->
      <div class="px-lg space-y-2">
        <RouterLink
          to="/"
          class="flex items-center gap-md px-md py-sm text-on-surface-variant hover:bg-surface-container rounded-lg transition-all"
        >
          <span class="material-symbols-outlined">storefront</span>
          <span class="text-label-md font-label-md">Về cửa hàng</span>
        </RouterLink>

        <div class="border-t border-outline-variant/40 pt-xs">
          <button
            @click="handleLogout"
            class="w-full flex items-center gap-md px-md py-sm text-on-surface-variant hover:text-error hover:bg-error-container/20 rounded-lg transition-all"
          >
            <span class="material-symbols-outlined">logout</span>
            <span class="text-label-md font-label-md">Đăng xuất</span>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content Canvas -->
    <div class="flex-1 md:ml-[280px] min-h-screen flex flex-col min-w-0">
      <!-- TopNavBar -->
      <header class="sticky top-0 w-full h-16 bg-surface/90 backdrop-blur-md border-b border-outline-variant/40 px-6 flex justify-between items-center z-30">
        <div class="flex items-center gap-md">
          <RouterLink to="/" class="md:hidden text-primary font-bold text-lg">TechVina Admin</RouterLink>
          <h2 class="text-headline-sm font-headline-sm font-bold text-on-surface hidden md:block">
            {{ pageTitle }}
          </h2>
        </div>

        <div class="flex items-center gap-md">
          <div class="hidden md:flex bg-surface-container rounded-full px-4 py-1.5 items-center gap-2 w-64 border border-outline-variant/30">
            <span class="material-symbols-outlined text-on-surface-variant text-[20px]">search</span>
            <input class="bg-transparent border-none focus:ring-0 text-body-sm w-full outline-none placeholder:text-on-surface-variant/60" placeholder="Tìm kiếm nhanh..." type="text"/>
          </div>

          <div class="flex items-center gap-sm">
            <div class="w-8 h-8 rounded-full bg-primary-fixed overflow-hidden border border-outline-variant flex items-center justify-center font-bold text-primary text-xs">
              {{ authStore.user?.name?.charAt(0) || 'A' }}
            </div>
            <div class="hidden sm:block text-left">
              <div class="text-label-sm font-bold text-on-surface leading-tight">{{ authStore.user?.name || 'Administrator' }}</div>
              <div class="text-[11px] text-primary font-semibold">Admin TechVina</div>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-6 md:p-8 bg-background min-w-0 overflow-x-hidden">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/modules/auth/stores/authStore'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

function isActive(path: string) {
  if (path === '/admin/dashboard') {
    return route.path === '/admin' || route.path === '/admin/dashboard'
  }
  return route.path.startsWith(path)
}

const pageTitle = computed(() => {
  if (route.path.includes('/admin/products')) return 'Quản lý sản phẩm (Products)'
  if (route.path.includes('/admin/categories')) return 'Quản lý danh mục (Categories)'
  if (route.path.includes('/admin/brands')) return 'Quản lý thương hiệu (Brands)'
  if (route.path.includes('/admin/orders')) return 'Quản lý đơn hàng (Orders)'
  if (route.path.includes('/admin/coupons')) return 'Quản lý mã giảm giá (Vouchers)'
  if (route.path.includes('/admin/warranties')) return 'Quản lý phiếu bảo hành (RMA)'
  return 'Tổng quan hệ thống (Dashboard)'
})

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>
