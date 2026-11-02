<template>
  <BaseModal
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    :title="order ? `Chi tiết đơn hàng #${order.id}` : 'Chi tiết đơn hàng'"
    max-width="max-w-3xl"
  >
    <div v-if="order" class="space-y-5">
      <!-- Status Updater Box -->
      <div class="p-4 rounded-xl bg-primary-fixed text-on-primary-fixed flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <div class="text-xs font-bold uppercase tracking-wider">Cập Nhật Trạng Thái Giao Hàng</div>
          <p class="text-[11px] opacity-90">Thay đổi trạng thái để khách hàng và kho hàng theo dõi tiến độ</p>
        </div>
        <div class="flex items-center gap-2">
          <select
            v-model="newStatus"
            class="bg-surface border border-outline rounded-lg px-3 py-1.5 text-xs font-bold text-on-surface focus:outline-none focus:ring-2 focus:ring-primary cursor-pointer"
          >
            <option value="pending">Chờ xác nhận (Pending)</option>
            <option value="processing">Đang đóng gói (Processing)</option>
            <option value="shipped">Đang giao hàng (Shipped)</option>
            <option value="delivered">Đã giao thành công (Delivered)</option>
            <option value="cancelled">Hủy đơn (Cancelled)</option>
          </select>
          <button
            @click="handleUpdateStatus"
            :disabled="updatingStatus"
            class="px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary text-xs font-bold transition shadow-sm disabled:opacity-50 inline-flex items-center gap-1.5"
          >
            <span v-if="updatingStatus" class="material-symbols-outlined text-[14px] animate-spin">sync</span>
            <span>{{ updatingStatus ? 'Lưu...' : 'Cập nhật' }}</span>
          </button>
        </div>
      </div>

      <!-- Customer & Shipping info -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
        <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-outline-variant/40">
          <div class="text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">
            Thông Tin Khách Hàng
          </div>
          <div class="text-xs font-bold text-on-surface">{{ order.user?.name || order.address?.name }}</div>
          <div class="text-xs text-on-surface-variant mt-1">Email: {{ order.user?.email || '-' }}</div>
          <div class="text-xs text-on-surface-variant mt-0.5">SĐT: {{ order.user?.phone || order.address?.phone }}</div>
        </div>

        <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-outline-variant/40">
          <div class="text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">
            Địa Chỉ Giao Hàng
          </div>
          <div class="text-xs font-bold text-on-surface">
            {{ order.address?.name }} ({{ order.address?.phone }})
          </div>
          <div class="text-xs text-on-surface-variant mt-1">
            {{ order.address?.address }}, {{ order.address?.city }}
          </div>
          <div v-if="order.ghn_order_code" class="text-xs text-primary font-mono font-bold mt-1">
            Vận đơn GHN: {{ order.ghn_order_code }}
          </div>
        </div>
      </div>

      <!-- Products Table -->
      <div>
        <div class="text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-2">
          Sản Phẩm Trong Đơn
        </div>
        <div class="rounded-xl border border-outline-variant/40 overflow-hidden bg-surface-container-lowest">
          <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-surface-container-low text-on-surface-variant border-b border-outline-variant/40 font-bold uppercase">
              <tr>
                <th class="py-2.5 px-3">Sản phẩm</th>
                <th class="py-2.5 px-3 text-center">Số lượng</th>
                <th class="py-2.5 px-3 text-right">Đơn giá</th>
                <th class="py-2.5 px-3 text-right">Thành tiền</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/20">
              <tr v-for="item in order.items" :key="item.id">
                <td class="py-2.5 px-3 flex items-center gap-3">
                  <img
                    :src="item.variant?.product?.images?.[0]?.image_url || 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=100'"
                    class="w-9 h-9 object-contain rounded-lg bg-surface-container p-0.5 shrink-0"
                    alt=""
                  />
                  <div class="min-w-0">
                    <div class="font-bold text-on-surface truncate">{{ item.variant?.product?.name || 'Sản phẩm' }}</div>
                    <div class="text-[10px] text-on-surface-variant font-mono">
                      SKU: {{ item.variant?.sku || '-' }}
                    </div>
                  </div>
                </td>
                <td class="py-2.5 px-3 text-center font-bold text-on-surface">{{ item.quantity }}</td>
                <td class="py-2.5 px-3 text-right text-on-surface-variant">{{ formatPrice(Number(item.price)) }}</td>
                <td class="py-2.5 px-3 text-right font-bold text-primary">
                  {{ formatPrice(Number(item.price) * item.quantity) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Financial Calculation -->
      <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-outline-variant/40 space-y-2 text-xs">
        <div class="flex items-center justify-between text-on-surface-variant">
          <span>Phí vận chuyển:</span>
          <span class="text-on-surface font-medium">{{ formatPrice(Number(order.shipping_fee || 0)) }}</span>
        </div>
        <div v-if="order.discount_amount" class="flex items-center justify-between text-on-surface-variant">
          <span>Giảm giá (Coupon {{ order.coupon?.code || '' }}):</span>
          <span class="text-emerald-700 font-bold">-{{ formatPrice(Number(order.discount_amount)) }}</span>
        </div>
        <div class="flex items-center justify-between text-sm font-bold text-on-surface pt-2 border-t border-outline-variant/40">
          <span>Tổng thanh toán:</span>
          <span class="text-base text-primary font-black">{{ formatPrice(Number(order.total_price)) }}</span>
        </div>
      </div>
    </div>

    <template #footer>
      <button
        type="button"
        @click="$emit('update:modelValue', false)"
        class="px-4 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-bold transition"
      >
        Đóng
      </button>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import BaseModal from '@/core/components/BaseModal.vue'
import { adminService, type Order } from '../services/adminService'

interface Props {
  modelValue: boolean
  order: Order | null
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'update:modelValue', val: boolean): void
  (e: 'status-updated'): void
}>()

const newStatus = ref('pending')
const updatingStatus = ref(false)

watch(
  () => props.order,
  (ord) => {
    if (ord) {
      newStatus.value = ord.status
    }
  },
  { immediate: true }
)

function formatPrice(val: number) {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}

async function handleUpdateStatus() {
  if (!props.order) return
  updatingStatus.value = true
  try {
    await adminService.updateOrderStatus(props.order.id, newStatus.value)
    emit('status-updated')
  } catch (err: unknown) {
    const errorMsg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    alert(errorMsg || 'Không thể cập nhật trạng thái đơn hàng')
  } finally {
    updatingStatus.value = false
  }
}
</script>

