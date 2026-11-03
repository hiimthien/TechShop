<template>
  <div class="max-w-container-max mx-auto px-lg py-8 md:py-12">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-xs text-xs text-on-surface-variant mb-6">
      <RouterLink to="/" class="hover:text-primary transition-colors">Trang chủ</RouterLink>
      <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      <span class="text-on-surface font-medium">Giỏ hàng</span>
    </nav>

    <h1 class="text-2xl md:text-3xl font-black text-on-surface mb-8">
      Giỏ hàng của bạn
      <span v-if="cartStore.totalItems > 0" class="text-primary font-bold text-lg md:text-xl ml-2">
        ({{ cartStore.totalItems }} sản phẩm)
      </span>
    </h1>

    <!-- Empty State -->
    <div
      v-if="cartStore.items.length === 0"
      class="py-16 text-center bg-surface-container-lowest rounded-2xl border border-outline-variant/30 flex flex-col items-center justify-center gap-4"
    >
      <div class="w-24 h-24 rounded-full bg-surface-container flex items-center justify-center text-outline">
        <span class="material-symbols-outlined text-5xl">shopping_cart</span>
      </div>
      <h2 class="text-xl font-bold text-on-surface">Giỏ hàng của bạn đang trống</h2>
      <p class="text-sm text-on-surface-variant max-w-sm">
        Hãy dạo quanh cửa hàng và chọn những thiết bị công nghệ bạn yêu thích nhé!
      </p>
      <RouterLink
        to="/products"
        class="mt-2 px-6 py-3 bg-primary text-on-primary font-bold rounded-xl text-sm hover:bg-primary-hover transition-all shadow-md active:scale-95"
      >
        Khám phá sản phẩm ngay
      </RouterLink>
    </div>

    <!-- Cart Main Grid -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Left: Cart Items List (8 cols) -->
      <div class="lg:col-span-8">
        <CartItemList
          :items="cartStore.items"
          @update-quantity="cartStore.updateQuantity($event.id, $event.quantity)"
          @remove-item="cartStore.removeItem"
          @clear-cart="cartStore.clearCart"
        />
      </div>

      <!-- Right: Order Summary (4 cols) -->
      <div class="lg:col-span-4">
        <CartSummary
          :subtotal="cartStore.subtotal"
          :shipping-fee="cartStore.shippingFee"
          :total-amount="cartStore.totalAmount"
          @checkout="router.push('/checkout')"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter, RouterLink } from 'vue-router'
import { useCartStore } from '../stores/cartStore'
import CartItemList from '../components/CartItemList.vue'
import CartSummary from '../components/CartSummary.vue'

const router = useRouter()
const cartStore = useCartStore()
</script>
