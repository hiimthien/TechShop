<template>
  <BaseModal
    :model-value="!!order"
    @update:model-value="(val) => !val && $emit('close')"
    max-width="2xl"
    :title="order ? `Chi tiết đơn hàng ${order.order_code}` : 'Chi tiết đơn hàng'"
    :subtitle="order ? `Ngày đặt: ${order.created_at}` : ''"
    icon="receipt_long"
  >
    <div v-if="order" class="space-y-5">
      <!-- Trạng thái & Địa chỉ nhận hàng -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="p-3.5 bg-surface-container-low/50 rounded-2xl border border-outline-variant/30 space-y-1 text-xs">
          <div class="font-bold text-on-surface text-[11px] uppercase tracking-wider text-outline flex items-center gap-1">
            <span class="material-symbols-outlined text-[15px]">info</span>
            Trạng thái hiện tại
          </div>
          <div class="pt-1 flex items-center gap-2">
            <StatusBadge :status="order.status" />
            <span
              v-if="order.payment_status === 'paid'"
              class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20"
            >
              Đã thanh toán
            </span>
          </div>
        </div>

        <div class="p-3.5 bg-surface-container-low/50 rounded-2xl border border-outline-variant/30 space-y-1 text-xs">
          <div class="font-bold text-on-surface text-[11px] uppercase tracking-wider text-outline flex items-center gap-1">
            <span class="material-symbols-outlined text-[15px]">local_shipping</span>
            Người nhận & Địa chỉ
          </div>
          <div class="font-bold text-on-surface">{{ order.address?.name }} • {{ order.address?.phone }}</div>
          <div class="text-[11px] text-on-surface-variant truncate">
            {{ order.address?.address }}, {{ order.address?.city }}
          </div>
        </div>
      </div>

      <!-- Danh sách mặt hàng -->
      <div class="space-y-3">
        <h4 class="text-xs font-bold text-on-surface uppercase tracking-wider">
          Sản phẩm trong đơn ({{ order.items?.length || 0 }})
        </h4>

        <div class="space-y-2">
          <div
            v-for="item in order.items"
            :key="item.id"
            class="flex items-center gap-3 p-3 bg-surface-container-low/30 rounded-2xl border border-outline-variant/20 hover:border-outline-variant/40 transition-colors"
          >
            <img
              :src="item.product?.image || 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300'"
              :alt="item.product?.name"
              class="w-14 h-14 object-cover rounded-xl border border-outline-variant/30 bg-surface shrink-0"
            />
            <div class="flex-1 min-w-0 text-xs">
              <div class="font-bold text-on-surface truncate">{{ item.product?.name }}</div>
              <div class="text-[11px] text-outline mt-0.5">
                SL: {{ item.quantity }} × {{ formatPrice(item.price) }}
              </div>
              <!-- Serial / IMEI -->
              <div v-if="item.serial_number" class="text-[11px] text-primary font-mono font-bold mt-0.5 flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">qr_code</span>
                Serial / IMEI: {{ item.serial_number }}
              </div>
              <button
                v-if="order.status === 'delivered'"
                type="button"
                @click="$emit('request-warranty', item)"
                class="text-[11px] text-primary hover:underline font-bold mt-1 inline-flex items-center gap-1"
              >
                <span class="material-symbols-outlined text-[13px]">build</span>
                <span>Yêu cầu bảo hành máy này</span>
              </button>
            </div>
            <div class="text-xs font-bold text-primary shrink-0">
              {{ formatPrice(item.line_total) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Thông tin phương thức thanh toán -->
      <div class="p-3.5 bg-surface-container-low/40 rounded-2xl border border-outline-variant/30 space-y-1.5 text-xs">
        <div class="font-bold text-on-surface text-[11px] uppercase tracking-wider text-outline flex items-center gap-1">
          <span class="material-symbols-outlined text-[15px]">payments</span>
          Thông tin thanh toán
        </div>
        <div class="flex justify-between items-center pt-1">
          <span class="text-on-surface-variant">Phương thức:</span>
          <span class="font-bold text-on-surface">
            {{ formatPaymentMethod(order.payment_method) }}
          </span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-on-surface-variant">Trạng thái thanh toán:</span>
          <span
            :class="[
              'font-bold text-xs',
              order.payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600'
            ]"
          >
            {{ formatPaymentStatus(order.payment_status) }}
          </span>
        </div>
        <div v-if="order.payment_transaction_id" class="flex justify-between items-center">
          <span class="text-on-surface-variant">Mã giao dịch:</span>
          <span class="font-mono text-on-surface font-semibold">{{ order.payment_transaction_id }}</span>
        </div>
      </div>

      <!-- Tổng tiền & Chi tiết thanh toán -->
      <div class="border-t border-outline-variant/20 pt-4 space-y-2 text-xs">
        <div class="flex justify-between text-on-surface-variant">
          <span>Tạm tính:</span>
          <span class="font-semibold">{{ formatPrice(order.total_price) }}</span>
        </div>
        <div class="flex justify-between text-on-surface-variant">
          <span>Phí vận chuyển:</span>
          <span class="font-semibold">{{ formatPrice(order.shipping_fee) }}</span>
        </div>
        <div v-if="order.discount_amount > 0" class="flex justify-between text-emerald-600 font-semibold">
          <span>Giảm giá voucher:</span>
          <span>-{{ formatPrice(order.discount_amount) }}</span>
        </div>
        <div class="flex justify-between text-base font-black text-primary border-t border-outline-variant/20 pt-3">
          <span>Tổng thanh toán:</span>
          <span>{{ formatPrice(order.final_total) }}</span>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex items-center justify-between w-full">
        <!-- Nút Thanh toán ngay nếu chưa trả qua VNPay -->
        <div>
          <button
            v-if="order && order.payment_method === 'vnpay' && order.payment_status !== 'paid' && order.status === 'pending'"
            type="button"
            :disabled="isPaying"
            @click="payNow"
            class="px-4 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-xl hover:bg-blue-700 shadow-sm transition-colors flex items-center gap-1.5 disabled:opacity-60"
          >
            <span v-if="isPaying" class="material-symbols-outlined text-sm animate-spin">progress_activity</span>
            <span v-else class="material-symbols-outlined text-sm">credit_card</span>
            <span>{{ isPaying ? 'Đang chuyển hướng...' : 'Thanh toán ngay qua VNPay' }}</span>
          </button>
        </div>

        <button
          type="button"
          @click="$emit('close')"
          class="px-5 py-2.5 bg-surface-container-high text-on-surface text-xs font-bold rounded-xl hover:bg-surface-container-highest transition-colors"
        >
          Đóng
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import type { CustomerOrder, OrderItemDetail } from '../services/accountService'
import { checkoutService } from '@/modules/checkout/services/checkoutService'
import { useFormatters } from '@/core/composables/useFormatters'
import BaseModal from '@/core/components/ui/BaseModal.vue'
import StatusBadge from '@/core/components/ui/StatusBadge.vue'

interface Props {
  order: CustomerOrder | null
}

const props = defineProps<Props>()
defineEmits<{
  (e: 'close'): void
  (e: 'request-warranty', item: OrderItemDetail): void
}>()

const { formatPrice } = useFormatters()
const isPaying = ref(false)

function formatPaymentMethod(method?: string): string {
  if (method === 'vnpay') return 'Cổng thanh toán VNPAY'
  if (method === 'bank_transfer') return 'Chuyển khoản VietQR'
  return 'Thanh toán khi nhận hàng (COD)'
}

function formatPaymentStatus(status?: string): string {
  if (status === 'paid') return 'Đã thanh toán'
  if (status === 'pending') return 'Chờ thanh toán'
  if (status === 'failed') return 'Giao dịch thất bại'
  return 'Chưa thanh toán'
}

async function payNow() {
  if (!props.order) return
  isPaying.value = true
  try {
    const data = await checkoutService.createVnpayPaymentUrl(props.order.id)
    if (data?.payment_url) {
      window.location.href = data.payment_url
    }
  } catch (err: unknown) {
    console.error('Lỗi khi mở cổng thanh toán VNPay:', err)
    const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    alert(msg || 'Không thể kết nối cổng VNPay lúc này. Vui lòng thử lại sau.')
  } finally {
    isPaying.value = false
  }
}
</script>
