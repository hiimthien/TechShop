<template>
  <div class="space-y-lg max-w-container-max mx-auto">
    <!-- Header Section -->
    <header class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-sm mb-md">
      <div>
        <h1 class="text-headline-lg font-headline-lg font-bold text-on-surface tracking-tight">Quản lý đơn hàng</h1>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Theo dõi và xử lý các đơn đặt hàng từ khách hàng.</p>
      </div>
      <div>
        <button
          @click="fetchOrders(1)"
          :disabled="loading"
          class="bg-surface-container hover:bg-surface-container-high text-on-surface px-lg py-md rounded-lg flex items-center gap-xs transition-all shadow-sm text-label-md font-label-md border border-outline-variant/30"
        >
          <span class="material-symbols-outlined text-[18px]" :class="{ 'animate-spin': loading }">refresh</span>
          Làm mới
        </button>
      </div>
    </header>

    <!-- Filter Bar Section -->
    <section class="bg-surface p-lg rounded-xl shadow-sm border border-outline-variant/30">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
        <!-- Search -->
        <div class="relative">
          <label class="text-label-sm font-label-sm text-on-surface-variant mb-2 block font-medium">Tìm kiếm đơn hàng</label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
            <input
              v-model="searchQuery"
              @keyup.enter="fetchOrders(1)"
              class="w-full pl-xl pr-md py-md bg-surface-container-low border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-body-sm"
              placeholder="Mã đơn, tên khách, SĐT..."
              type="text"
            />
          </div>
        </div>

        <!-- Order Status -->
        <div>
          <label class="text-label-sm font-label-sm text-on-surface-variant mb-2 block font-medium">Trạng thái đơn hàng</label>
          <select
            v-model="currentStatus"
            @change="fetchOrders(1)"
            class="w-full px-md py-md bg-surface-container-low border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 text-body-sm cursor-pointer"
          >
            <option value="all">Tất cả trạng thái</option>
            <option value="pending">Chờ xác nhận (Pending)</option>
            <option value="processing">Đang đóng gói (Processing)</option>
            <option value="shipped">Đang giao hàng (Shipped)</option>
            <option value="delivered">Đã giao thành công (Delivered)</option>
            <option value="cancelled">Đã hủy đơn (Cancelled)</option>
          </select>
        </div>

        <!-- Quick Reset Filter -->
        <div class="flex items-end">
          <button
            @click="resetFilters"
            class="w-full py-md bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md rounded-lg transition-all flex items-center justify-center gap-xs border border-outline-variant/40"
          >
            <span class="material-symbols-outlined text-[18px]">restart_alt</span>
            Đặt lại bộ lọc
          </button>
        </div>
      </div>
    </section>

    <!-- AdminTable Section -->
    <section class="bg-surface rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
      <div v-if="loading" class="p-16 text-center text-on-surface-variant">
        <div class="inline-block animate-spin w-8 h-8 border-4 border-primary border-t-transparent rounded-full mb-3"></div>
        <div class="text-body-sm font-medium">Đang tải danh sách đơn hàng...</div>
      </div>

      <div v-else-if="!orders.length" class="p-16 text-center text-on-surface-variant">
        <span class="material-symbols-outlined text-5xl text-outline mb-2">shopping_bag</span>
        <div class="text-headline-sm font-bold text-on-surface">Không tìm thấy đơn hàng nào</div>
        <p class="text-body-sm text-on-surface-variant mt-1">Hãy thử tìm với từ khóa khác hoặc điều chỉnh bộ lọc</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant/50 text-xs font-bold uppercase tracking-wider text-on-surface-variant">
              <th class="py-3.5 px-4 whitespace-nowrap">Mã đơn</th>
              <th class="py-3.5 px-4 whitespace-nowrap">Khách hàng</th>
              <th class="py-3.5 px-4 whitespace-nowrap">Số điện thoại</th>
              <th class="py-3.5 px-4 whitespace-nowrap">Tổng tiền</th>
              <th class="py-3.5 px-4 whitespace-nowrap text-center">Trạng thái</th>
              <th class="py-3.5 px-4 whitespace-nowrap text-right pr-6">Hành động</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30">
            <tr
              v-for="order in orders"
              :key="order.id"
              class="hover:bg-surface-container-low transition-colors"
            >
              <td class="py-3.5 px-4 whitespace-nowrap">
                <span class="font-bold text-primary font-mono text-sm block whitespace-nowrap">
                  #ORD-{{ order.id }}
                </span>
                <span class="text-[11px] text-on-surface-variant block">
                  {{ formatDate(order.created_at) }}
                </span>
                <span v-if="order.ghn_order_code" class="text-xs text-on-surface-variant font-mono block">
                  {{ order.ghn_order_code }}
                </span>
              </td>
              <td class="py-3.5 px-4 whitespace-nowrap">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold text-xs shrink-0">
                    {{ order.user?.name?.charAt(0) || order.address?.name?.charAt(0) || 'K' }}
                  </div>
                  <div>
                    <span class="text-sm font-semibold text-on-surface block">{{ order.user?.name || order.address?.name || 'Khách vãng lai' }}</span>
                    <span class="text-xs text-on-surface-variant block">{{ order.user?.email || '-' }}</span>
                  </div>
                </div>
              </td>
              <td class="py-3.5 px-4 text-sm text-on-surface-variant whitespace-nowrap font-mono">
                {{ order.address?.phone || order.user?.phone || 'Chưa có' }}
              </td>
              <td class="py-3.5 px-4 text-sm font-bold text-on-surface whitespace-nowrap">
                {{ formatPrice(Number(order.total_price)) }}
              </td>
              <td class="py-3.5 px-4 whitespace-nowrap text-center">
                <span
                  class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold"
                  :class="getStatusBadgeClass(order.status)"
                >
                  {{ getStatusText(order.status) }}
                </span>
              </td>
              <td class="py-3.5 px-4 text-right whitespace-nowrap pr-6">
                <button
                  @click="openDetail(order)"
                  class="bg-surface-container hover:bg-primary hover:text-on-primary text-on-surface px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-xs border border-outline-variant/40"
                >
                  Chi tiết & Xử lý
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total > pagination.per_page" class="p-lg border-t border-outline-variant/30 flex items-center justify-between text-body-sm text-on-surface-variant">
        <div>
          Trang {{ pagination.current_page }} / {{ pagination.last_page }} (Tổng {{ pagination.total }} đơn hàng)
        </div>
        <div class="flex items-center gap-sm">
          <button
            :disabled="pagination.current_page <= 1"
            @click="fetchOrders(pagination.current_page - 1)"
            class="px-md py-xs rounded-lg bg-surface-container hover:bg-surface-container-high disabled:opacity-40 disabled:cursor-not-allowed text-on-surface font-bold text-label-md transition"
          >
            Trang trước
          </button>
          <button
            :disabled="pagination.current_page >= pagination.last_page"
            @click="fetchOrders(pagination.current_page + 1)"
            class="px-md py-xs rounded-lg bg-surface-container hover:bg-surface-container-high disabled:opacity-40 disabled:cursor-not-allowed text-on-surface font-bold text-label-md transition"
          >
            Trang sau
          </button>
        </div>
      </div>
    </section>

    <!-- Order Detail & Status Modal Component -->
    <OrderDetailModal
      v-model="showDetailModal"
      :order="selectedOrder"
      @status-updated="onStatusUpdated"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { adminService, type Order } from '@/modules/admin/services/adminService'
import OrderDetailModal from '../components/OrderDetailModal.vue'

const route = useRoute()
const loading = ref(true)
const orders = ref<Order[]>([])
const currentStatus = ref('all')
const searchQuery = ref('')

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 15,
})

const selectedOrder = ref<Order | null>(null)
const showDetailModal = ref(false)

async function fetchOrders(page = 1) {
  try {
    loading.value = true
    const res = (await adminService.getOrders({
      page,
      status: currentStatus.value,
      search: searchQuery.value,
      per_page: 15,
    })) as { data: Order[]; current_page: number; last_page: number; total: number; per_page: number }

    orders.value = res.data
    pagination.value = {
      current_page: res.current_page,
      last_page: res.last_page,
      total: res.total,
      per_page: res.per_page,
    }

    if (route.query.view) {
      const match = orders.value.find((o) => o.id === Number(route.query.view))
      if (match) openDetail(match)
    }
  } catch (err) {
    console.error('Lỗi khi tải đơn hàng:', err)
  } finally {
    loading.value = false
  }
}

function resetFilters() {
  currentStatus.value = 'all'
  searchQuery.value = ''
  fetchOrders(1)
}

function openDetail(order: Order) {
  selectedOrder.value = order
  showDetailModal.value = true
}

function onStatusUpdated() {
  fetchOrders(pagination.value.current_page)
}

function formatPrice(value: number): string {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(value)
}

function formatDate(dateStr: string): string {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

function getStatusBadgeClass(status: string): string {
  switch (status) {
    case 'pending':
      return 'bg-amber-100 text-amber-800'
    case 'processing':
      return 'bg-blue-100 text-blue-800'
    case 'shipped':
      return 'bg-indigo-100 text-indigo-800'
    case 'delivered':
      return 'bg-emerald-100 text-emerald-800'
    case 'cancelled':
      return 'bg-rose-100 text-rose-800'
    default:
      return 'bg-surface-container text-on-surface-variant'
  }
}

function getStatusText(status: string): string {
  switch (status) {
    case 'pending':
      return 'Chờ xác nhận'
    case 'processing':
      return 'Đang đóng gói'
    case 'shipped':
      return 'Đang giao hàng'
    case 'delivered':
      return 'Đã hoàn thành'
    case 'cancelled':
      return 'Đã hủy'
    default:
      return status
  }
}

onMounted(() => {
  if (route.query.status) {
    currentStatus.value = String(route.query.status)
  }
  fetchOrders(1)
})
</script>
