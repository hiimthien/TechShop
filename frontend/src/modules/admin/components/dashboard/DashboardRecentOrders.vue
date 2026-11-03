<template>
  <section class="bg-surface-container-lowest rounded-2xl shadow-xs border border-outline-variant/30 overflow-hidden">
    <!-- Table Header & Status Filter Tabs -->
    <div class="p-5 border-b border-outline-variant/30 flex flex-col md:flex-row md:items-center justify-between gap-3">
      <div>
        <h4 class="text-base sm:text-lg font-black text-on-surface">Đơn hàng gần đây</h4>
        <p class="text-xs text-on-surface-variant">Lọc nhanh theo trạng thái vận đơn</p>
      </div>

      <!-- Order Status Tabs -->
      <div class="flex items-center gap-1 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
        <button
          v-for="st in statusTabs"
          :key="st.key"
          type="button"
          class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all shrink-0 cursor-pointer"
          :class="selectedOrderStatus === st.key ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high'"
          @click="$emit('select-status', st.key)"
        >
          {{ st.label }}
        </button>
      </div>
    </div>

    <!-- Orders Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-surface-container-low text-on-surface-variant border-b border-outline-variant/30 text-xs uppercase font-bold tracking-wider">
            <th class="py-3 px-4">Mã đơn</th>
            <th class="py-3 px-4">Khách hàng</th>
            <th class="py-3 px-4">Thời gian</th>
            <th class="py-3 px-4">Tổng tiền</th>
            <th class="py-3 px-4">Thanh toán</th>
            <th class="py-3 px-4">Trạng thái</th>
            <th class="py-3 px-4 text-right">Thao tác</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/20 text-xs">
          <tr v-if="orders.length === 0">
            <td colspan="7" class="py-8 text-center text-outline">
              Không có đơn hàng nào theo bộ lọc này
            </td>
          </tr>
          <tr
            v-for="order in orders"
            :key="order.id"
            class="hover:bg-surface-container-low transition-colors"
          >
            <td class="py-3 px-4 font-mono font-bold text-on-surface">
              #{{ order.id }}
            </td>
            <td class="py-3 px-4">
              <div class="font-bold text-on-surface">{{ order.user?.name || order.address?.name || 'Khách vãng lai' }}</div>
              <div class="text-[11px] text-outline">{{ order.address?.phone || order.user?.email || '-' }}</div>
            </td>
            <td class="py-3 px-4 text-outline whitespace-nowrap">
              {{ formatDate(order.created_at) }}
            </td>
            <td class="py-3 px-4 font-bold text-primary whitespace-nowrap">
              {{ formatPrice(Number(order.total_price)) }}
            </td>
            <td class="py-3 px-4">
              <span
                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                :class="order.payment_method === 'vnpay' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800'"
              >
                {{ order.payment_method?.toUpperCase() || 'COD' }}
              </span>
            </td>
            <td class="py-3 px-4">
              <StatusBadge :status="order.status" />
            </td>
            <td class="py-3 px-4 text-right whitespace-nowrap">
              <RouterLink
                :to="`/admin/orders?view=${order.id}`"
                class="text-primary hover:underline font-bold transition"
              >
                Xem chi tiết
              </RouterLink>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Table Footer Link -->
    <div class="p-4 bg-surface-container-low border-t border-outline-variant/30 text-center">
      <RouterLink
        to="/admin/orders"
        class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1"
      >
        <span>Xem toàn bộ danh sách đơn hàng</span>
        <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
      </RouterLink>
    </div>
  </section>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { useFormatters } from '@/core/composables/useFormatters'
import StatusBadge from '@/core/components/ui/StatusBadge.vue'
import type { Order } from '../../services/adminService'

interface StatusTab {
  key: string
  label: string
}

interface Props {
  orders: Order[]
  selectedOrderStatus: string
  statusTabs: StatusTab[]
}

defineProps<Props>()
defineEmits<{
  (e: 'select-status', key: string): void
}>()

const { formatPrice, formatDate } = useFormatters()
</script>

