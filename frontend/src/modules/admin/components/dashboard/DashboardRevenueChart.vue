<template>
  <section class="grid grid-cols-1 lg:grid-cols-12 gap-5">
    <!-- Revenue Chart (8 cols on lg) -->
    <div class="lg:col-span-8 bg-surface-container-lowest p-5 rounded-2xl shadow-xs border border-outline-variant/30 flex flex-col justify-between">
      <div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
          <div>
            <h4 class="text-base sm:text-lg font-black text-on-surface">Biểu đồ doanh thu</h4>
            <p class="text-xs text-on-surface-variant">Thống kê theo trục thời gian được lọc</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-[11px] font-semibold text-outline bg-surface-container px-2.5 py-1 rounded-lg">
              Đơn vị: VNĐ
            </span>
          </div>
        </div>

        <!-- Empty Chart State -->
        <div
          v-if="!revenueChart || revenueChart.length === 0"
          class="h-64 flex flex-col items-center justify-center text-outline text-xs"
        >
          <span class="material-symbols-outlined text-3xl mb-1">bar_chart</span>
          <span>Chưa có phát sinh doanh thu trong khoảng thời gian này</span>
        </div>

        <!-- Dynamic Bar Chart -->
        <div v-else class="h-64 w-full flex items-end justify-between px-2 gap-2 border-b border-outline-variant/30 pb-3">
          <div
            v-for="item in revenueChart"
            :key="item.month"
            class="flex flex-col items-center flex-1 gap-2 h-full justify-end group cursor-pointer relative"
          >
            <!-- Tooltip on hover -->
            <div class="absolute -top-10 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 bg-black/85 text-white px-2 py-1 rounded-md text-[10px] whitespace-nowrap shadow-md">
              <div>{{ formatPrice(Number(item.revenue)) }}</div>
              <div class="text-white/80 font-normal">{{ item.order_count }} đơn hàng</div>
            </div>

            <!-- Bar -->
            <div
              class="w-full max-w-[36px] bg-primary/30 group-hover:bg-primary rounded-t-lg transition-all duration-300"
              :style="{ height: getBarHeight(Number(item.revenue)) }"
            ></div>

            <!-- Label -->
            <span class="text-[11px] text-on-surface-variant font-medium truncate max-w-[48px]" :title="item.month">
              {{ item.month }}
            </span>
          </div>
        </div>
      </div>

      <div class="pt-4 flex items-center justify-between text-xs text-on-surface-variant border-t border-outline-variant/20 mt-4">
        <span class="italic">* Dữ liệu loại trừ các đơn hàng đã bị hủy</span>
        <RouterLink to="/admin/orders" class="text-primary font-bold hover:underline flex items-center gap-0.5">
          <span>Chi tiết danh sách đơn</span>
          <span class="material-symbols-outlined text-sm">arrow_forward</span>
        </RouterLink>
      </div>
    </div>

    <!-- Order Status Pipeline (4 cols on lg) -->
    <div class="lg:col-span-4 bg-surface-container-lowest p-5 rounded-2xl shadow-xs border border-outline-variant/30 flex flex-col justify-between">
      <div>
        <h4 class="text-base sm:text-lg font-black text-on-surface mb-1">Tiến trình vận đơn</h4>
        <p class="text-xs text-on-surface-variant mb-4">Trạng thái các đơn hàng toàn sàn</p>

        <div class="space-y-2.5">
          <div class="flex items-center justify-between p-2.5 bg-amber-500/5 rounded-xl border border-amber-500/20">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
              <span class="text-xs font-semibold text-on-surface">Chờ xác nhận</span>
            </div>
            <span class="text-xs font-black text-amber-700 dark:text-amber-300">{{ summary.pending_orders }}</span>
          </div>

          <div class="flex items-center justify-between p-2.5 bg-blue-500/5 rounded-xl border border-blue-500/20">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
              <span class="text-xs font-semibold text-on-surface">Đang đóng gói</span>
            </div>
            <span class="text-xs font-black text-blue-700 dark:text-blue-300">{{ summary.processing_orders }}</span>
          </div>

          <div class="flex items-center justify-between p-2.5 bg-indigo-500/5 rounded-xl border border-indigo-500/20">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
              <span class="text-xs font-semibold text-on-surface">Đang giao hàng</span>
            </div>
            <span class="text-xs font-black text-indigo-700 dark:text-indigo-300">{{ summary.shipped_orders }}</span>
          </div>

          <div class="flex items-center justify-between p-2.5 bg-emerald-500/5 rounded-xl border border-emerald-500/20">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
              <span class="text-xs font-semibold text-on-surface">Giao thành công</span>
            </div>
            <span class="text-xs font-black text-emerald-700 dark:text-emerald-300">{{ summary.delivered_orders }}</span>
          </div>

          <div class="flex items-center justify-between p-2.5 bg-rose-500/5 rounded-xl border border-rose-500/20">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
              <span class="text-xs font-semibold text-on-surface">Đã hủy đơn</span>
            </div>
            <span class="text-xs font-black text-rose-700 dark:text-rose-300">{{ summary.cancelled_orders }}</span>
          </div>
        </div>
      </div>

      <div class="pt-4 mt-2">
        <RouterLink
          to="/admin/orders?status=pending"
          class="w-full py-2.5 bg-primary text-on-primary font-bold text-xs rounded-xl shadow-xs hover:bg-primary-hover transition-all flex items-center justify-center gap-1.5"
        >
          <span class="material-symbols-outlined text-sm">checklist</span>
          <span>Xử lý {{ summary.pending_orders }} đơn cần duyệt</span>
        </RouterLink>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { useFormatters } from '@/core/composables/useFormatters'
import type { DashboardSummary } from './DashboardMetricsGrid.vue'

export interface RevenueChartItem {
  month: string
  revenue: number | string
  order_count: number
}

interface Props {
  revenueChart: RevenueChartItem[]
  summary: DashboardSummary
}

const props = defineProps<Props>()
const { formatPrice } = useFormatters()

const maxChartRevenue = computed(() => {
  if (!props.revenueChart || props.revenueChart.length === 0) return 1
  const max = Math.max(...props.revenueChart.map((i) => Number(i.revenue)))
  return max > 0 ? max : 1
})

function getBarHeight(revenue: number): string {
  const percent = Math.round((revenue / maxChartRevenue.value) * 100)
  return `${Math.max(8, percent)}%`
}
</script>

