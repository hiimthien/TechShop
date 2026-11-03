<template>
  <div class="min-h-screen bg-surface py-8 md:py-12 px-4 md:px-8">
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Breadcrumb / Header Title -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-outline-variant/30 pb-4">
        <div>
          <h1 class="text-2xl md:text-3xl font-black text-on-surface flex items-center gap-3">
            <span class="w-2 h-7 bg-primary rounded-full"></span>
            Tài khoản của tôi
          </h1>
          <p class="text-xs text-on-surface-variant mt-1">
            Quản lý thông tin cá nhân, theo dõi lịch sử đơn hàng và nhận voucher ưu đãi từ TechShop.
          </p>
        </div>
        <div class="flex items-center gap-2">
          <RouterLink
            to="/products"
            class="text-xs font-bold text-primary hover:bg-primary/10 px-3.5 py-2 rounded-xl transition-colors flex items-center gap-1.5 border border-primary/20"
          >
            <span class="material-symbols-outlined text-[16px]">storefront</span>
            Tiếp tục mua sắm
          </RouterLink>
        </div>
      </div>

      <!-- Main Layout: Sidebar & Content -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- 1. Left Sidebar Navigation -->
        <aside class="lg:col-span-4 bg-surface-container-lowest rounded-3xl border border-outline-variant/30 p-6 space-y-6 shadow-sm">
          <!-- User Summary Card -->
          <div class="flex items-center gap-4 border-b border-outline-variant/20 pb-5">
            <div class="relative shrink-0">
              <img
                v-if="userProfile.avatar"
                :src="userProfile.avatar"
                :alt="userProfile.name"
                class="w-16 h-16 rounded-2xl object-cover ring-2 ring-primary/20"
              />
              <div
                v-else
                class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary to-primary-container text-white flex items-center justify-center text-2xl font-black shadow-md"
              >
                {{ (userProfile.name || 'U').charAt(0).toUpperCase() }}
              </div>
              <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-2 border-white rounded-full flex items-center justify-center text-[10px] text-white">
                ✓
              </span>
            </div>
            <div class="overflow-hidden">
              <h2 class="text-base font-bold text-on-surface truncate">
                {{ userProfile.name || 'Khách hàng TechShop' }}
              </h2>
              <p class="text-xs text-on-surface-variant truncate">
                {{ userProfile.email }}
              </p>
              <div class="inline-flex items-center gap-1 px-2.5 py-0.5 mt-1.5 rounded-full bg-primary/10 text-primary text-[10px] font-bold">
                <span class="material-symbols-outlined text-[12px]">verified</span>
                Thành viên VIP
              </div>
            </div>
          </div>

          <!-- Navigation Tabs -->
          <nav class="space-y-1.5 text-xs font-semibold">
            <button
              v-for="tab in navTabs"
              :key="tab.id"
              type="button"
              @click="activeTab = tab.id"
              :class="[
                'w-full flex items-center gap-3 px-4 py-3 rounded-2xl transition-all text-left',
                activeTab === tab.id
                  ? 'bg-primary text-white shadow-md shadow-primary/20 font-bold'
                  : 'text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface'
              ]"
            >
              <span class="material-symbols-outlined text-[20px]">{{ tab.icon }}</span>
              <span class="flex-1">{{ tab.label }}</span>
              <span
                v-if="tab.badge !== undefined && tab.badge > 0"
                class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                :class="
                  activeTab === tab.id
                    ? 'bg-white/20 text-white'
                    : (tab.badgeVariant === 'error' ? 'bg-error text-white' : 'bg-surface-container text-on-surface')
                "
              >
                {{ tab.badge }}
              </span>
              <span v-else class="material-symbols-outlined text-[16px] opacity-70">chevron_right</span>
            </button>
          </nav>

          <!-- Logout Button -->
          <div class="border-t border-outline-variant/20 pt-4">
            <button
              type="button"
              @click="handleLogout"
              class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border border-outline-variant/30 text-on-surface-variant hover:text-primary hover:bg-primary/5 hover:border-primary/30 text-xs font-bold transition-all"
            >
              <span class="material-symbols-outlined text-[18px]">logout</span>
              Đăng xuất tài khoản
            </button>
          </div>
        </aside>

        <!-- 2. Right Content Area (Tab Panels) -->
        <main class="lg:col-span-8 space-y-6">
          <!-- Tab 1: Profile -->
          <AccountProfileTab
            v-if="activeTab === 'profile'"
            :user="userProfile"
            @updated="onProfileUpdated"
          />

          <!-- Tab 2: Orders -->
          <AccountOrdersTab
            v-else-if="activeTab === 'orders'"
            :orders="orders"
            :loading="isLoadingOrders"
            @view-detail="openOrderDetail"
            @cancel="handleCancelOrder"
          />

          <!-- Tab 3: Vouchers -->
          <AccountVouchersTab
            v-else-if="activeTab === 'vouchers'"
            :coupons="coupons"
            :loading="isLoadingCoupons"
          />

          <!-- Tab 4: Warranties -->
          <AccountWarrantiesTab
            v-else-if="activeTab === 'warranties'"
            :warranties="userWarranties"
            :loading="isLoadingWarranties"
            @create="showCreateWarrantyModal = true"
          />

          <!-- Tab 5: Notifications -->
          <AccountNotificationsTab
            v-else-if="activeTab === 'notifications'"
            :notifications="userNotifications"
            :unread-count="unreadNotificationsCount"
            :loading="isLoadingNotifications"
            @click-item="handleClickNotification"
            @mark-all-read="handleMarkAllNotificationsRead"
          />
        </main>
      </div>
    </div>

    <!-- Modals -->
    <AccountOrderDetailModal
      :order="selectedOrder"
      @close="selectedOrder = null"
      @request-warranty="handleRequestWarrantyFromItem"
    />

    <AccountWarrantyModal
      v-model="showCreateWarrantyModal"
      :form="warrantyForm"
      :submitting="isSubmittingWarranty"
      :error-message="warrantySubmitMessage"
      @submit="handleSubmitWarranty"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, shallowRef, computed, onMounted, defineAsyncComponent } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/modules/auth/stores/authStore'
import { useToast } from '@/core/composables/useToast'
import { useConfirmDialog } from '@/core/composables/useConfirmDialog'
import {
  accountService,
  type UserProfile,
  type CustomerOrder,
  type CouponItem,
  type WarrantyClaimItem,
  type OrderItemDetail,
} from '../services/accountService'
import { notificationService, type AppNotification } from '@/core/services/notificationService'

// Tab Components (Synchronous for fast tab switching)
import AccountProfileTab from '../components/AccountProfileTab.vue'
import AccountOrdersTab from '../components/AccountOrdersTab.vue'
import AccountVouchersTab from '../components/AccountVouchersTab.vue'
import AccountWarrantiesTab from '../components/AccountWarrantiesTab.vue'
import AccountNotificationsTab from '../components/AccountNotificationsTab.vue'

// Modals: Lazy load with defineAsyncComponent to reduce initial bundle size
const AccountOrderDetailModal = defineAsyncComponent(
  () => import('../components/AccountOrderDetailModal.vue')
)
const AccountWarrantyModal = defineAsyncComponent(
  () => import('../components/AccountWarrantyModal.vue')
)

type TabType = 'profile' | 'orders' | 'vouchers' | 'warranties' | 'notifications'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()
const { confirm: confirmDialog } = useConfirmDialog()

const activeTab = ref<TabType>('profile')

// User Profile
const userProfile = ref<UserProfile>({
  id: 0,
  name: '',
  email: '',
  phone: '',
  avatar: '',
})

// Performance Optimization: Use shallowRef for large API arrays to avoid deep reactive proxy overhead
const orders = shallowRef<CustomerOrder[]>([])
const isLoadingOrders = ref(true)
const selectedOrder = shallowRef<CustomerOrder | null>(null)

const coupons = shallowRef<CouponItem[]>([])
const isLoadingCoupons = ref(true)

const userWarranties = shallowRef<WarrantyClaimItem[]>([])
const isLoadingWarranties = ref(false)
const showCreateWarrantyModal = ref(false)
const isSubmittingWarranty = ref(false)
const warrantySubmitMessage = ref('')
const warrantyForm = ref({
  product_name: '',
  serial_number: '',
  issue_description: '',
  address: '',
})

const userNotifications = shallowRef<AppNotification[]>([])
const isLoadingNotifications = ref(false)

const unreadNotificationsCount = computed(() => {
  return userNotifications.value.filter((n) => !n.is_read).length
})

// Navigation Tabs Config
const navTabs = computed(() => [
  { id: 'profile' as TabType, label: 'Thông tin cá nhân', icon: 'person' },
  { id: 'orders' as TabType, label: 'Lịch sử đơn hàng', icon: 'package_2', badge: orders.value.length },
  { id: 'vouchers' as TabType, label: 'Ví Voucher & Ưu đãi', icon: 'confirmation_number', badge: coupons.value.length },
  { id: 'warranties' as TabType, label: 'Bảo hành & Sửa chữa', icon: 'build', badge: userWarranties.value.length },
  {
    id: 'notifications' as TabType,
    label: 'Thông báo của tôi',
    icon: 'notifications',
    badge: unreadNotificationsCount.value,
    badgeVariant: 'error',
  },
])

// Data fetchers
async function fetchProfileData() {
  try {
    userProfile.value = await accountService.getProfile()
  } catch (err) {
    console.error('Lỗi khi tải thông tin hồ sơ:', err)
  }
}

function onProfileUpdated(updatedUser: UserProfile) {
  userProfile.value = updatedUser
}

async function fetchOrdersData() {
  isLoadingOrders.value = true
  try {
    orders.value = await accountService.getMyOrders()
  } catch (err) {
    console.error('Lỗi khi tải lịch sử đơn hàng:', err)
  } finally {
    isLoadingOrders.value = false
  }
}

async function handleCancelOrder(orderId: number) {
  const isConfirmed = await confirmDialog({
    title: 'Hủy đơn hàng này?',
    message: 'Bạn có chắc chắn muốn hủy đơn hàng không? Thao tác này không thể hoàn tác.',
    confirmText: 'Đồng ý hủy đơn',
    cancelText: 'Giữ lại đơn',
    type: 'danger',
  })
  if (!isConfirmed) return

  try {
    const res = await accountService.cancelOrder(orderId)
    toast.success(res.message || 'Đã hủy đơn hàng thành công.')
    await fetchOrdersData()
  } catch (err: unknown) {
    const errorMsg =
      (err as { response?: { data?: { message?: string } } })?.response?.data?.message ||
      'Không thể hủy đơn hàng.'
    toast.error(errorMsg)
  }
}

function openOrderDetail(order: CustomerOrder) {
  selectedOrder.value = order
}

function handleRequestWarrantyFromItem(item: OrderItemDetail) {
  const currentAddress = selectedOrder.value?.address?.address || ''
  selectedOrder.value = null
  activeTab.value = 'warranties'
  warrantyForm.value = {
    product_name: item.product?.name || 'Thiết bị TechShop',
    serial_number: item.serial_number || '',
    issue_description: '',
    address: currentAddress,
  }
  showCreateWarrantyModal.value = true
}

async function fetchCouponsData() {
  isLoadingCoupons.value = true
  try {
    coupons.value = await accountService.getAvailableCoupons()
  } catch (err) {
    console.error('Lỗi khi tải danh sách voucher:', err)
  } finally {
    isLoadingCoupons.value = false
  }
}

async function fetchWarrantiesData() {
  isLoadingWarranties.value = true
  try {
    userWarranties.value = await accountService.getMyWarranties()
  } catch (err) {
    console.error('Lỗi khi tải thông tin bảo hành:', err)
  } finally {
    isLoadingWarranties.value = false
  }
}

async function handleSubmitWarranty() {
  warrantySubmitMessage.value = ''
  isSubmittingWarranty.value = true
  try {
    await accountService.createWarranty({
      customer_name: userProfile.value.name || 'Khách hàng',
      customer_phone: userProfile.value.phone || '',
      customer_email: userProfile.value.email || '',
      product_name: warrantyForm.value.product_name,
      serial_number: warrantyForm.value.serial_number,
      issue_description: warrantyForm.value.issue_description,
      address: warrantyForm.value.address,
    })
    showCreateWarrantyModal.value = false
    warrantyForm.value = {
      product_name: '',
      serial_number: '',
      issue_description: '',
      address: '',
    }
    await fetchWarrantiesData()
    await fetchNotificationsData()
    toast.success('Gửi phiếu yêu cầu bảo hành thành công!')
  } catch (err: unknown) {
    warrantySubmitMessage.value =
      (err as { response?: { data?: { message?: string } } })?.response?.data?.message ||
      'Có lỗi khi gửi phiếu bảo hành.'
  } finally {
    isSubmittingWarranty.value = false
  }
}

async function fetchNotificationsData() {
  isLoadingNotifications.value = true
  try {
    const res = await notificationService.getNotifications()
    userNotifications.value = res.data || []
  } catch (err) {
    console.error('Lỗi khi tải thông báo:', err)
  } finally {
    isLoadingNotifications.value = false
  }
}

async function handleClickNotification(item: AppNotification) {
  if (!item.is_read) {
    try {
      await notificationService.markAsRead(item.id)
      item.is_read = true
    } catch {
      // ignore
    }
  }
  if (item.link) {
    if (item.link.startsWith('http')) {
      window.location.href = item.link
    } else {
      router.push(item.link)
    }
  }
}

async function handleMarkAllNotificationsRead() {
  try {
    await notificationService.markAllAsRead()
    userNotifications.value.forEach((n) => (n.is_read = true))
  } catch (err) {
    console.error('Lỗi khi đánh dấu đã đọc tất cả:', err)
  }
}

async function handleLogout() {
  await authStore.logout()
  await router.push('/login')
}

onMounted(() => {
  fetchProfileData()
  fetchOrdersData()
  fetchCouponsData()
  fetchWarrantiesData()
  fetchNotificationsData()
})
</script>
