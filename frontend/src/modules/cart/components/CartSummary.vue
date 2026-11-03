<template>
  <aside class="sticky top-24">
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-outline-variant/30 flex flex-col gap-6">
      <h2 class="text-lg font-black text-on-surface pb-3 border-b border-outline-variant/20">
        Tóm tắt đơn hàng
      </h2>

      <div class="space-y-3 text-sm">
        <div class="flex justify-between text-on-surface-variant">
          <span>Tạm tính</span>
          <span class="text-on-surface font-semibold">{{ formatPrice(subtotal) }}</span>
        </div>

        <div class="flex justify-between text-on-surface-variant">
          <span>Phí vận chuyển</span>
          <span v-if="shippingFee === 0" class="text-primary font-semibold">
            Miễn phí
          </span>
          <span v-else class="text-on-surface font-semibold">
            {{ formatPrice(shippingFee) }}
          </span>
        </div>

        <p v-if="shippingFee > 0 && subtotal < 500000" class="text-[11px] text-outline">
          Mua thêm <strong>{{ formatPrice(500000 - subtotal) }}</strong> để được miễn phí ship.
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
            @keyup.enter="handleApplyCoupon"
          />
          <button
            type="button"
            @click="handleApplyCoupon"
            class="bg-surface-container-high px-4 rounded-xl text-xs font-bold hover:bg-outline-variant transition-colors cursor-pointer"
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
            {{ formatPrice(totalAmount) }}
          </p>
          <p class="text-[10px] text-on-surface-variant mt-0.5">(Đã bao gồm thuế VAT)</p>
        </div>
      </div>

      <!-- Checkout CTA -->
      <button
        @click="$emit('checkout')"
        type="button"
        class="w-full bg-primary text-on-primary py-3.5 rounded-xl font-bold text-sm shadow-md hover:bg-primary-hover transition-all active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer"
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
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useFormatters } from '@/core/composables/useFormatters'

interface Props {
  subtotal: number
  shippingFee: number
  totalAmount: number
}

defineProps<Props>()
const emit = defineEmits<{
  (e: 'checkout'): void
  (e: 'apply-coupon', code: string): void
}>()

const { formatPrice } = useFormatters()
const couponCode = ref('')
const couponMessage = ref('')

function handleApplyCoupon() {
  const code = couponCode.value.trim().toUpperCase()
  if (code === 'TECHVINA10' || code === 'TECHVINA50K' || code === 'FREESHIP' || code === 'GAMINGVIP') {
    couponMessage.value = `Áp dụng mã ${code} thành công!`
    emit('apply-coupon', code)
  } else if (code) {
    couponMessage.value = 'Mã ưu đãi không hợp lệ hoặc đã hết hạn.'
  }
}
</script>

