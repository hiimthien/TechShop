<template>
  <nav
    v-if="lastPage > 1"
    class="flex flex-wrap items-center justify-between gap-4 py-4 px-2"
    aria-label="Pagination Navigation"
  >
    <!-- Left: Hiển thị thông tin kết quả -->
    <div v-if="total !== undefined && perPage !== undefined" class="text-xs text-on-surface-variant font-medium">
      Hiển thị
      <strong class="text-on-surface font-bold">{{ startItem }}</strong>
      -
      <strong class="text-on-surface font-bold">{{ endItem }}</strong>
      trong tổng số
      <strong class="text-on-surface font-bold">{{ total }}</strong>
      kết quả
    </div>
    <div v-else class="text-xs text-on-surface-variant font-medium">
      Trang <strong class="text-on-surface font-bold">{{ currentPage }}</strong> / {{ lastPage }}
    </div>

    <!-- Right: Nút điều hướng các trang -->
    <div class="flex items-center gap-1.5 ml-auto">
      <!-- Nút Trang trước -->
      <button
        type="button"
        @click="goToPage(currentPage - 1)"
        :disabled="currentPage <= 1"
        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-outline-variant/60 bg-surface-container-lowest text-on-surface hover:bg-surface-container-low hover:border-primary/50 disabled:opacity-40 disabled:cursor-not-allowed transition-all text-xs"
        aria-label="Trang trước"
      >
        <span class="material-symbols-outlined text-[16px]">chevron_left</span>
      </button>

      <!-- Các số trang -->
      <template v-for="page in visiblePages" :key="page">
        <!-- Dấu 3 chấm -->
        <span
          v-if="page === -1"
          class="w-8 h-8 flex items-center justify-center text-xs text-on-surface-variant select-none"
        >
          ...
        </span>

        <!-- Nút số trang cụ thể -->
        <button
          v-else
          type="button"
          @click="goToPage(page)"
          class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold transition-all"
          :class="
            page === currentPage
              ? 'bg-primary text-on-primary shadow-xs font-black'
              : 'border border-outline-variant/60 bg-surface-container-lowest text-on-surface hover:bg-surface-container-low hover:border-primary/50'
          "
        >
          {{ page }}
        </button>
      </template>

      <!-- Nút Trang kế tiếp -->
      <button
        type="button"
        @click="goToPage(currentPage + 1)"
        :disabled="currentPage >= lastPage"
        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-outline-variant/60 bg-surface-container-lowest text-on-surface hover:bg-surface-container-low hover:border-primary/50 disabled:opacity-40 disabled:cursor-not-allowed transition-all text-xs"
        aria-label="Trang kế tiếp"
      >
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      </button>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  currentPage: number
  lastPage: number
  total?: number
  perPage?: number
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'change-page', page: number): void
}>()

const startItem = computed(() => {
  if (!props.total || !props.perPage) return 0
  return (props.currentPage - 1) * props.perPage + 1
})

const endItem = computed(() => {
  if (!props.total || !props.perPage) return 0
  return Math.min(props.currentPage * props.perPage, props.total)
})

// Tính danh sách các trang hiển thị thông minh kèm dấu "..." (-1)
const visiblePages = computed<number[]>(() => {
  const current = props.currentPage
  const total = props.lastPage
  const pages: number[] = []

  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i)
    return pages
  }

  // Luôn có trang 1
  pages.push(1)

  if (current > 3) {
    pages.push(-1) // Ellipsis
  }

  const start = Math.max(2, current - 1)
  const end = Math.min(total - 1, current + 1)

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  if (current < total - 2) {
    pages.push(-1) // Ellipsis
  }

  // Luôn có trang cuối
  pages.push(total)

  return pages
})

function goToPage(page: number) {
  if (page >= 1 && page <= props.lastPage && page !== props.currentPage) {
    emit('change-page', page)
  }
}
</script>

