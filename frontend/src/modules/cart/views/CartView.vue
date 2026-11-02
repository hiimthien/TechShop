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
        class="mt-2 px-6 py-3 bg-primary text-on-primary font-bold rounded-xl text-sm hover:bg-primary-container transition-all shadow-md active:scale-95"
      >
        Khám phá sản phẩm ngay
      </RouterLink>
    </div>

    <!-- Cart Main Grid -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Left: Cart Items List (8 cols) -->
      <div class="lg:col-span-8 flex flex-col gap-4">
        <div
          v-for="item in cartStore.items"
          :key="item.variant_id"
          class="flex flex-col sm:flex-row gap-4 p-4 md:p-6 bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/30 transition-all hover:shadow-md"
        >
          <!-- Thumbnail -->
          <div class="w-full sm:w-28 h-28 bg-white rounded-xl overflow-hidden flex-shrink-0 border border-outline-variant/20 p-2 flex items-center justify-center">
            <img
              :src="item.thumbnail || fallbackImage"
              :alt="item.product_name"
              class="w-full h-full object-contain"
            />
          </div>

          <!-- Item Details -->
          <div class="flex-grow flex flex-col justify-between py-1">
            <div>
              <div class="flex justify-between items-start gap-2">
                <RouterLink
                  :to="`/products/${item.product_slug}`"
                  class="text-base font-bold text-on-surface hover:text-primary transition-colors line-clamp-2"
                >
                  {{ item.product_name }}
                </RouterLink>
                <button
                  type="button"
                  @click="cartStore.removeItem(item.id)"
                  class="text-outline hover:text-error transition-colors p-1"
                  title="Xóa khỏi giỏ hàng"
                >
                  <span class="material-symbols-outlined text-[20px]">delete</span>
                </button>
              </div>

              <p class="text-xs text-on-surface-variant mt-1">
                Phiên bản: <span class="font-medium">{{ item.variant_option || item.variant_sku }}</span>
              </p>
            </div>

            <div class="flex flex-wrap justify-between items-end gap-3 mt-4 pt-3 border-t border-outline-variant/20">
              <!-- Quantity Controls -->
              <div class="flex items-center bg-surface-container rounded-lg p-1">
                <button
                  type="button"
                  @click="cartStore.updateQuantity(item.id, item.quantity - 1)"
                  class="w-7 h-7 flex items-center justify-center hover:bg-surface-container-high rounded transition-colors text-on-surface"
                >
                  <span class="material-symbols-outlined text-sm">remove</span>
                </button>
                <input
                  type="number"
                  :value="item.quantity"
                  @change="handleQuantityInput(item.id, $event)"
                  min="1"
                  class="w-10 bg-transparent border-none text-center focus:outline-none font-bold text-xs"
                />
                <button
                  type="button"
                  @click="cartStore.updateQuantity(item.id, item.quantity + 1)"
                  class="w-7 h-7 flex items-center justify-center hover:bg-surface-container-high rounded transition-colors text-on-surface"
                >
                  <span class="material-symbols-outlined text-sm">add</span>
                </button>
              </div>

              <!-- Price -->
              <div class="text-right">
                <span class="text-lg font-black text-primary">
                  {{ formatPrice(item.price * item.quantity) }}
                </span>
                <span v-if="item.quantity > 1" class="block text-[11px] text-on-surface-variant">
                  ({{ formatPrice(item.price) }}/cái)
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Back to shopping -->
        <div class="flex justify-between items-center pt-2">
          <RouterLink
            to="/products"
            class="text-primary hover:underline text-sm font-bold flex items-center gap-1"
          >
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>Tiếp tục mua sắm</span>
          </RouterLink>
          <button
            @click="cartStore.clearCart"
            class="text-xs text-outline hover:text-error transition-colors"
          >
            Xóa toàn bộ giỏ hàng
          </button>
        </div>
      </div>

      <!-- Right: Order Summary (4 cols) -->
      <aside class="lg:col-span-4 sticky top-24">
        <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-outline-variant/30 flex flex-col gap-6">
          <h2 class="text-lg font-black text-on-surface pb-3 border-b border-outline-variant/20">
            Tóm tắt đơn hàng
          </h2>

          <div class="space-y-3 text-sm">
            <div class="flex justify-between text-on-surface-variant">
              <span>Tạm tính</span>
              <span class="text-on-surface font-semibold">{{ formatPrice(cartStore.subtotal) }}</span>
            </div>

            <div class="flex justify-between text-on-surface-variant">
              <span>Phí vận chuyển</span>
              <span v-if="cartStore.shippingFee === 0" class="text-primary font-semibold">
                Miễn phí
              </span>
              <span v-else class="text-on-surface font-semibold">
                {{ formatPrice(cartStore.shippingFee) }}
              </span>
            </div>

            <p v-if="cartStore.shippingFee > 0" class="text-[11px] text-outline">
              Mua thêm <strong>{{ formatPrice(500000 - cartStore.subtotal) }}</strong> để được miễn phí ship.
            </p>
          </div>

          <!-- Coupon Input -->
          <div class="pt-2">
            <label class="text-xs font-semibold text-on-surface-variant block mb-1.5">
              Mã ưu đãi / Voucher
            </label>
            <div class="flex gap-2">
              <input
                v-model="couponCode"
                placeholder="Nhập mã voucher..."
                type="text"
                class="flex-grow bg-surface-container-low border border-outline-variant/50 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-primary uppercase"
              />
              <button
                type="button"
                @click="applyCoupon"
                class="bg-surface-container-high px-4 rounded-xl text-xs font-bold hover:bg-outline-variant transition-colors"
              >
                Áp dụng
              </button>
            </div>
            <p v-if="couponMessage" class="text-xs text-primary mt-1.5 font-medium">
              {{ couponMessage }}
            </p>
          </div>

          <!-- Total Calculation -->
          <div class="pt-4 border-t border-outline-variant/20 flex justify-between items-baseline">
            <span class="text-base font-bold text-on-surface">Tổng cộng</span>
            <div class="text-right">
              <p class="text-2xl font-black text-primary leading-tight">
                {{ formatPrice(cartStore.totalAmount) }}
              </p>
              <p class="text-[10px] text-on-surface-variant mt-0.5">(Đã bao gồm thuế VAT)</p>
            </div>
          </div>

          <!-- Checkout CTA -->
          <button
            @click="handleCheckout"
            type="button"
            class="w-full bg-primary text-on-primary py-3.5 rounded-xl font-bold text-sm shadow-md hover:bg-primary-container transition-all active:scale-[0.98] flex items-center justify-center gap-2"
          >
            <span>Tiến hành thanh toán</span>
            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
          </button>

          <!-- Security note -->
          <div class="flex items-center justify-center gap-2 text-on-surface-variant text-xs">
            <span class="material-symbols-outlined text-sm text-primary">verified_user</span>
            <span>Thanh toán an toàn & bảo mật 100%</span>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useCartStore } from '../stores/cartStore'

const router = useRouter()
const cartStore = useCartStore()

const couponCode = ref('')
const couponMessage = ref('')

const fallbackImage =
  'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect fill="%23f0eded" width="200" height="200"/><text fill="%237b7487" font-family="sans-serif" font-size="14" dy="5" font-weight="bold" x="50%" y="50%" text-anchor="middle">TechShop</text></svg>'

const formatPrice = (price: number | string | null | undefined): string => {
  if (price === null || price === undefined || price === '') return '0 ₫'
  const numeric = typeof price === 'string' ? parseFloat(price) : price
  if (isNaN(numeric)) return '0 ₫'
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(numeric)
}

const handleQuantityInput = (itemId: number, event: Event) => {
  const target = event.target as HTMLInputElement
  const val = parseInt(target.value, 10)
  if (!isNaN(val) && val > 0) {
    cartStore.updateQuantity(itemId, val)
  }
}

const applyCoupon = () => {
  if (couponCode.value.trim().toUpperCase() === 'TECHVINA') {
    couponMessage.value = 'Mã TECHVINA hợp lệ! Giảm 50.000₫ cho đơn hàng.'
  } else if (couponCode.value.trim()) {
    couponMessage.value = 'Mã ưu đãi không hợp lệ hoặc đã hết hạn.'
  }
}

const handleCheckout = () => {
  router.push('/checkout')
}
</script>
