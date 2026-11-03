<template>
  <div class="space-y-4">
    <!-- Filter Bar -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-2 flex items-center gap-1.5 overflow-x-auto scrollbar-none text-xs font-bold shadow-sm">
      <button
        v-for="status in orderStatuses"
        :key="status.key"
        type="button"
        @click="currentFilter = status.key"
        :class="[
          'px-3.5 py-2 rounded-xl whitespace-nowrap transition-colors shrink-0',
          currentFilter === status.key
            ? 'bg-primary text-white shadow-sm'
            : 'text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface'
        ]"
      >
        {{ status.label }}
        <span
          class="ml-1.5 px-1.5 py-0.2 rounded-full text-[10px]"
          :class="currentFilter === status.key ? 'bg-white/20 text-white' : 'bg-surface-container text-outline'"
        >
          {{ getOrderCount(status.key) }}
        </span>
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="space-y-4">
      <div v-for="i in 3" :key="i" class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 p-6 animate-pulse space-y-4">
        <div class="h-5 bg-surface-container rounded-lg w-1/3"></div>
        <div class="h-16 bg-surface-container rounded-2xl"></div>
        <div class="h-8 bg-surface-container rounded-xl w-1/4 ml-auto"></div>
      </div>
    </div>

    <!-- Empty Orders State -->
    <div
      v-else-if="filteredOrders.length === 0"
      class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 p-8 shadow-sm"
    >
      <EmptyState
        icon="inventory_2"
        title="Chưa có đơn hàng nào"
        description="Bạn chưa có đơn hàng nào trong trạng thái này. Khám phá các siêu phẩm công nghệ chính hãng tại TechShop ngay!"
        action-text="Khám phá sản phẩm"
        action-icon="storefront"
        @action="$router.push('/products')"
      />
    </div>

    <!-- Order Cards List -->
    <div v-else class="space-y-4">
      <div
        v-for="order in filteredOrders"
        :key="order.id"
        v-memo="[order.id, order.status, order.final_total]"
        class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 overflow-hidden shadow-sm transition-all hover:border-outline-variant/60"
      >
        <!-- Order Card Header -->
        <div class="p-4 md:p-5 border-b border-outline-variant/20 flex flex-wrap items-center justify-between gap-3 bg-surface-container-low/30">
          <div class="flex items-center gap-3">
            <span class="font-black text-sm text-primary tracking-wider">{{ order.order_code }}</span>
            <span class="text-outline text-xs">• {{ order.created_at }}</span>
          </div>
          <div>
            <StatusBadge :status="order.status" :label="getStatusText(order.status)" />
          </div>
        </div>

        <!-- Order Items Body -->
        <div class="p-4 md:p-5 divide-y divide-outline-variant/15">
          <div
            v-for="item in order.items"
            :key="item.id"
            class="py-3 first:pt-0 last:pb-0 flex items-center gap-4"
          >
            <img
              :src="item.product?.image || 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300'"
              :alt="item.product?.name"
              loading="lazy"
              decoding="async"
              class="w-16 h-16 object-cover rounded-xl border border-outline-variant/30 shrink-0 bg-surface-container"
            />
            <div class="flex-1 min-w-0">
              <h4 class="text-xs md:text-sm font-bold text-on-surface truncate">
                {{ item.product?.name }}
              </h4>
              <p v-if="item.variant?.sku" class="text-[11px] text-outline mt-0.5">
                Phiên bản: {{ item.variant.sku }}
              </p>
              <div class="text-xs text-on-surface-variant mt-1">
                Số lượng: <span class="font-bold text-on-surface">x{{ item.quantity }}</span>
              </div>
            </div>
            <div class="text-right shrink-0">
              <div class="text-xs md:text-sm font-bold text-primary">
                {{ formatPrice(item.price) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Order Card Footer -->
        <div class="p-4 md:p-5 border-t border-outline-variant/20 flex flex-col sm:flex-row items-center justify-between gap-3 bg-surface-container-low/20">
          <div class="text-xs text-on-surface-variant w-full sm:w-auto text-left">
            Tổng thanh toán:
            <span class="text-base md:text-lg font-black text-primary ml-1">
              {{ formatPrice(order.final_total) }}
            </span>
            <span v-if="order.discount_amount > 0" class="text-[11px] text-emerald-600 ml-1 font-semibold">
              (Đã giảm {{ formatPrice(order.discount_amount) }})
            </span>
          </div>

          <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <button
              v-if="order.status === 'pending'"
              type="button"
              @click="$emit('cancel', order.id)"
              class="px-4 py-2 text-xs font-bold text-error border border-error/30 hover:bg-error/10 rounded-xl transition-colors"
            >
              Hủy đơn hàng
            </button>
            <button
              type="button"
              @click="$emit('view-detail', order)"
              class="px-4 py-2 text-xs font-bold text-white bg-primary hover:bg-primary-hover rounded-xl shadow-sm transition-colors"
            >
              Xem chi tiết
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import type { CustomerOrder } from '../services/accountService'
import { useFormatters } from '@/core/composables/useFormatters'
import StatusBadge from '@/core/components/ui/StatusBadge.vue'
import EmptyState from '@/core/components/ui/EmptyState.vue'

interface Props {
  orders: CustomerOrder[]
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
})

defineEmits<{
  (e: 'view-detail', order: CustomerOrder): void
  (e: 'cancel', orderId: number): void
}>()

const router = useRouter()
const { formatPrice } = useFormatters()

const currentFilter = ref<string>('all')

const orderStatuses = [
  { key: 'all', label: 'Tất cả' },
  { key: 'pending', label: 'Chờ duyệt' },
  { key: 'processing', label: 'Đang chuẩn bị' },
  { key: 'shipping', label: 'Đang giao' },
  { key: 'delivered', label: 'Đã giao' },
  { key: 'cancelled', label: 'Đã hủy' },
]

function getOrderCount(statusKey: string): number {
  if (statusKey === 'all') return props.orders.length
  return props.orders.filter((o) => o.status === statusKey).length
}

const filteredOrders = computed(() => {
  if (currentFilter.value === 'all') return props.orders
  return props.orders.filter((o) => o.status === currentFilter.value)
})

function getStatusText(status: string): string {
  const map: Record<string, string> = {
    pending: 'Chờ duyệt',
    processing: 'Đang xử lý',
    shipping: 'Đang vận chuyển',
    delivered: 'Giao thành công',
    cancelled: 'Đã hủy',
  }
  return map[status] || status
}
</script>

