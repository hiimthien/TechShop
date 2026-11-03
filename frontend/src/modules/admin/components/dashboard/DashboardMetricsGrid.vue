<template>
  <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card: Doanh thu trong kỳ -->
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-xs border border-outline-variant/30 hover:-translate-y-1 transition-all duration-300">
      <div class="flex justify-between items-start mb-2">
        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
          <span class="material-symbols-outlined text-xl">payments</span>
        </div>
        <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full flex items-center gap-1">
          <span class="material-symbols-outlined text-xs">trending_up</span>
          Trong kỳ
        </span>
      </div>
      <p class="text-xs text-on-surface-variant font-medium">Doanh thu kỳ này</p>
      <h3 class="text-xl sm:text-2xl font-black text-on-surface mt-1 truncate" :title="formatPrice(summary.total_revenue)">
        {{ formatPrice(summary.total_revenue) }}
      </h3>
      <p class="text-[11px] text-outline mt-1.5 flex items-center justify-between">
        <span>TB / Đơn (AOV):</span>
        <strong class="text-on-surface font-semibold">{{ formatPrice(summary.average_order_value || 0) }}</strong>
      </p>
    </div>

    <!-- Card: Đơn hàng trong kỳ -->
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-xs border border-outline-variant/30 hover:-translate-y-1 transition-all duration-300">
      <div class="flex justify-between items-start mb-2">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
          <span class="material-symbols-outlined text-xl">shopping_cart</span>
        </div>
        <span class="text-[11px] font-bold text-amber-700 dark:text-amber-300 bg-amber-500/10 px-2 py-0.5 rounded-full">
          {{ summary.pending_orders }} chờ duyệt
        </span>
      </div>
      <p class="text-xs text-on-surface-variant font-medium">Đơn hàng kỳ này</p>
      <h3 class="text-xl sm:text-2xl font-black text-on-surface mt-1">
        {{ summary.total_orders }}
      </h3>
      <p class="text-[11px] text-outline mt-1.5 flex items-center justify-between">
        <span>Giao thành công:</span>
        <strong class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ summary.delivered_orders }} đơn</strong>
      </p>
    </div>

    <!-- Card: Khách hàng thành viên -->
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-xs border border-outline-variant/30 hover:-translate-y-1 transition-all duration-300">
      <div class="flex justify-between items-start mb-2">
        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
          <span class="material-symbols-outlined text-xl">group</span>
        </div>
        <span class="text-[11px] font-bold text-blue-700 dark:text-blue-300 bg-blue-500/10 px-2 py-0.5 rounded-full">
          {{ summary.total_products }} sản phẩm
        </span>
      </div>
      <p class="text-xs text-on-surface-variant font-medium">Khách hàng thành viên</p>
      <h3 class="text-xl sm:text-2xl font-black text-on-surface mt-1">
        {{ summary.total_customers }}
      </h3>
      <p class="text-[11px] text-outline mt-1.5 flex items-center justify-between">
        <span>Mã giảm giá khả dụng:</span>
        <strong class="text-primary font-semibold">{{ summary.total_coupons }} voucher</strong>
      </p>
    </div>

    <!-- Card: Phiếu bảo hành RMA -->
    <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-xs border border-outline-variant/30 hover:-translate-y-1 transition-all duration-300">
      <div class="flex justify-between items-start mb-2">
        <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center">
          <span class="material-symbols-outlined text-xl">verified_user</span>
        </div>
        <span class="text-[11px] font-bold text-rose-700 dark:text-rose-300 bg-rose-500/10 px-2 py-0.5 rounded-full">
          {{ summary.pending_warranties }} cần duyệt
        </span>
      </div>
      <p class="text-xs text-on-surface-variant font-medium">Phiếu bảo hành RMA</p>
      <h3 class="text-xl sm:text-2xl font-black text-on-surface mt-1">
        {{ summary.total_warranties }}
      </h3>
      <p class="text-[11px] text-outline mt-1.5 flex items-center justify-between">
        <span>Xử lý khiếu nại:</span>
        <RouterLink to="/admin/warranties" class="text-primary hover:underline font-semibold">
          Xem ngay &rarr;
        </RouterLink>
      </p>
    </div>
  </section>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { useFormatters } from '@/core/composables/useFormatters'

export interface DashboardSummary {
  total_revenue: number
  total_orders: number
  total_customers: number
  total_products: number
  total_coupons: number
  pending_orders: number
  processing_orders: number
  shipped_orders: number
  delivered_orders: number
  cancelled_orders: number
  pending_warranties: number
  total_warranties: number
  average_order_value?: number
  all_time_revenue?: number
  all_time_orders?: number
}

interface Props {
  summary: DashboardSummary
}

defineProps<Props>()

const { formatPrice } = useFormatters()
</script>

