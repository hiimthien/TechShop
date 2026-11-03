<template>
  <div class="max-w-xl mx-auto py-16 px-4">
    <!-- Đang xử lý kiểm tra chữ ký giao dịch -->
    <div v-if="isLoading" class="bg-surface-container-lowest p-8 rounded-3xl border border-outline-variant/30 text-center space-y-4 shadow-sm">
      <div class="w-16 h-16 rounded-full bg-primary/10 text-primary mx-auto flex items-center justify-center">
        <span class="material-symbols-outlined text-3xl animate-spin">progress_activity</span>
      </div>
      <h2 class="text-lg font-black text-on-surface">Đang đối soát giao dịch VNPay...</h2>
      <p class="text-xs text-on-surface-variant">Vui lòng chờ trong giây lát, hệ thống đang kiểm tra chữ ký số bảo mật từ VNPay.</p>
    </div>

    <!-- Thanh toán thành công -->
    <div v-else-if="isSuccess" class="bg-surface-container-lowest p-8 rounded-3xl border border-primary/20 text-center space-y-5 shadow-lg shadow-primary/5">
      <div class="w-20 h-20 rounded-full bg-primary/10 text-primary mx-auto flex items-center justify-center">
        <span class="material-symbols-outlined text-4xl">verified</span>
      </div>

      <div>
        <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-black uppercase tracking-wider">
          Thanh toán thành công
        </span>
        <h1 class="text-2xl font-black text-on-surface mt-3">Giao dịch đã được ghi nhận</h1>
        <p class="text-xs text-on-surface-variant mt-1">
          Cảm ơn bạn đã mua hàng! Đơn hàng của bạn đã chuyển sang trạng thái <strong>Đang chuẩn bị hàng</strong>.
        </p>
      </div>

      <div class="p-4 rounded-2xl bg-surface-container-low text-left space-y-2 text-xs border border-outline-variant/30">
        <div class="flex justify-between py-1 border-b border-outline-variant/20">
          <span class="text-on-surface-variant">Mã đơn hàng:</span>
          <span class="font-bold text-on-surface">{{ resultData?.order_code || '#' + orderId }}</span>
        </div>
        <div class="flex justify-between py-1 border-b border-outline-variant/20">
          <span class="text-on-surface-variant">Số tiền thanh toán:</span>
          <span class="font-black text-primary">{{ formatPrice(resultData?.amount || 0) }}</span>
        </div>
        <div class="flex justify-between py-1">
          <span class="text-on-surface-variant">Mã giao dịch VNPay:</span>
          <span class="font-mono font-bold text-on-surface">{{ resultData?.transaction_no || 'N/A' }}</span>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row gap-3 pt-2">
        <RouterLink
          to="/account"
          class="flex-1 py-3 bg-primary text-on-primary font-bold rounded-xl text-xs hover:bg-primary-hover transition-all text-center"
        >
          Theo dõi đơn hàng của tôi
        </RouterLink>
        <RouterLink
          to="/products"
          class="flex-1 py-3 border border-outline-variant/50 text-on-surface-variant font-bold rounded-xl text-xs hover:bg-surface-container transition-all text-center"
        >
          Tiếp tục mua sắm
        </RouterLink>
      </div>
    </div>

    <!-- Thanh toán thất bại hoặc khách hủy -->
    <div v-else class="bg-surface-container-lowest p-8 rounded-3xl border border-error/20 text-center space-y-5 shadow-sm">
      <div class="w-20 h-20 rounded-full bg-error-container text-error mx-auto flex items-center justify-center">
        <span class="material-symbols-outlined text-4xl">cancel</span>
      </div>

      <div>
        <span class="px-3 py-1 rounded-full bg-error/10 text-error text-xs font-black uppercase tracking-wider">
          Giao dịch không thành công
        </span>
        <h1 class="text-xl font-black text-on-surface mt-3">Thanh toán chưa hoàn tất</h1>
        <p class="text-xs text-on-surface-variant mt-1">
          {{ errorMessage || 'Giao dịch qua cổng VNPay đã bị hủy hoặc gặp lỗi trong quá trình xử lý.' }}
        </p>
      </div>

      <div class="p-4 rounded-2xl bg-surface-container-low text-xs text-on-surface-variant text-left leading-relaxed">
        Đơn hàng của bạn vẫn được lưu lại ở trạng thái <strong>Chờ thanh toán</strong>. Bạn có thể vào mục Đơn hàng của tôi trong tài khoản để thử thanh toán lại hoặc chọn phương thức khác.
      </div>

      <div class="flex flex-col sm:flex-row gap-3 pt-2">
        <RouterLink
          to="/account"
          class="flex-1 py-3 bg-primary text-on-primary font-bold rounded-xl text-xs hover:bg-primary-hover transition-all text-center"
        >
          Đến trang Đơn hàng
        </RouterLink>
        <RouterLink
          to="/checkout"
          class="flex-1 py-3 border border-outline-variant/50 text-on-surface font-bold rounded-xl text-xs hover:bg-surface-container transition-all text-center"
        >
          Quay lại giỏ thanh toán
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { checkoutService } from '../services/checkoutService'
import { useFormatters } from '@/core/composables/useFormatters'

const route = useRoute()
const { formatPrice } = useFormatters()

const isLoading = ref(true)
const isSuccess = ref(false)
const errorMessage = ref('')
const orderId = ref<string>('')
const resultData = ref<{ order_code?: string; amount?: number; transaction_no?: string } | null>(null)

async function verifyPayment() {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const rawParams = route.query as Record<string, string>
    orderId.value = rawParams.vnp_TxnRef || ''

    const res = await checkoutService.handleVnpayReturn(rawParams)

    if (res.status) {
      isSuccess.value = true
      resultData.value = res.data
    } else {
      isSuccess.value = false
      errorMessage.value = res.message || 'Thanh toán không thành công.'
    }
  } catch (err: unknown) {
    console.error('Lỗi khi đối soát VNPay:', err)
    isSuccess.value = false
    const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    errorMessage.value = msg || 'Không thể xác thực giao dịch từ VNPay.'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  verifyPayment()
})
</script>

