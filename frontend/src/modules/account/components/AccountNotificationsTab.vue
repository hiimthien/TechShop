<template>
  <div class="p-6 md:p-8 bg-surface-container-lowest rounded-3xl border border-outline-variant/30 space-y-6 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-outline-variant/20 gap-3">
      <div>
        <h3 class="text-lg font-black text-on-surface flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">notifications</span>
          Trung tâm thông báo
        </h3>
        <p class="text-xs text-on-surface-variant mt-1">
          Cập nhật mới nhất về đơn hàng, bảo hành và chương trình ưu đãi của bạn
        </p>
      </div>
      <button
        v-if="unreadCount > 0"
        type="button"
        @click="$emit('mark-all-read')"
        class="px-3.5 py-1.5 rounded-xl border border-primary text-primary hover:bg-primary/5 text-xs font-bold transition-all self-start sm:self-auto"
      >
        Đánh dấu đã đọc tất cả
      </button>
    </div>

    <!-- Filter Pills -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs">
      <button
        v-for="cat in categories"
        :key="cat.val"
        type="button"
        @click="currentCategory = cat.val"
        class="px-3 py-1.5 rounded-xl font-bold transition-all whitespace-nowrap"
        :class="
          currentCategory === cat.val
            ? 'bg-primary text-on-primary shadow-xs'
            : 'bg-surface-container-low text-on-surface-variant hover:text-on-surface'
        "
      >
        {{ cat.label }}
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="py-12 text-center text-xs text-outline flex flex-col items-center gap-2">
      <span class="material-symbols-outlined animate-spin text-2xl text-primary">progress_activity</span>
      <span>Đang tải danh sách thông báo...</span>
    </div>

    <!-- Empty -->
    <div v-else-if="filteredList.length === 0">
      <EmptyState
        icon="notifications_off"
        title="Chưa có thông báo nào"
        description="Bạn không có thông báo nào trong mục này. Chúng tôi sẽ cập nhật ngay khi có tin mới!"
      />
    </div>

    <!-- List -->
    <div v-else class="space-y-3">
      <div
        v-for="item in filteredList"
        :key="item.id"
        @click="$emit('click-item', item)"
        class="p-4 rounded-2xl border transition-all cursor-pointer flex items-start gap-4 relative group"
        :class="
          item.is_read
            ? 'bg-surface-container-lowest border-outline-variant/30 hover:border-primary/40'
            : 'bg-primary/[0.04] border-primary/30 hover:border-primary shadow-xs'
        "
      >
        <div
          class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0"
          :class="getIconColorClass(item.category)"
        >
          <span class="material-symbols-outlined text-[20px]">
            {{ item.icon || getDefaultIcon(item.category) }}
          </span>
        </div>

        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between gap-2 mb-1">
            <h4 class="text-xs font-bold text-on-surface" :class="{ 'text-primary font-black': !item.is_read }">
              {{ item.title }}
            </h4>
            <span class="text-[10px] text-outline whitespace-nowrap">
              {{ timeAgo(item.created_at) }}
            </span>
          </div>
          <p class="text-xs text-on-surface-variant leading-relaxed">
            {{ item.message }}
          </p>
        </div>

        <span
          v-if="!item.is_read"
          class="w-2.5 h-2.5 rounded-full bg-primary shrink-0 self-center"
          title="Chưa đọc"
        ></span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import type { AppNotification } from '@/core/services/notificationService'
import { useFormatters } from '@/core/composables/useFormatters'
import EmptyState from '@/core/components/ui/EmptyState.vue'

interface Props {
  notifications: AppNotification[]
  unreadCount?: number
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  unreadCount: 0,
  loading: false,
})

defineEmits<{
  (e: 'click-item', item: AppNotification): void
  (e: 'mark-all-read'): void
}>()

const { timeAgo } = useFormatters()
const currentCategory = ref('all')

const categories = [
  { label: 'Tất cả', val: 'all' },
  { label: 'Đơn hàng', val: 'order' },
  { label: 'Bảo hành', val: 'warranty' },
  { label: 'Ưu đãi', val: 'coupon' },
]

const filteredList = computed(() => {
  if (currentCategory.value === 'all') return props.notifications
  return props.notifications.filter((n) => n.category === currentCategory.value)
})

function getDefaultIcon(cat?: string) {
  if (cat === 'order') return 'shopping_bag'
  if (cat === 'warranty') return 'build'
  if (cat === 'coupon') return 'local_activity'
  return 'notifications'
}

function getIconColorClass(cat?: string) {
  if (cat === 'order') return 'bg-primary/10 text-primary'
  if (cat === 'warranty') return 'bg-amber-500/10 text-amber-600'
  if (cat === 'coupon') return 'bg-emerald-500/10 text-emerald-600'
  return 'bg-surface-container text-on-surface-variant'
}
</script>

