<template>
  <span
    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border transition-colors"
    :class="statusClasses"
  >
    <span v-if="dot" class="w-1.5 h-1.5 rounded-full" :class="dotColorClass"></span>
    <span v-if="icon" class="material-symbols-outlined text-[13px]">{{ icon }}</span>
    <slot>{{ label }}</slot>
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  status?: string
  label?: string
  dot?: boolean
  icon?: string
}

const props = withDefaults(defineProps<Props>(), {
  status: '',
  label: '',
  dot: true,
  icon: '',
})

const normalized = computed(() => (props.status || '').toLowerCase().trim())

const statusClasses = computed(() => {
  switch (normalized.value) {
    case 'pending':
    case 'chờ duyệt':
    case 'chờ thanh toán':
      return 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/50'

    case 'confirmed':
    case 'processing':
    case 'đang chuẩn bị':
    case 'đang tiếp nhận':
    case 'đang kiểm tra':
      return 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/50'

    case 'shipping':
    case 'đang giao':
      return 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800/50'

    case 'delivered':
    case 'completed':
    case 'hoàn tất':
    case 'đã nhận':
    case 'đã giao':
    case 'đã bảo hành xong':
      return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/50'

    case 'cancelled':
    case 'rejected':
    case 'đã hủy':
    case 'từ chối':
      return 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/50'

    case 'active':
    case 'hoạt động':
      return 'bg-primary/10 text-primary border-primary/20'

    default:
      return 'bg-surface-container text-on-surface-variant border-outline-variant/50'
  }
})

const dotColorClass = computed(() => {
  switch (normalized.value) {
    case 'pending':
    case 'chờ duyệt':
    case 'chờ thanh toán':
      return 'bg-amber-500'
    case 'confirmed':
    case 'processing':
    case 'đang chuẩn bị':
    case 'đang tiếp nhận':
    case 'đang kiểm tra':
      return 'bg-blue-500'
    case 'shipping':
    case 'đang giao':
      return 'bg-indigo-500'
    case 'delivered':
    case 'completed':
    case 'hoàn tất':
    case 'đã nhận':
    case 'đã giao':
    case 'đã bảo hành xong':
      return 'bg-emerald-500'
    case 'cancelled':
    case 'rejected':
    case 'đã hủy':
    case 'từ chối':
      return 'bg-rose-500'
    default:
      return 'bg-outline'
  }
})
</script>

