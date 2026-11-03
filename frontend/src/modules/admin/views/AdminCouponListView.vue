<template>
  <div class="space-y-lg max-w-container-max mx-auto">
    <!-- Header Section -->
    <header class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-sm mb-md">
      <div>
        <h1 class="text-headline-lg font-headline-lg font-bold text-on-surface tracking-tight">Quản lý mã giảm giá (Vouchers)</h1>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Tạo và thiết lập các chiến dịch khuyến mãi, giảm giá trực tiếp hoặc phần trăm.</p>
      </div>
      <div>
        <button
          @click="openCreateModal"
          class="bg-primary hover:bg-primary-container text-on-primary px-lg py-md rounded-lg flex items-center gap-xs transition-all shadow-md active:scale-95"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          <span class="text-label-md font-label-md">Tạo mã mới</span>
        </button>
      </div>
    </header>

    <!-- Filters & Search Section -->
    <section class="bg-surface p-lg rounded-xl shadow-sm border border-outline-variant/30">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-md">
        <!-- Filter Tabs -->
        <div class="flex gap-xs bg-surface-container-low p-base rounded-lg border border-outline-variant/40">
          <button
            @click="filterStatus = 'all'; fetchCoupons(1)"
            class="px-md py-xs rounded-md text-label-md font-bold transition"
            :class="filterStatus === 'all' ? 'bg-surface text-primary shadow-sm' : 'text-on-surface-variant hover:bg-surface-container'"
          >
            Tất cả
          </button>
          <button
            @click="filterStatus = 'active'; fetchCoupons(1)"
            class="px-md py-xs rounded-md text-label-md font-bold transition"
            :class="filterStatus === 'active' ? 'bg-surface text-primary shadow-sm' : 'text-on-surface-variant hover:bg-surface-container'"
          >
            Đang chạy
          </button>
          <button
            @click="filterStatus = 'inactive'; fetchCoupons(1)"
            class="px-md py-xs rounded-md text-label-md font-bold transition"
            :class="filterStatus === 'inactive' ? 'bg-surface text-primary shadow-sm' : 'text-on-surface-variant hover:bg-surface-container'"
          >
            Tạm dừng
          </button>
        </div>

        <!-- Search Input -->
        <div class="relative w-full sm:w-72">
          <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
          <input
            v-model="searchQuery"
            @keyup.enter="fetchCoupons(1)"
            type="text"
            placeholder="Tìm theo mã coupon..."
            class="w-full pl-xl pr-md py-xs bg-surface-container-low border border-outline-variant rounded-full focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-body-sm uppercase font-mono"
          />
        </div>
      </div>
    </section>

    <!-- Coupons Table -->
    <section class="bg-surface rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
      <div v-if="loading" class="p-16 text-center text-on-surface-variant">
        <div class="inline-block animate-spin w-8 h-8 border-4 border-primary border-t-transparent rounded-full mb-3"></div>
        <div class="text-body-sm font-medium">Đang tải mã giảm giá...</div>
      </div>

      <div v-else-if="!coupons.length" class="p-16 text-center text-on-surface-variant">
        <span class="material-symbols-outlined text-5xl text-outline mb-2">confirmation_number</span>
        <div class="text-headline-sm font-bold text-on-surface">Chưa có mã giảm giá nào</div>
        <p class="text-body-sm text-on-surface-variant mt-1">Bấm "Tạo mã mới" để bắt đầu thiết lập khuyến mãi</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant">
              <th class="px-lg py-md text-left text-label-md font-label-md text-on-surface-variant">Mã Voucher</th>
              <th class="px-lg py-md text-left text-label-md font-label-md text-on-surface-variant">Mức giảm</th>
              <th class="px-lg py-md text-left text-label-md font-label-md text-on-surface-variant">Đơn tối thiểu</th>
              <th class="px-lg py-md text-left text-label-md font-label-md text-on-surface-variant">Lượt dùng</th>
              <th class="px-lg py-md text-left text-label-md font-label-md text-on-surface-variant">Hạn dùng</th>
              <th class="px-lg py-md text-center text-label-md font-label-md text-on-surface-variant">Trạng thái</th>
              <th class="px-lg py-md text-right text-label-md font-label-md text-on-surface-variant">Hành động</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <tr
              v-for="coupon in coupons"
              :key="coupon.id"
              class="hover:bg-surface-container-low transition-colors group"
            >
              <td class="px-lg py-lg whitespace-nowrap">
                <span class="px-md py-xs rounded-lg bg-primary-fixed text-on-primary-fixed font-mono font-bold text-body-sm border border-primary/20">
                  {{ coupon.code }}
                </span>
              </td>
              <td class="px-lg py-lg text-body-sm font-bold text-emerald-700 whitespace-nowrap">
                <span v-if="coupon.type === 'percentage'">{{ coupon.value }}%</span>
                <span v-else>{{ formatPrice(Number(coupon.value)) }}</span>
                <span v-if="coupon.max_discount" class="text-label-sm text-on-surface-variant font-normal block">
                  Tối đa: {{ formatPrice(Number(coupon.max_discount)) }}
                </span>
              </td>
              <td class="px-lg py-lg text-body-sm text-on-surface whitespace-nowrap">
                {{ coupon.min_order_value ? formatPrice(Number(coupon.min_order_value)) : 'Không giới hạn' }}
              </td>
              <td class="px-lg py-lg text-body-sm text-on-surface whitespace-nowrap">
                <div class="flex items-center gap-xs">
                  <span class="font-bold text-on-surface">{{ coupon.used_count }}</span>
                  <span class="text-on-surface-variant">/ {{ coupon.usage_limit || '∞' }}</span>
                </div>
              </td>
              <td class="px-lg py-lg text-body-sm text-on-surface-variant whitespace-nowrap">
                {{ coupon.expires_at ? formatDate(coupon.expires_at) : 'Vô thời hạn' }}
              </td>
              <td class="px-lg py-lg text-center whitespace-nowrap">
                <button
                  @click="toggleStatus(coupon)"
                  class="px-md py-xs rounded-full text-xs font-bold cursor-pointer transition"
                  :class="coupon.is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'"
                >
                  {{ coupon.is_active ? '● Đang chạy' : '○ Tạm dừng' }}
                </button>
              </td>
              <td class="px-lg py-lg text-right whitespace-nowrap">
                <button
                  @click="deleteCoupon(coupon)"
                  class="p-xs text-on-surface-variant hover:text-error hover:bg-error-container/20 rounded-lg transition"
                  title="Xóa voucher"
                >
                  <span class="material-symbols-outlined text-[20px]">delete</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Create Coupon Modal -->
    <div
      v-if="showCreateModal"
      class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
    >
      <div class="bg-surface rounded-2xl w-full max-w-lg shadow-2xl border border-outline-variant overflow-hidden my-8">
        <div class="p-lg border-b border-outline-variant flex items-center justify-between bg-surface-container-lowest">
          <h2 class="text-headline-sm font-headline-sm font-bold text-on-surface">Tạo Mã Giảm Giá Mới</h2>
          <button
            @click="showCreateModal = false"
            class="p-xs rounded-lg text-on-surface-variant hover:bg-surface-container transition"
          >
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>

        <form @submit.prevent="submitCreateCoupon" class="p-lg space-y-md">
          <div>
            <label class="block text-label-sm font-bold uppercase tracking-wider text-on-surface-variant mb-1">Mã Voucher *</label>
            <input
              v-model="form.code"
              required
              type="text"
              placeholder="VD: TECHSHOP50"
              class="w-full px-md py-md bg-surface-container-low border border-outline-variant rounded-lg font-mono font-bold uppercase text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
            />
          </div>

          <div class="grid grid-cols-2 gap-md">
            <div>
              <label class="block text-label-sm font-bold uppercase tracking-wider text-on-surface-variant mb-1">Loại giảm giá</label>
              <select
                v-model="form.type"
                class="w-full px-md py-md bg-surface-container-low border border-outline-variant rounded-lg text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer"
              >
                <option value="fixed">Tiền mặt cố định (VNĐ)</option>
                <option value="percentage">Phần trăm (%)</option>
              </select>
            </div>
            <div>
              <label class="block text-label-sm font-bold uppercase tracking-wider text-on-surface-variant mb-1">
                {{ form.type === 'percentage' ? 'Mức giảm (%)' : 'Số tiền giảm (VNĐ)' }} *
              </label>
              <input
                v-model.number="form.value"
                required
                type="number"
                min="1"
                class="w-full px-md py-md bg-surface-container-low border border-outline-variant rounded-lg text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-md">
            <div>
              <label class="block text-label-sm font-bold uppercase tracking-wider text-on-surface-variant mb-1">Đơn tối thiểu (VNĐ)</label>
              <input
                v-model.number="form.min_order_value"
                type="number"
                min="0"
                placeholder="0"
                class="w-full px-md py-md bg-surface-container-low border border-outline-variant rounded-lg text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
              />
            </div>
            <div v-if="form.type === 'percentage'">
              <label class="block text-label-sm font-bold uppercase tracking-wider text-on-surface-variant mb-1">Giảm tối đa (VNĐ)</label>
              <input
                v-model.number="form.max_discount"
                type="number"
                min="0"
                placeholder="Không giới hạn"
                class="w-full px-md py-md bg-surface-container-low border border-outline-variant rounded-lg text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
              />
            </div>
            <div v-else>
              <label class="block text-label-sm font-bold uppercase tracking-wider text-on-surface-variant mb-1">Giới hạn lượt dùng</label>
              <input
                v-model.number="form.usage_limit"
                type="number"
                min="1"
                placeholder="Không giới hạn"
                class="w-full px-md py-md bg-surface-container-low border border-outline-variant rounded-lg text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-md">
            <div>
              <label class="block text-label-sm font-bold uppercase tracking-wider text-on-surface-variant mb-1">Ngày hết hạn</label>
              <input
                v-model="form.expires_at"
                type="date"
                class="w-full px-md py-md bg-surface-container-low border border-outline-variant rounded-lg text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
              />
            </div>
            <div class="flex items-center pt-lg">
              <label class="flex items-center gap-xs cursor-pointer text-label-md font-bold text-on-surface">
                <input
                  v-model="form.is_active"
                  type="checkbox"
                  class="rounded border-outline-variant text-primary focus:ring-primary"
                />
                Kích hoạt áp dụng ngay
              </label>
            </div>
          </div>

          <div class="pt-lg border-t border-outline-variant flex justify-end gap-sm">
            <button
              type="button"
              @click="showCreateModal = false"
              class="px-lg py-md rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface text-label-md font-bold transition"
            >
              Hủy
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="px-xl py-md rounded-lg bg-primary hover:bg-primary-container text-on-primary text-label-md font-bold transition shadow-sm disabled:opacity-50"
            >
              {{ submitting ? 'Đang tạo...' : 'Lưu Voucher' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { adminService, type Coupon } from '@/modules/admin/services/adminService'

const loading = ref(true)
const coupons = ref<Coupon[]>([])
const filterStatus = ref('all')
const searchQuery = ref('')
const showCreateModal = ref(false)
const submitting = ref(false)

const form = ref({
  code: '',
  type: 'percentage' as 'fixed' | 'percentage',
  value: 10,
  min_order_value: null as number | null,
  max_discount: null as number | null,
  usage_limit: null as number | null,
  expires_at: '',
  is_active: true,
})

async function fetchCoupons(page = 1) {
  try {
    loading.value = true
    const res = (await adminService.getCoupons({
      page,
      status: filterStatus.value,
      search: searchQuery.value,
    })) as { data: Coupon[] }
    coupons.value = res.data
  } catch (err: unknown) {
    console.error('Lỗi khi tải coupon:', err)
  } finally {
    loading.value = false
  }
}

function openCreateModal() {
  form.value = {
    code: '',
    type: 'percentage',
    value: 10,
    min_order_value: null,
    max_discount: null,
    usage_limit: null,
    expires_at: '',
    is_active: true,
  }
  showCreateModal.value = true
}

async function submitCreateCoupon() {
  try {
    submitting.value = true
    await adminService.createCoupon(form.value)
    showCreateModal.value = false
    fetchCoupons(1)
  } catch (err: unknown) {
    const errorMsg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    alert(errorMsg || 'Không thể tạo mã giảm giá!')
  } finally {
    submitting.value = false
  }
}

async function toggleStatus(coupon: Coupon) {
  try {
    await adminService.toggleCoupon(coupon.id)
    coupon.is_active = !coupon.is_active
  } catch {
    alert('Không thể đổi trạng thái coupon!')
  }
}

async function deleteCoupon(coupon: Coupon) {
  if (!confirm(`Bạn có chắc chắn muốn xóa mã "${coupon.code}"?`)) return
  try {
    await adminService.deleteCoupon(coupon.id)
    coupons.value = coupons.value.filter((c) => c.id !== coupon.id)
  } catch {
    alert('Không thể xóa mã!')
  }
}

function formatPrice(value: number): string {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(value)
}

function formatDate(dateStr: string): string {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  })
}

onMounted(() => {
  fetchCoupons(1)
})
</script>
