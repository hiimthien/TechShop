<template>
  <div v-if="totalPages > 1" class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4 select-none">
    <!-- Pagination Nav Buttons -->
    <nav class="flex items-center gap-1.5">
      <!-- First Page -->
      <button
        type="button"
        :disabled="currentPage <= 1"
        @click="changePage(1)"
        class="w-9 h-9 flex items-center justify-center rounded-xl border border-outline-variant/50 text-outline transition-all disabled:opacity-30 disabled:cursor-not-allowed hover:not-disabled:bg-surface-container-high hover:not-disabled:text-on-surface"
        title="Trang đầu"
      >
        <span class="material-symbols-outlined text-[18px]">first_page</span>
      </button>

      <!-- Prev Button -->
      <button
        type="button"
        :disabled="currentPage <= 1"
        @click="changePage(currentPage - 1)"
        class="w-9 h-9 flex items-center justify-center rounded-xl border border-outline-variant/50 text-outline transition-all disabled:opacity-30 disabled:cursor-not-allowed hover:not-disabled:bg-surface-container-high hover:not-disabled:text-on-surface"
        title="Trang trước"
      >
        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
      </button>

      <!-- Page Numbers -->
      <template v-for="page in visiblePages" :key="page">
        <button
          v-if="typeof page === 'number'"
          type="button"
          @click="changePage(page)"
          class="w-9 h-9 flex items-center justify-center rounded-xl text-xs font-bold transition-all"
          :class="
            page === currentPage
              ? 'bg-primary text-on-primary shadow-xs'
              : 'border border-outline-variant/50 text-on-surface hover:bg-surface-container-high'
          "
        >
          {{ page }}
        </button>
        <span v-else class="text-outline px-1 text-xs">...</span>
      </template>

      <!-- Next Button -->
      <button
        type="button"
        :disabled="currentPage >= totalPages"
        @click="changePage(currentPage + 1)"
        class="w-9 h-9 flex items-center justify-center rounded-xl border border-outline-variant/50 text-outline transition-all disabled:opacity-30 disabled:cursor-not-allowed hover:not-disabled:bg-surface-container-high hover:not-disabled:text-on-surface"
        title="Trang sau"
      >
        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
      </button>

      <!-- Last Page -->
      <button
        type="button"
        :disabled="currentPage >= totalPages"
        @click="changePage(totalPages)"
        class="w-9 h-9 flex items-center justify-center rounded-xl border border-outline-variant/50 text-outline transition-all disabled:opacity-30 disabled:cursor-not-allowed hover:not-disabled:bg-surface-container-high hover:not-disabled:text-on-surface"
        title="Trang cuối"
      >
        <span class="material-symbols-outlined text-[18px]">last_page</span>
      </button>
    </nav>

    <!-- Jump to Page Input -->
    <div class="flex items-center gap-1.5 text-xs text-on-surface-variant bg-surface-container-lowest px-3 py-1.5 rounded-xl border border-outline-variant/40 shadow-xs">
      <span>Đến trang:</span>
      <input
        v-model.number="inputPage"
        @keyup.enter="handleJumpPage"
        type="number"
        min="1"
        :max="totalPages"
        class="w-12 text-center py-1 rounded-lg border border-outline-variant/50 text-xs font-bold bg-surface-container-low focus:outline-none focus:border-primary text-on-surface"
      />
      <span class="text-outline">/ {{ totalPages }}</span>
      <button
        type="button"
        @click="handleJumpPage"
        class="px-2.5 py-1 bg-primary text-on-primary rounded-lg text-xs font-bold hover:bg-primary-container transition-colors active:scale-95"
      >
        Đi
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'

const props = defineProps<{
  currentPage: number
  totalPages: number
}>()

const emit = defineEmits<{
  (e: 'changePage', page: number): void
}>()

const inputPage = ref<number>(props.currentPage)

watch(
  () => props.currentPage,
  (newVal) => {
    inputPage.value = newVal
  }
)

const changePage = (page: number) => {
  if (page >= 1 && page <= props.totalPages && page !== props.currentPage) {
    emit('changePage', page)
  }
}

const handleJumpPage = () => {
  let target = Math.floor(inputPage.value)
  if (isNaN(target) || target < 1) target = 1
  if (target > props.totalPages) target = props.totalPages
  inputPage.value = target
  changePage(target)
}

const visiblePages = computed(() => {
  const current = props.currentPage
  const total = props.totalPages
  const delta = 2
  const pages: (number | string)[] = []

  for (let i = 1; i <= total; i++) {
    if (i === 1 || i === total || (i >= current - delta && i <= current + delta)) {
      pages.push(i)
    } else if (pages[pages.length - 1] !== '...') {
      pages.push('...')
    }
  }

  return pages
})
</script>
