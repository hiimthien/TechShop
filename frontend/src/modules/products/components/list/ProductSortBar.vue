<template>
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 bg-surface-container-lowest p-4 rounded-2xl border border-outline-variant/30 shadow-xs">
    <!-- Counter -->
    <div class="flex items-center gap-1.5 text-xs text-on-surface-variant shrink-0">
      <span>Hiển thị</span>
      <strong class="text-primary font-bold px-0.5">{{ count }}</strong>
      <span>/</span>
      <strong class="text-on-surface font-bold px-0.5">{{ total }}</strong>
      <span>sản phẩm</span>
    </div>

    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
      <!-- Search Box -->
      <div class="flex items-center flex-grow sm:flex-grow-0 sm:w-64 bg-surface-container-low rounded-xl px-3 py-1.5 border border-outline-variant/40 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all">
        <span class="material-symbols-outlined text-outline text-[18px] shrink-0 select-none">
          search
        </span>
        <input
          :value="searchQuery"
          @input="$emit('update:searchQuery', ($event.target as HTMLInputElement).value)"
          @keyup.enter="$emit('search')"
          type="text"
          placeholder="Tìm theo tên..."
          class="w-full bg-transparent !border-0 !border-none !ring-0 !shadow-none !outline-none text-xs px-2 py-0 placeholder:text-outline text-on-surface"
        />
        <button
          v-if="searchQuery"
          @click="$emit('clear-search')"
          class="text-outline hover:text-on-surface flex items-center justify-center shrink-0 cursor-pointer ml-1"
          type="button"
          aria-label="Xóa từ khóa tìm kiếm"
        >
          <span class="material-symbols-outlined text-[16px]">close</span>
        </button>
      </div>

      <!-- Sort Dropdown -->
      <div class="flex items-center gap-1.5">
        <span class="text-xs text-outline hidden md:inline">Sắp xếp:</span>
        <select
          :value="sortOrder"
          @change="$emit('update:sortOrder', ($event.target as HTMLSelectElement).value)"
          class="bg-surface-container-low border border-outline-variant/40 rounded-xl px-3 py-1.5 text-xs font-medium text-on-surface focus:outline-none focus:border-primary cursor-pointer"
        >
          <option value="latest">Mới nhất</option>
          <option value="price_asc">Giá: Thấp đến cao</option>
          <option value="price_desc">Giá: Cao đến thấp</option>
          <option value="name_asc">Tên: A - Z</option>
        </select>
      </div>

      <!-- Mobile Filter Toggle Button -->
      <button
        type="button"
        @click="$emit('open-mobile-filter')"
        class="lg:hidden inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/60 bg-surface-container-low text-xs font-bold text-on-surface hover:border-primary transition-colors shrink-0"
      >
        <span class="material-symbols-outlined text-[17px] text-primary">tune</span>
        <span>Bộ lọc</span>
        <span v-if="hasActiveFilters" class="w-2 h-2 rounded-full bg-primary"></span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  count: number
  total: number
  searchQuery: string
  sortOrder: string
  hasActiveFilters: boolean
}

defineProps<Props>()
defineEmits<{
  (e: 'update:searchQuery', val: string): void
  (e: 'update:sortOrder', val: string): void
  (e: 'search'): void
  (e: 'clear-search'): void
  (e: 'open-mobile-filter'): void
}>()
</script>

