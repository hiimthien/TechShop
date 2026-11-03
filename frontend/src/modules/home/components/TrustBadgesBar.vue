<template>
  <section class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
    <div
      v-for="badge in badges"
      :key="badge.id"
      class="flex items-center gap-3 p-3.5 sm:p-4 rounded-xl bg-surface-container-low border border-outline-variant/30 hover:border-primary/40 hover:bg-surface-container-high/60 transition-all duration-300 group"
    >
      <div
        class="w-10 h-10 sm:w-11 sm:h-11 rounded-lg flex items-center justify-center shrink-0 transition-transform group-hover:scale-110"
        :class="badge.bgClass"
      >
        <span class="material-symbols-outlined text-xl sm:text-2xl" :class="badge.iconClass">
          {{ badge.icon }}
        </span>
      </div>
      <div class="min-w-0">
        <h4 class="text-xs sm:text-sm font-bold text-on-surface line-clamp-1">
          {{ badge.title }}
        </h4>
        <p class="text-[11px] sm:text-xs text-on-surface-variant line-clamp-1 mt-0.5">
          {{ badge.subtitle }}
        </p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const badges = [
  {
    id: 1,
    icon: 'verified',
    title: '100% Chính Hãng',
    subtitle: 'Bảo hành đầy đủ VAT',
    bgClass: 'bg-emerald-500/10',
    iconClass: 'text-emerald-600 dark:text-emerald-400',
  },
  {
    id: 2,
    icon: 'bolt',
    title: 'Giao Siêu Tốc 2H',
    subtitle: 'Nội thành nhận ngay',
    bgClass: 'bg-amber-500/10',
    iconClass: 'text-amber-600 dark:text-amber-400',
  },
  {
    id: 3,
    icon: 'published_with_changes',
    title: 'Lỗi Là Đổi 30 Ngày',
    subtitle: '1 đổi 1 tận nơi chu đáo',
    bgClass: 'bg-blue-500/10',
    iconClass: 'text-blue-600 dark:text-blue-400',
  },
  {
    id: 4,
    icon: 'credit_card',
    title: 'Trả Góp 0% Dễ Dàng',
    subtitle: 'Xét duyệt hồ sơ 5 phút',
    bgClass: 'bg-purple-500/10',
    iconClass: 'text-purple-600 dark:text-purple-400',
  },
]
</script>

