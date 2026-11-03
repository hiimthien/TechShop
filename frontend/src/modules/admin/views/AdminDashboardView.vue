<template>
  <div class="space-y-6 max-w-container-max mx-auto">
    <!-- 1. Top Bar: Title & Filter Toolbar -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/30 shadow-xs">
      <div>
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-primary text-2xl">analytics</span>
          <h2 class="text-xl sm:text-2xl font-black text-on-surface">Tổng quan hệ thống</h2>
        </div>
        <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5">Báo cáo hiệu suất kinh doanh, dòng tiền và tiến trình vận đơn.</p>
      </div>

      <!-- Filter Controls -->
      <div class="flex flex-wrap items-center gap-2">
        <!-- Quick Period Pills -->
        <div class="flex items-center gap-1 bg-surface-container-low p-1 rounded-xl border border-outline-variant/30">
          <button
            v-for="p in periodOptions"
            :key="p.key"
            type="button"
            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
            :class="selectedPeriod === p.key ? 'bg-primary text-on-primary shadow-xs' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container'"
            @click="selectPeriod(p.key)"
          >
            {{ p.label }}
          </button>
        </div>

        <!-- Custom Date Range Form (when 'custom' is active) -->
        <div v-if="selectedPeriod === 'custom'" class="flex items-center gap-1.5 bg-surface-container-low p-1 rounded-xl border border-outline-variant/30">
          <input
            v-model="customFromDate"
            type="date"
            class="bg-surface-container-lowest px-2 py-1 rounded-lg text-xs text-on-surface border border-outline-variant/40 focus:outline-none focus:border-primary"
            title="Từ ngày"
          />
          <span class="text-xs text-outline">-</span>
          <input
            v-model="customToDate"
            type="date"
            class="bg-surface-container-lowest px-2 py-1 rounded-lg text-xs text-on-surface border border-outline-variant/40 focus:outline-none focus:border-primary"
            title="Đến ngày"
          />
          <button
            type="button"
            class="px-2.5 py-1 bg-primary text-on-primary rounded-lg text-xs font-bold hover:bg-primary-container transition cursor-pointer"
            @click="applyCustomDate"
          >
            Lọc
          </button>
        </div>

        <!-- Refresh Button -->
        <button
          type="button"
          @click="fetchData"
          :disabled="loading"
          class="bg-surface-container hover:bg-surface-container-high text-on-surface px-3 py-1.5 rounded-xl flex items-center gap-1.5 transition-all shadow-xs text-xs font-bold border border-outline-variant/40 cursor-pointer"
        >
          <span class="material-symbols-outlined text-[17px]" :class="{ 'animate-spin': loading }">refresh</span>
          <span>Làm mới</span>
        </button>
      </div>
    </div>

    <!-- Active Filter Indicator -->
    <div class="flex items-center justify-between text-xs text-on-surface-variant px-1">
      <div class="flex items-center gap-1.5">
        <span class="material-symbols-outlined text-sm text-primary">calendar_today</span>
        <span>Khoảng thời gian đang hiển thị:</span>
        <strong class="text-on-surface font-bold">{{ activePeriodText }}</strong>
      </div>
      <div v-if="dashboardData?.summary?.all_time_revenue" class="hidden sm:block">
        <span>Toàn thời gian: </span>
        <strong class="text-primary font-bold">{{ formatPrice(dashboardData.summary.all_time_revenue) }}</strong>
        <span class="text-outline mx-1">•</span>
        <strong>{{ dashboardData.summary.all_time_orders || dashboardData.summary.total_orders }}</strong> đơn
      </div>
    </div>

    <!-- Skeleton Loading -->
    <div v-if="loading && !dashboardData" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="i in 4" :key="i" class="h-32 bg-surface-container-lowest rounded-2xl border border-outline-variant/20 animate-pulse"></div>
    </div>

    <!-- 2. Metric Cards -->
    <section v-else-if="dashboardData" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
        <h3 class="text-xl sm:text-2xl font-black text-on-surface mt-1 truncate" :title="formatPrice(dashboardData.summary.total_revenue)">
          {{ formatPrice(dashboardData.summary.total_revenue) }}
        </h3>
        <p class="text-[11px] text-outline mt-1.5 flex items-center justify-between">
          <span>TB / Đơn (AOV):</span>
          <strong class="text-on-surface font-semibold">{{ formatPrice(dashboardData.summary.average_order_value || 0) }}</strong>
        </p>
      </div>

      <!-- Card: Đơn hàng trong kỳ -->
      <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-xs border border-outline-variant/30 hover:-translate-y-1 transition-all duration-300">
        <div class="flex justify-between items-start mb-2">
          <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
            <span class="material-symbols-outlined text-xl">shopping_cart</span>
          </div>
          <span class="text-[11px] font-bold text-amber-700 dark:text-amber-300 bg-amber-500/10 px-2 py-0.5 rounded-full">
            {{ dashboardData.summary.pending_orders }} chờ duyệt
          </span>
        </div>
        <p class="text-xs text-on-surface-variant font-medium">Đơn hàng kỳ này</p>
        <h3 class="text-xl sm:text-2xl font-black text-on-surface mt-1">
          {{ dashboardData.summary.total_orders }}
        </h3>
        <p class="text-[11px] text-outline mt-1.5 flex items-center justify-between">
          <span>Giao thành công:</span>
          <strong class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ dashboardData.summary.delivered_orders }} đơn</strong>
        </p>
      </div>

      <!-- Card: Khách hàng thành viên -->
      <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-xs border border-outline-variant/30 hover:-translate-y-1 transition-all duration-300">
        <div class="flex justify-between items-start mb-2">
          <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
            <span class="material-symbols-outlined text-xl">group</span>
          </div>
          <span class="text-[11px] font-bold text-blue-700 dark:text-blue-300 bg-blue-500/10 px-2 py-0.5 rounded-full">
            {{ dashboardData.summary.total_products }} sản phẩm
          </span>
        </div>
        <p class="text-xs text-on-surface-variant font-medium">Khách hàng thành viên</p>
        <h3 class="text-xl sm:text-2xl font-black text-on-surface mt-1">
          {{ dashboardData.summary.total_customers }}
        </h3>
        <p class="text-[11px] text-outline mt-1.5 flex items-center justify-between">
          <span>Mã giảm giá khả dụng:</span>
          <strong class="text-primary font-semibold">{{ dashboardData.summary.total_coupons }} voucher</strong>
        </p>
      </div>

      <!-- Card: Phiếu bảo hành RMA -->
      <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-xs border border-outline-variant/30 hover:-translate-y-1 transition-all duration-300">
        <div class="flex justify-between items-start mb-2">
          <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center">
            <span class="material-symbols-outlined text-xl">verified_user</span>
          </div>
          <span class="text-[11px] font-bold text-rose-700 dark:text-rose-300 bg-rose-500/10 px-2 py-0.5 rounded-full">
            {{ dashboardData.summary.pending_warranties }} cần duyệt
          </span>
        </div>
        <p class="text-xs text-on-surface-variant font-medium">Phiếu bảo hành RMA</p>
        <h3 class="text-xl sm:text-2xl font-black text-on-surface mt-1">
          {{ dashboardData.summary.total_warranties }}
        </h3>
        <p class="text-[11px] text-outline mt-1.5 flex items-center justify-between">
          <span>Xử lý khiếu nại:</span>
          <RouterLink to="/admin/warranties" class="text-primary hover:underline font-semibold">
            Xem ngay &rarr;
          </RouterLink>
        </p>
      </div>
    </section>

    <!-- 3. Middle Section: Revenue Chart & Order Pipeline -->
    <section v-if="dashboardData" class="grid grid-cols-1 lg:grid-cols-12 gap-5">
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
          <div v-if="!dashboardData.revenue_chart || dashboardData.revenue_chart.length === 0" class="h-64 flex flex-col items-center justify-center text-outline text-xs">
            <span class="material-symbols-outlined text-3xl mb-1">bar_chart</span>
            <span>Chưa có phát sinh doanh thu trong khoảng thời gian này</span>
          </div>

          <!-- Dynamic Bar Chart -->
          <div v-else class="h-64 w-full flex items-end justify-between px-2 gap-2 border-b border-outline-variant/30 pb-3">
            <div
              v-for="item in dashboardData.revenue_chart"
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
              <span class="text-xs font-black text-amber-700 dark:text-amber-300">{{ dashboardData.summary.pending_orders }}</span>
            </div>

            <div class="flex items-center justify-between p-2.5 bg-blue-500/5 rounded-xl border border-blue-500/20">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                <span class="text-xs font-semibold text-on-surface">Đang đóng gói</span>
              </div>
              <span class="text-xs font-black text-blue-700 dark:text-blue-300">{{ dashboardData.summary.processing_orders }}</span>
            </div>

            <div class="flex items-center justify-between p-2.5 bg-indigo-500/5 rounded-xl border border-indigo-500/20">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                <span class="text-xs font-semibold text-on-surface">Đang giao hàng</span>
              </div>
              <span class="text-xs font-black text-indigo-700 dark:text-indigo-300">{{ dashboardData.summary.shipped_orders }}</span>
            </div>

            <div class="flex items-center justify-between p-2.5 bg-emerald-500/5 rounded-xl border border-emerald-500/20">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span class="text-xs font-semibold text-on-surface">Giao thành công</span>
              </div>
              <span class="text-xs font-black text-emerald-700 dark:text-emerald-300">{{ dashboardData.summary.delivered_orders }}</span>
            </div>

            <div class="flex items-center justify-between p-2.5 bg-rose-500/5 rounded-xl border border-rose-500/20">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                <span class="text-xs font-semibold text-on-surface">Đã hủy đơn</span>
              </div>
              <span class="text-xs font-black text-rose-700 dark:text-rose-300">{{ dashboardData.summary.cancelled_orders }}</span>
            </div>
          </div>
        </div>

        <div class="pt-4 mt-2">
          <RouterLink
            to="/admin/orders?status=pending"
            class="w-full py-2.5 bg-primary text-on-primary font-bold text-xs rounded-xl shadow-xs hover:bg-primary-container transition-all flex items-center justify-center gap-1.5"
          >
            <span class="material-symbols-outlined text-sm">checklist</span>
            <span>Xử lý {{ dashboardData.summary.pending_orders }} đơn cần duyệt</span>
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- 4. Bottom Section: Filtered Recent Orders Table -->
    <section v-if="dashboardData" class="bg-surface-container-lowest rounded-2xl shadow-xs border border-outline-variant/30 overflow-hidden">
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
            @click="selectOrderStatus(st.key)"
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
            <tr v-if="dashboardData.recent_orders.length === 0">
              <td colspan="7" class="py-8 text-center text-outline">
                Không có đơn hàng nào theo bộ lọc này
              </td>
            </tr>
            <tr
              v-for="order in dashboardData.recent_orders"
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
                <span
                  class="px-2.5 py-0.5 rounded-full text-[11px] font-bold inline-block"
                  :class="getStatusBadgeClass(order.status)"
                >
                  {{ getStatusText(order.status) }}
                </span>
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
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import {
  adminService,
  type DashboardSummary,
  type RevenuePoint,
  type Order,
  type DashboardFilterParams,
} from '@/modules/admin/services/adminService'

const loading = ref(true)
const dashboardData = ref<{
  summary: DashboardSummary
  revenue_chart: RevenuePoint[]
  recent_orders: Order[]
  filters?: DashboardFilterParams
} | null>(null)

type PeriodKey = 'today' | '7days' | '30days' | 'this_month' | 'this_year' | 'all' | 'custom'

const selectedPeriod = ref<PeriodKey>('30days')
const customFromDate = ref<string>('')
const customToDate = ref<string>('')
const selectedOrderStatus = ref<string>('all')

const periodOptions = [
  { key: 'today' as PeriodKey, label: 'Hôm nay' },
  { key: '7days' as PeriodKey, label: '7 ngày' },
  { key: '30days' as PeriodKey, label: '30 ngày' },
  { key: 'this_month' as PeriodKey, label: 'Tháng này' },
  { key: 'this_year' as PeriodKey, label: 'Năm nay' },
  { key: 'custom' as PeriodKey, label: 'Tùy chọn' },
]

const statusTabs = [
  { key: 'all', label: 'Tất cả' },
  { key: 'pending', label: 'Chờ duyệt' },
  { key: 'processing', label: 'Đang gói' },
  { key: 'shipped', label: 'Đang giao' },
  { key: 'delivered', label: 'Hoàn thành' },
  { key: 'cancelled', label: 'Đã hủy' },
]

const activePeriodText = computed(() => {
  switch (selectedPeriod.value) {
    case 'today':
      return 'Hôm nay'
    case '7days':
      return '7 ngày gần nhất'
    case '30days':
      return '30 ngày gần nhất'
    case 'this_month':
      return 'Tháng này'
    case 'this_year':
      return 'Năm nay'
    case 'custom':
      return customFromDate.value && customToDate.value
        ? `Từ ${customFromDate.value} đến ${customToDate.value}`
        : 'Khoảng ngày tùy chọn'
    default:
      return 'Toàn thời gian'
  }
})

async function fetchData() {
  try {
    loading.value = true
    const params: DashboardFilterParams = {
      period: selectedPeriod.value,
      order_status: selectedOrderStatus.value,
    }
    if (selectedPeriod.value === 'custom' && customFromDate.value && customToDate.value) {
      params.from_date = customFromDate.value
      params.to_date = customToDate.value
    }
    const res = await adminService.getDashboard(params)
    dashboardData.value = res
  } catch (err) {
    console.error('Lỗi tải dữ liệu dashboard admin:', err)
  } finally {
    loading.value = false
  }
}

function selectPeriod(period: PeriodKey) {
  selectedPeriod.value = period
  if (period !== 'custom') {
    fetchData()
  }
}

function applyCustomDate() {
  if (customFromDate.value && customToDate.value) {
    fetchData()
  }
}

function selectOrderStatus(status: string) {
  selectedOrderStatus.value = status
  fetchData()
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
