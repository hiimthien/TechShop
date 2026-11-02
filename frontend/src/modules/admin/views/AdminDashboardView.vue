<template>
  <div class="space-y-lg max-w-container-max mx-auto">
    <!-- Top Action bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-sm">
      <div>
        <h2 class="text-headline-lg font-headline-lg font-bold text-on-surface">Tổng quan hệ thống</h2>
        <p class="text-body-sm text-on-surface-variant">Báo cáo hiệu suất kinh doanh, đơn hàng và dịch vụ bảo hành.</p>
      </div>
      <div>
        <button
          @click="fetchData"
          :disabled="loading"
          class="bg-surface-container hover:bg-surface-container-high text-on-surface px-md py-xs rounded-lg flex items-center gap-xs transition-all shadow-sm text-label-md font-label-md border border-outline-variant/30"
        >
          <span class="material-symbols-outlined text-[18px]" :class="{ 'animate-spin': loading }">refresh</span>
          Làm mới
        </button>
      </div>
    </div>

    <!-- Skeleton Loading -->
    <div v-if="loading && !dashboardData" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-lg">
      <div v-for="i in 4" :key="i" class="h-32 bg-surface-container-lowest rounded-xl border border-outline-variant/20 animate-pulse"></div>
    </div>

    <!-- Metric Cards -->
    <section v-else-if="dashboardData" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-lg">
      <!-- Card: Tổng doanh thu -->
      <div class="bg-surface-container-lowest p-lg rounded-xl shadow-[0px_4px_12px_rgba(0,0,0,0.08)] border border-outline-variant/20 hover:translate-y-[-4px] transition-transform duration-300">
        <div class="flex justify-between items-start mb-sm">
          <div class="p-xs bg-primary-fixed text-primary rounded-lg">
            <span class="material-symbols-outlined">payments</span>
          </div>
          <span class="text-label-sm text-secondary font-bold flex items-center gap-xs">
            +12.5% <span class="material-symbols-outlined text-sm">trending_up</span>
          </span>
        </div>
        <p class="text-label-sm text-on-surface-variant mb-xs">Tổng doanh thu</p>
        <h3 class="text-headline-lg font-headline-lg text-on-surface">{{ formatPrice(dashboardData.summary.total_revenue) }}</h3>
      </div>

      <!-- Card: Đơn hàng mới -->
      <div class="bg-surface-container-lowest p-lg rounded-xl shadow-[0px_4px_12px_rgba(0,0,0,0.08)] border border-outline-variant/20 hover:translate-y-[-4px] transition-transform duration-300">
        <div class="flex justify-between items-start mb-sm">
          <div class="p-xs bg-tertiary-fixed text-tertiary rounded-lg">
            <span class="material-symbols-outlined">shopping_cart</span>
          </div>
          <span class="text-label-sm text-secondary font-bold flex items-center gap-xs">
            {{ dashboardData.summary.pending_orders }} chờ duyệt
          </span>
        </div>
        <p class="text-label-sm text-on-surface-variant mb-xs">Tổng đơn hàng</p>
        <h3 class="text-headline-lg font-headline-lg text-on-surface">{{ dashboardData.summary.total_orders }}</h3>
      </div>

      <!-- Card: Phiếu bảo hành -->
      <div class="bg-surface-container-lowest p-lg rounded-xl shadow-[0px_4px_12px_rgba(0,0,0,0.08)] border border-outline-variant/20 hover:translate-y-[-4px] transition-transform duration-300">
        <div class="flex justify-between items-start mb-sm">
          <div class="p-xs bg-error-container text-error rounded-lg">
            <span class="material-symbols-outlined">verified_user</span>
          </div>
          <span class="text-label-sm text-error font-bold">{{ dashboardData.summary.pending_warranties }} cần duyệt</span>
        </div>
        <p class="text-label-sm text-on-surface-variant mb-xs">Phiếu bảo hành RMA</p>
        <h3 class="text-headline-lg font-headline-lg text-on-surface">{{ dashboardData.summary.total_warranties }}</h3>
      </div>

      <!-- Card: Khách hàng mới -->
      <div class="bg-surface-container-lowest p-lg rounded-xl shadow-[0px_4px_12px_rgba(0,0,0,0.08)] border border-outline-variant/20 hover:translate-y-[-4px] transition-transform duration-300">
        <div class="flex justify-between items-start mb-sm">
          <div class="p-xs bg-secondary-fixed text-on-secondary-container rounded-lg">
            <span class="material-symbols-outlined">group</span>
          </div>
          <span class="text-label-sm text-secondary font-bold flex items-center gap-xs">
            {{ dashboardData.summary.total_coupons }} coupon
          </span>
        </div>
        <p class="text-label-sm text-on-surface-variant mb-xs">Khách hàng thành viên</p>
        <h3 class="text-headline-lg font-headline-lg text-on-surface">{{ dashboardData.summary.total_customers }}</h3>
      </div>
    </section>

    <!-- Middle: Charts and Status Breakdown -->
    <section v-if="dashboardData" class="grid grid-cols-1 lg:grid-cols-3 gap-lg">
      <!-- Revenue Chart -->
      <div class="lg:col-span-2 bg-surface-container-lowest p-lg rounded-xl shadow-[0px_4px_12px_rgba(0,0,0,0.08)] border border-outline-variant/20 flex flex-col justify-between">
        <div>
          <div class="flex justify-between items-center mb-xl">
            <div>
              <h4 class="text-headline-sm font-headline-sm text-on-surface font-bold">Biểu đồ doanh thu</h4>
              <p class="text-body-sm text-on-surface-variant">Thống kê theo các tháng gần nhất</p>
            </div>
            <span class="bg-surface-container text-primary font-bold text-label-sm rounded-lg py-xs px-sm">
              Đơn vị: VNĐ
            </span>
          </div>

          <div class="h-64 w-full flex items-end justify-between px-md gap-sm border-b border-outline-variant/20 pb-2">
            <div
              v-for="item in dashboardData.revenue_chart"
              :key="item.month"
              class="flex flex-col items-center flex-1 gap-sm h-full justify-end group cursor-pointer"
            >
              <div class="text-[11px] font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity">
                {{ formatPrice(Number(item.revenue)) }}
              </div>
              <div
                class="chart-bar w-full max-w-[40px] bg-primary-container rounded-t-lg transition-all duration-500 group-hover:bg-primary"
                :style="{ height: getBarHeight(Number(item.revenue)) }"
              ></div>
              <span class="text-label-sm text-on-surface-variant font-medium">{{ formatMonth(item.month) }}</span>
            </div>
          </div>
        </div>

        <div class="pt-md flex items-center justify-between text-label-sm text-on-surface-variant">
          <span>* Doanh thu thực nhận sau khi hoàn tất đơn hàng</span>
          <RouterLink to="/admin/orders" class="text-primary font-bold hover:underline">
            Xem chi tiết đơn &rarr;
          </RouterLink>
        </div>
      </div>

      <!-- Order Status Breakdown -->
      <div class="bg-surface-container-lowest p-lg rounded-xl shadow-[0px_4px_12px_rgba(0,0,0,0.08)] border border-outline-variant/20 flex flex-col justify-between">
        <div>
          <h4 class="text-headline-sm font-headline-sm text-on-surface font-bold mb-xs">Tiến trình vận đơn</h4>
          <p class="text-body-sm text-on-surface-variant mb-md">Phân bố đơn hàng theo trạng thái xử lý</p>

          <div class="space-y-sm">
            <div class="flex items-center justify-between p-sm bg-surface-container-low rounded-lg border border-outline-variant/30">
              <div class="flex items-center gap-sm">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                <span class="text-label-md font-medium text-on-surface">Chờ xác nhận</span>
              </div>
              <span class="font-bold text-on-surface">{{ dashboardData.summary.pending_orders }}</span>
            </div>

            <div class="flex items-center justify-between p-sm bg-surface-container-low rounded-lg border border-outline-variant/30">
              <div class="flex items-center gap-sm">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                <span class="text-label-md font-medium text-on-surface">Đang đóng gói</span>
              </div>
              <span class="font-bold text-on-surface">{{ dashboardData.summary.processing_orders }}</span>
            </div>

            <div class="flex items-center justify-between p-sm bg-surface-container-low rounded-lg border border-outline-variant/30">
              <div class="flex items-center gap-sm">
                <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                <span class="text-label-md font-medium text-on-surface">Đang giao vận</span>
              </div>
              <span class="font-bold text-on-surface">{{ dashboardData.summary.shipped_orders }}</span>
            </div>

            <div class="flex items-center justify-between p-sm bg-surface-container-low rounded-lg border border-outline-variant/30">
              <div class="flex items-center gap-sm">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span class="text-label-md font-medium text-on-surface">Đã giao thành công</span>
              </div>
              <span class="font-bold text-on-surface">{{ dashboardData.summary.delivered_orders }}</span>
            </div>

            <div class="flex items-center justify-between p-sm bg-surface-container-low rounded-lg border border-outline-variant/30">
              <div class="flex items-center gap-sm">
                <span class="w-3 h-3 rounded-full bg-error"></span>
                <span class="text-label-md font-medium text-on-surface">Đã hủy</span>
              </div>
              <span class="font-bold text-on-surface">{{ dashboardData.summary.cancelled_orders }}</span>
            </div>
          </div>
        </div>

        <div class="pt-md">
          <RouterLink
            to="/admin/orders?status=pending"
            class="w-full py-sm bg-primary text-on-primary font-label-md rounded-lg shadow-sm hover:opacity-90 transition-all flex items-center justify-center gap-xs"
          >
            <span class="material-symbols-outlined text-sm">checklist</span>
            Xử lý {{ dashboardData.summary.pending_orders }} đơn chờ duyệt
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- Bottom: Recent Orders Table -->
    <section v-if="dashboardData" class="bg-surface-container-lowest rounded-xl shadow-[0px_4px_12px_rgba(0,0,0,0.08)] border border-outline-variant/20 overflow-hidden">
      <div class="p-lg border-b border-outline-variant/30 flex justify-between items-center">
        <div>
          <h4 class="text-headline-sm font-headline-sm font-bold text-on-surface">Đơn hàng gần đây</h4>
          <p class="text-body-sm text-on-surface-variant">Các đơn đặt hàng vừa phát sinh trên hệ thống</p>
        </div>
        <RouterLink to="/admin/orders" class="text-label-md text-primary font-bold hover:underline transition-all">
          Xem tất cả đơn &rarr;
        </RouterLink>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low text-on-surface-variant border-b border-outline-variant/40">
              <th class="px-lg py-md text-label-md font-label-md">Mã đơn</th>
              <th class="px-lg py-md text-label-md font-label-md">Khách hàng</th>
              <th class="px-lg py-md text-label-md font-label-md">Ngày đặt</th>
              <th class="px-lg py-md text-label-md font-label-md">Tổng tiền</th>
              <th class="px-lg py-md text-label-md font-label-md">Trạng thái</th>
              <th class="px-lg py-md text-right text-label-md font-label-md">Thao tác</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/20">
            <tr
              v-for="order in dashboardData.recent_orders"
              :key="order.id"
              class="hover:bg-surface-container-low transition-colors group"
            >
              <td class="px-lg py-md text-body-sm font-bold text-primary font-mono">
                #ORD-{{ order.id }}
                <div v-if="order.ghn_order_code" class="text-[11px] text-on-surface-variant font-normal">
                  {{ order.ghn_order_code }}
                </div>
              </td>
              <td class="px-lg py-md">
                <div class="text-body-sm font-medium text-on-surface">{{ order.user?.name || order.address?.name || 'Khách vãng lai' }}</div>
                <div class="text-label-sm text-on-surface-variant">{{ order.user?.email || order.address?.phone }}</div>
              </td>
              <td class="px-lg py-md text-body-sm text-on-surface-variant">
                {{ formatDate(order.created_at) }}
              </td>
              <td class="px-lg py-md text-body-sm font-bold text-on-surface">
                {{ formatPrice(Number(order.total_price)) }}
              </td>
              <td class="px-lg py-md">
                <span
                  class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold"
                  :class="getStatusBadgeClass(order.status)"
                >
                  {{ getStatusText(order.status) }}
                </span>
              </td>
              <td class="px-lg py-md text-right">
                <RouterLink
                  :to="`/admin/orders?view=${order.id}`"
                  class="text-primary hover:text-primary-container text-label-md font-bold transition"
                >
                  Xem chi tiết
                </RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { adminService, type DashboardSummary, type RevenuePoint, type Order } from '@/modules/admin/services/adminService'

const loading = ref(true)
const dashboardData = ref<{
  summary: DashboardSummary
  revenue_chart: RevenuePoint[]
  recent_orders: Order[]
} | null>(null)

async function fetchData() {
  try {
    loading.value = true
    const res = await adminService.getDashboard()
    dashboardData.value = res
  } catch (err) {
    console.error('Lỗi tải dữ liệu dashboard admin:', err)
  } finally {
    loading.value = false
  }
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

function formatMonth(monthStr: string): string {
  const parts = monthStr.split('-')
  if (parts.length === 2 && parts[1]) {
    return `T${parseInt(parts[1], 10)}/${parts[0]}`
  }
  return monthStr
}

function getBarHeight(revenue: number): string {
  if (!dashboardData.value?.revenue_chart?.length) return '10%'
  const maxRevenue = Math.max(...dashboardData.value.revenue_chart.map((p) => Number(p.revenue)), 1)
  const percent = Math.max(12, Math.round((revenue / maxRevenue) * 100))
  return `${percent}%`
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
  fetchData()
})
</script>
