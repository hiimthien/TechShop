<template>
  <div class="space-y-6 max-w-container-max mx-auto">
    <!-- 1. Top Bar: Title & Filter Toolbar -->
    <DashboardFilterBar
      :period-options="periodOptions"
      :selected-period="selectedPeriod"
      v-model:custom-from-date="customFromDate"
      v-model:custom-to-date="customToDate"
      :loading="loading"
      :active-period-text="activePeriodText"
      :all-time-revenue="dashboardData?.summary?.all_time_revenue"
      :all-time-orders="dashboardData?.summary?.all_time_orders || dashboardData?.summary?.total_orders"
      @select-period="selectPeriod"
      @apply-custom-date="applyCustomDate"
      @refresh="fetchData"
    />

    <!-- Skeleton Loading -->
    <div v-if="loading && !dashboardData" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="i in 4" :key="i" class="h-32 bg-surface-container-lowest rounded-2xl border border-outline-variant/20 animate-pulse"></div>
    </div>

    <template v-else-if="dashboardData">
      <!-- 2. Metric KPI Cards -->
      <DashboardMetricsGrid :summary="dashboardData.summary" />

      <!-- 3. Middle Section: Revenue Bar Chart & Order Pipeline -->
      <DashboardRevenueChart
        :revenue-chart="dashboardData.revenue_chart"
        :summary="dashboardData.summary"
      />

      <!-- 4. Bottom Section: Recent Orders with Status Tabs -->
      <DashboardRecentOrders
        :orders="dashboardData.recent_orders"
        :selected-order-status="selectedOrderStatus"
        :status-tabs="statusTabs"
        @select-status="selectOrderStatus"
      />
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { adminService, type DashboardData, type DashboardFilterParams } from '../services/adminService'
import DashboardFilterBar from '../components/dashboard/DashboardFilterBar.vue'
import DashboardMetricsGrid from '../components/dashboard/DashboardMetricsGrid.vue'
import DashboardRevenueChart from '../components/dashboard/DashboardRevenueChart.vue'
import DashboardRecentOrders from '../components/dashboard/DashboardRecentOrders.vue'

const loading = ref(true)
const dashboardData = ref<DashboardData | null>(null)

// Period selection state
const selectedPeriod = ref<'today' | '7days' | '30days' | 'this_month' | 'this_year' | 'all' | 'custom'>('this_month')
const customFromDate = ref('')
const customToDate = ref('')
const selectedOrderStatus = ref('all')

const periodOptions: Array<{ key: 'today' | '7days' | '30days' | 'this_month' | 'this_year' | 'all' | 'custom'; label: string }> = [
  { key: 'today', label: 'Hôm nay' },
  { key: '7days', label: '7 ngày' },
  { key: '30days', label: '30 ngày' },
  { key: 'this_month', label: 'Tháng này' },
  { key: 'this_year', label: 'Năm nay' },
  { key: 'all', label: 'Toàn thời gian' },
  { key: 'custom', label: 'Tùy chọn...' },
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
  if (selectedPeriod.value === 'custom') {
    if (customFromDate.value && customToDate.value) {
      return `Từ ${customFromDate.value} đến ${customToDate.value}`
    }
    return 'Khoảng ngày tùy chỉnh'
  }
  const found = periodOptions.find((p) => p.key === selectedPeriod.value)
  return found ? found.label : selectedPeriod.value
})

function buildFilterParams(): DashboardFilterParams {
  const params: DashboardFilterParams = {
    period: selectedPeriod.value,
  }

  if (selectedPeriod.value === 'custom') {
    if (customFromDate.value) params.from_date = customFromDate.value
    if (customToDate.value) params.to_date = customToDate.value
  }

  if (selectedOrderStatus.value && selectedOrderStatus.value !== 'all') {
    params.order_status = selectedOrderStatus.value
  }

  return params
}

async function fetchData() {
  loading.value = true
  try {
    const params = buildFilterParams()
    const res = await adminService.getDashboard(params)
    dashboardData.value = res
  } catch (error) {
    console.error('Failed to load dashboard metrics:', error)
  } finally {
    loading.value = false
  }
}

function selectPeriod(periodKey: string) {
  selectedPeriod.value = periodKey as 'today' | '7days' | '30days' | 'this_month' | 'this_year' | 'all' | 'custom'
  if (periodKey !== 'custom') {
    fetchData()
  }
}

function applyCustomDate() {
  if (customFromDate.value && customToDate.value) {
    fetchData()
  }
}

function selectOrderStatus(statusKey: string) {
  selectedOrderStatus.value = statusKey
  fetchData()
}

onMounted(() => {
  fetchData()
})
</script>
