<template>
  <div class="space-y-3">
    <!-- Top Bar: Title & Filter Toolbar -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/30 shadow-xs">
      <div>
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-primary text-2xl">analytics</span>
          <h2 class="text-xl sm:text-2xl font-black text-on-surface">Tổng quan hệ thống</h2>
        </div>
        <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5">
          Báo cáo hiệu suất kinh doanh, dòng tiền và tiến trình vận đơn.
        </p>
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
            @click="$emit('select-period', p.key)"
          >
            {{ p.label }}
          </button>
        </div>

        <!-- Custom Date Range Form (when 'custom' is active) -->
        <div v-if="selectedPeriod === 'custom'" class="flex items-center gap-1.5 bg-surface-container-low p-1 rounded-xl border border-outline-variant/30">
          <input
            :value="customFromDate"
            @input="$emit('update:customFromDate', ($event.target as HTMLInputElement).value)"
            type="date"
            class="bg-surface-container-lowest px-2 py-1 rounded-lg text-xs text-on-surface border border-outline-variant/40 focus:outline-none focus:border-primary"
            title="Từ ngày"
          />
          <span class="text-xs text-outline">-</span>
          <input
            :value="customToDate"
            @input="$emit('update:customToDate', ($event.target as HTMLInputElement).value)"
            type="date"
            class="bg-surface-container-lowest px-2 py-1 rounded-lg text-xs text-on-surface border border-outline-variant/40 focus:outline-none focus:border-primary"
            title="Đến ngày"
          />
          <button
            type="button"
            class="px-2.5 py-1 bg-primary text-on-primary rounded-lg text-xs font-bold hover:bg-primary-hover transition cursor-pointer"
            @click="$emit('apply-custom-date')"
          >
            Lọc
          </button>
        </div>

        <!-- Refresh Button -->
        <button
          type="button"
          @click="$emit('refresh')"
          :disabled="loading"
          class="bg-surface-container hover:bg-surface-container-high text-on-surface px-3 py-1.5 rounded-xl flex items-center gap-1.5 transition-all shadow-xs text-xs font-bold border border-outline-variant/40 cursor-pointer disabled:opacity-50"
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
      <div v-if="allTimeRevenue !== undefined" class="hidden sm:block">
        <span>Toàn thời gian: </span>
        <strong class="text-primary font-bold">{{ formatPrice(allTimeRevenue) }}</strong>
        <span class="text-outline mx-1">•</span>
        <strong>{{ allTimeOrders }}</strong> đơn
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useFormatters } from '@/core/composables/useFormatters'

interface PeriodOption {
  key: string
  label: string
}

interface Props {
  periodOptions: PeriodOption[]
  selectedPeriod: string
  customFromDate: string
  customToDate: string
  loading: boolean
  activePeriodText: string
  allTimeRevenue?: number
  allTimeOrders?: number
}

defineProps<Props>()
defineEmits<{
  (e: 'select-period', key: string): void
  (e: 'update:customFromDate', val: string): void
  (e: 'update:customToDate', val: string): void
  (e: 'apply-custom-date'): void
  (e: 'refresh'): void
}>()

const { formatPrice } = useFormatters()
</script>

