<template>
  <div class="relative" ref="dropdownRef">
    <!-- Nút Chuông -->
    <button
      type="button"
      @click="toggleDropdown"
      class="relative p-2 text-on-surface hover:text-primary hover:bg-surface-container transition-all rounded-xl active:scale-95 duration-150 flex items-center justify-center"
      :class="{ 'text-primary bg-surface-container': isOpen }"
      title="Thông báo"
    >
      <span class="material-symbols-outlined text-[22px]">notifications</span>
      <!-- Badge số lượng chưa đọc -->
      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 bg-error text-on-error text-[10px] font-black min-w-4 h-4 px-1 flex items-center justify-center rounded-full shadow-sm animate-pulse"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <!-- Dropdown Menu -->
    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="transform scale-95 opacity-0 -translate-y-1"
      enter-to-class="transform scale-100 opacity-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="transform scale-100 opacity-100 translate-y-0"
      leave-to-class="transform scale-95 opacity-0 -translate-y-1"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-80 sm:w-96 bg-surface-container-lowest rounded-2xl shadow-xl border border-outline-variant/40 overflow-hidden z-50 flex flex-col max-h-[520px]"
      >
        <!-- Header Dropdown -->
        <div class="p-3.5 px-4 border-b border-outline-variant/20 flex items-center justify-between bg-surface-container-low/50">
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">notifications_active</span>
            <h3 class="font-black text-sm text-on-surface">Thông báo</h3>
            <span v-if="unreadCount > 0" class="text-[11px] bg-primary/10 text-primary font-bold px-2 py-0.5 rounded-full">
              {{ unreadCount }} mới
            </span>
          </div>
          <button
            v-if="unreadCount > 0"
            @click="handleMarkAllAsRead"
            type="button"
            class="text-[11px] font-bold text-primary hover:underline"
          >
            Đọc tất cả
          </button>
        </div>

        <!-- Filter Tabs -->
        <div class="flex items-center gap-1 p-2 px-3 border-b border-outline-variant/20 bg-surface-container-lowest text-xs overflow-x-auto scrollbar-none">
          <button
            v-for="tab in filterTabs"
            :key="tab.value"
            @click="activeCategory = tab.value"
            type="button"
            class="px-2.5 py-1 rounded-lg font-medium whitespace-nowrap transition-colors"
            :class="
              activeCategory === tab.value
                ? 'bg-primary text-on-primary font-bold shadow-xs'
                : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'
            "
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- Notification List -->
        <div class="overflow-y-auto flex-1 divide-y divide-outline-variant/15">
          <!-- Loading -->
          <div v-if="isLoading" class="p-6 text-center text-xs text-outline flex flex-col items-center gap-2">
            <span class="material-symbols-outlined animate-spin text-2xl text-primary">progress_activity</span>
            <span>Đang tải thông báo...</span>
          </div>

          <!-- Empty -->
          <div v-else-if="filteredNotifications.length === 0" class="py-12 px-4 text-center flex flex-col items-center justify-center gap-2 text-on-surface-variant">
            <span class="material-symbols-outlined text-4xl text-outline/60">notifications_off</span>
            <p class="text-xs font-medium">Chưa có thông báo nào trong mục này</p>
          </div>

          <!-- Items -->
          <div
            v-else
            v-for="item in filteredNotifications"
            :key="item.id"
            @click="handleNotificationClick(item)"
            class="p-3.5 px-4 flex items-start gap-3 hover:bg-surface-container-low/70 cursor-pointer transition-colors relative group"
            :class="{ 'bg-primary/[0.03]': !item.is_read }"
          >
            <!-- Icon -->
            <div
              class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
              :class="getCategoryBadgeClass(item.category)"
            >
              <span class="material-symbols-outlined text-[18px]">
                {{ item.icon || getCategoryIcon(item.category) }}
              </span>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-1 mb-0.5">
                <h4
                  class="text-xs font-bold text-on-surface truncate"
                  :class="{ 'text-primary font-black': !item.is_read }"
                >
                  {{ item.title }}
                </h4>
                <span class="text-[10px] text-outline shrink-0 whitespace-nowrap">
                  {{ formatTimeAgo(item.created_at) }}
                </span>
              </div>
              <p class="text-[11px] text-on-surface-variant leading-relaxed line-clamp-2">
                {{ item.message }}
              </p>
            </div>

            <!-- Dot unread -->
            <span
              v-if="!item.is_read"
              class="w-2 h-2 rounded-full bg-primary shrink-0 self-center ring-2 ring-surface"
              title="Chưa đọc"
            ></span>
          </div>
        </div>

        <!-- Footer Dropdown -->
        <div class="p-2.5 px-4 border-t border-outline-variant/20 bg-surface-container-low/40 text-center">
          <RouterLink
            to="/account"
            @click="isOpen = false"
            class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1"
          >
            <span>Xem tất cả trong tài khoản</span>
            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
          </RouterLink>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { notificationService, type AppNotification } from '../services/notificationService'

const router = useRouter()
const dropdownRef = ref<HTMLElement | null>(null)

const isOpen = ref(false)
const isLoading = ref(false)
const notifications = ref<AppNotification[]>([])
const unreadCount = ref(0)
const activeCategory = ref<string>('all')

const filterTabs = [
  { label: 'Tất cả', value: 'all' },
  { label: 'Đơn hàng', value: 'order' },
  { label: 'Bảo hành', value: 'warranty' },
  { label: 'Ưu đãi', value: 'coupon' },
]

const filteredNotifications = computed(() => {
  if (activeCategory.value === 'all') return notifications.value
  return notifications.value.filter((n) => n.category === activeCategory.value)
})

const fetchNotifications = async () => {
  const token = localStorage.getItem('token')
  if (!token) return

  isLoading.value = true
  try {
    const res = await notificationService.getNotifications({ per_page: 25 })
    notifications.value = res.data
    unreadCount.value = res.unread_count
  } catch (error) {
    console.error('Lỗi khi tải thông báo:', error)
  } finally {
    isLoading.value = false
  }
}

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    fetchNotifications()
  }
}

const handleMarkAllAsRead = async () => {
  try {
    await notificationService.markAllAsRead()
    notifications.value.forEach((n) => {
      n.is_read = true
    })
    unreadCount.value = 0
  } catch (error) {
    console.error('Lỗi khi đánh dấu đọc tất cả:', error)
  }
}

const handleNotificationClick = async (item: AppNotification) => {
  if (!item.is_read) {
    try {
      await notificationService.markAsRead(item.id)
      item.is_read = true
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    } catch (e) {
      console.error(e)
    }
  }

  isOpen.value = false
  if (item.link) {
    router.push(item.link)
  }
}

const getCategoryIcon = (cat: string) => {
  switch (cat) {
    case 'order':
      return 'shopping_bag'
    case 'warranty':
      return 'build'
    case 'coupon':
      return 'local_activity'
    default:
      return 'notifications'
  }
}

const getCategoryBadgeClass = (cat: string) => {
  switch (cat) {
    case 'order':
      return 'bg-primary/10 text-primary'
    case 'warranty':
      return 'bg-amber-500/10 text-amber-600'
    case 'coupon':
      return 'bg-emerald-500/10 text-emerald-600'
    default:
      return 'bg-surface-container-high text-on-surface'
  }
}

const formatTimeAgo = (dateStr: string) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const now = new Date()
  const diffSec = Math.floor((now.getTime() - date.getTime()) / 1000)

  if (diffSec < 60) return 'Vừa xong'
  if (diffSec < 3600) return `${Math.floor(diffSec / 60)} phút trước`
  if (diffSec < 86400) return `${Math.floor(diffSec / 3600)} giờ trước`
  if (diffSec < 604800) return `${Math.floor(diffSec / 86400)} ngày trước`

  return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' })
}

const handleClickOutside = (e: MouseEvent) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target as Node)) {
    isOpen.value = false
  }
}

onMounted(() => {
  fetchNotifications()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

