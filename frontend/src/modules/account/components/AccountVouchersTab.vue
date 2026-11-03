<template>
  <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 p-6 md:p-8 space-y-6 shadow-sm">
    <div class="flex items-center justify-between border-b border-outline-variant/20 pb-4">
      <div>
        <h3 class="text-base md:text-lg font-black text-on-surface">Kho Voucher của bạn</h3>
        <p class="text-xs text-on-surface-variant">Lưu và sao chép mã khuyến mãi để áp dụng trực tiếp tại bước thanh toán.</p>
      </div>
    </div>

    <!-- Loading Skeletons -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div v-for="i in 4" :key="i" class="h-32 bg-surface-container rounded-2xl animate-pulse"></div>
    </div>

    <!-- Voucher Grid -->
    <div v-else-if="coupons.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div
        v-for="coupon in coupons"
        :key="coupon.id"
        class="relative flex rounded-2xl border border-primary/20 bg-gradient-to-r from-primary/5 via-surface-container-lowest to-surface-container-lowest overflow-hidden shadow-sm hover:shadow-md transition-shadow"
      >
        <!-- Left Badge -->
        <div class="w-24 bg-gradient-to-br from-primary to-primary-container text-white p-3 flex flex-col items-center justify-center text-center shrink-0">
          <span class="material-symbols-outlined text-[28px] mb-1">redeem</span>
          <span class="text-xs font-black uppercase tracking-wider">
            {{ coupon.type === 'percentage' ? `${coupon.value}%` : formatCurrencyShort(coupon.value) }}
          </span>
          <span class="text-[9px] uppercase tracking-wider opacity-80">GIẢM GIÁ</span>
        </div>

        <!-- Right Details -->
        <div class="p-3.5 flex-1 flex flex-col justify-between text-xs space-y-2">
          <div>
            <div class="flex items-center justify-between">
              <span class="font-black text-sm text-primary tracking-wider font-mono">
                {{ coupon.code }}
              </span>
              <button
                type="button"
                @click="copyCode(coupon.code)"
                class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition-all"
                :class="copiedCode === coupon.code ? 'bg-emerald-500 text-white' : 'bg-primary/10 text-primary hover:bg-primary/20'"
              >
                {{ copiedCode === coupon.code ? 'Đã sao chép!' : 'Sao chép' }}
              </button>
            </div>
            <p class="text-on-surface-variant text-[11px] mt-1">
              {{ coupon.type === 'percentage' ? `Giảm ${coupon.value}% tối đa ${formatPrice(coupon.max_discount || 500000)}` : `Giảm ngay ${formatPrice(coupon.value)}` }}
              {{ coupon.min_order_value ? `cho đơn từ ${formatPrice(coupon.min_order_value)}` : '' }}
            </p>
          </div>

          <div class="flex items-center justify-between text-[10px] text-outline border-t border-outline-variant/20 pt-2">
            <span>HSD: {{ coupon.expires_at || 'Vô thời hạn' }}</span>
            <span v-if="coupon.usage_limit" class="text-primary font-semibold">
              Còn {{ Math.max(0, coupon.usage_limit - coupon.used_count) }} lượt
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty Vouchers -->
    <div v-else>
      <EmptyState
        icon="confirmation_number"
        title="Chưa có mã giảm giá nào"
        description="Hiện tại chưa có mã giảm giá khả dụng. Hãy theo dõi các chương trình khuyến mãi sắp tới của TechShop nhé!"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import type { CouponItem } from '../services/accountService'
import { useFormatters } from '@/core/composables/useFormatters'
import EmptyState from '@/core/components/ui/EmptyState.vue'

interface Props {
  coupons: CouponItem[]
  loading?: boolean
}

withDefaults(defineProps<Props>(), {
  loading: false,
})

const { formatPrice } = useFormatters()
const copiedCode = ref<string | null>(null)

function formatCurrencyShort(val: number): string {
  if (val >= 1000) {
    return `${val / 1000}K`
  }
  return `${val}đ`
}

function copyCode(code: string) {
  navigator.clipboard.writeText(code)
  copiedCode.value = code
  setTimeout(() => {
    copiedCode.value = null
  }, 2000)
}
</script>

