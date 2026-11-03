<template>
  <aside class="sticky top-24">
    <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/30 flex flex-col gap-5">
      <div class="flex items-center justify-between pb-3 border-b border-outline-variant/20">
        <h2 class="text-lg font-black text-on-surface">Đơn hàng của bạn</h2>
        <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">
          {{ totalItems }} sản phẩm
        </span>
      </div>

      <!-- Items List -->
      <div class="divide-y divide-outline-variant/20 max-h-64 overflow-y-auto pr-1">
        <div
          v-for="item in items"
          :key="item.variant_id"
          class="py-3 flex gap-3 items-center"
        >
          <div class="w-14 h-14 rounded-lg bg-surface-container-low border border-outline-variant/30 p-1 flex-shrink-0 flex items-center justify-center">
            <img
              :src="item.thumbnail || fallbackImage"
              :alt="item.product_name"
              class="w-full h-full object-contain"
            />
          </div>
          <div class="flex-grow min-w-0">
            <h4 class="text-xs font-bold text-on-surface truncate">{{ item.product_name }}</h4>
            <p class="text-[11px] text-on-surface-variant">SL: {{ item.quantity }}</p>
            <p class="text-xs font-bold text-primary mt-0.5">{{ formatPrice(item.price * item.quantity) }}</p>
          </div>
        </div>
      </div>

      <!-- Voucher / Coupon Box -->
      <div class="pt-2 border-t border-outline-variant/20 space-y-2">
        <label class="text-xs font-bold text-on-surface flex items-center gap-1.5">
          <span class="material-symbols-outlined text-[16px] text-primary">confirmation_number</span>
          Mã khuyến mãi / Voucher
        </label>

        <div v-if="appliedCoupon" class="flex items-center justify-between p-2.5 rounded-xl bg-primary/10 border border-primary/20 text-xs">
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[18px]">verified</span>
            <div>
              <span class="font-bold text-primary">{{ appliedCoupon.code }}</span>
              <span class="text-on-surface-variant text-[11px] block">Giảm: -{{ formatPrice(discountAmount) }}</span>
            </div>
          </div>
          <button
            type="button"
            @click="removeCoupon"
            class="text-[11px] text-error hover:underline font-bold px-2 py-1"
          >
            Gỡ bỏ
          </button>
        </div>

        <div v-else class="flex gap-2">
          <input
            v-model="couponCodeInput"
            @keydown.enter.prevent="handleApplyCoupon"
            type="text"
            placeholder="Nhập mã voucher (VD: TECHSHOP50)"
            class="flex-1 px-3 py-2 rounded-xl border border-outline-variant/50 uppercase text-xs focus:border-primary focus:ring-1 focus:ring-primary/20 bg-surface text-on-surface placeholder:normal-case font-mono"
          />
          <button
            type="button"
            :disabled="isCheckingCoupon || !couponCodeInput.trim()"
            @click="handleApplyCoupon"
            class="px-3.5 py-2 bg-primary text-on-primary text-xs font-bold rounded-xl hover:bg-primary-hover disabled:opacity-50 transition-all flex items-center gap-1 shrink-0"
          >
            <span v-if="isCheckingCoupon" class="material-symbols-outlined animate-spin text-xs">progress_activity</span>
            <span>Áp dụng</span>
          </button>
        </div>

        <p v-if="couponMessage" :class="couponError ? 'text-error' : 'text-primary'" class="text-[11px] font-semibold mt-1">
          {{ couponMessage }}
        </p>
      </div>

      <!-- Price Breakdown -->
      <div class="space-y-2.5 pt-3 border-t border-outline-variant/20 text-xs">
        <div class="flex justify-between text-on-surface-variant">
          <span>Tạm tính</span>
          <span class="font-bold text-on-surface">{{ formatPrice(subtotal) }}</span>
        </div>
        <div class="flex justify-between text-on-surface-variant">
          <span>Phí vận chuyển</span>
          <span v-if="shippingFee === 0" class="text-primary font-bold">Miễn phí</span>
          <span v-else class="font-bold text-on-surface">{{ formatPrice(shippingFee) }}</span>
        </div>
        <div v-if="discountAmount > 0" class="flex justify-between text-primary font-bold">
          <span>Giảm giá voucher</span>
          <span>-{{ formatPrice(discountAmount) }}</span>
        </div>
        <div class="flex justify-between pt-3 border-t border-outline-variant/20 text-sm">
          <span class="font-bold text-on-surface">Tổng thanh toán</span>
          <span class="text-xl font-black text-primary">{{ formatPrice(finalTotal) }}</span>
        </div>
      </div>

      <!-- Submit CTA -->
      <button
        type="button"
        :disabled="submitting"
        @click="$emit('place-order')"
        class="w-full py-4 bg-primary text-on-primary font-bold rounded-xl text-sm shadow-md hover:bg-primary-hover transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
      >
        <span v-if="submitting" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
        <span>{{ submitting ? 'Đang tạo đơn hàng...' : 'Xác nhận đặt hàng' }}</span>
      </button>

      <p class="text-[11px] text-outline text-center leading-relaxed">
        Bằng cách nhấn "Xác nhận đặt hàng", bạn đồng ý với các Điều khoản & Chính sách giao dịch của TechShop.
      </p>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import type { CartItem } from '@/modules/cart/types'
import { useFormatters } from '@/core/composables/useFormatters'
import { checkoutService } from '../services/checkoutService'

interface Props {
  items: CartItem[]
  totalItems: number
  subtotal: number
  shippingFee: number
  submitting?: boolean
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'place-order'): void
  (e: 'update:coupon', coupon: { code: string; discount_amount: number } | null): void
}>()

const { formatPrice } = useFormatters()
const fallbackImage =
  'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect fill="%23f0eded" width="100" height="100"/><text fill="%237b7487" font-family="sans-serif" font-size="12" dy="4" font-weight="bold" x="50%" y="50%" text-anchor="middle">TechShop</text></svg>'

const couponCodeInput = ref('')
const isCheckingCoupon = ref(false)
const couponMessage = ref('')
const couponError = ref(false)
const appliedCoupon = ref<{ code: string; discount_amount: number } | null>(null)

const discountAmount = computed(() => appliedCoupon.value?.discount_amount || 0)

const finalTotal = computed(() => {
  return Math.max(0, props.subtotal - discountAmount.value) + props.shippingFee
})

async function handleApplyCoupon() {
  const code = couponCodeInput.value.trim().toUpperCase()
  if (!code) return

  isCheckingCoupon.value = true
  couponMessage.value = ''
  couponError.value = false

  try {
    const res = await checkoutService.checkCoupon(code, props.subtotal)
    if (res.status && res.data) {
      appliedCoupon.value = {
        code: code,
        discount_amount: res.data.discount_amount,
      }
      couponMessage.value = `Áp dụng thành công! Đã giảm ${formatPrice(res.data.discount_amount)}`
      couponError.value = false
      couponCodeInput.value = ''
      emit('update:coupon', appliedCoupon.value)
    } else {
      couponMessage.value = res.message || 'Mã giảm giá không hợp lệ.'
      couponError.value = true
    }
  } catch (err: unknown) {
    const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    couponMessage.value = msg || 'Mã giảm giá không hợp lệ hoặc đã hết lượt.'
    couponError.value = true
  } finally {
    isCheckingCoupon.value = false
  }
}

function removeCoupon() {
  appliedCoupon.value = null
  couponMessage.value = ''
  emit('update:coupon', null)
}
</script>
