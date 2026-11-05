<template>
  <div class="max-w-xl mx-auto py-16 px-4">
    <!-- Đang xử lý kiểm tra chữ ký giao dịch -->
    <div v-if="isLoading" class="bg-surface-container-lowest p-8 rounded-3xl border border-outline-variant/30 text-center space-y-4 shadow-sm">
      <div class="w-16 h-16 rounded-full bg-primary/10 text-primary mx-auto flex items-center justify-center">
        <span class="material-symbols-outlined text-3xl animate-spin">progress_activity</span>
      </div>
      <h2 class="text-lg font-black text-on-surface">Đang đối soát giao dịch VNPay...</h2>
      <p class="text-xs text-on-surface-variant">Vui lòng chờ trong giây lát, hệ thống đang kiểm tra chữ ký số bảo mật từ cổng thanh toán VNPay.</p>
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
          Cảm ơn bạn đã mua sắm tại TechShop! Đơn hàng của bạn đã chuyển sang trạng thái <strong>Đang chuẩn bị hàng</strong>.
        </p>
      </div>

      <div class="p-4 rounded-2xl bg-surface-container-low text-left space-y-2 text-xs border border-outline-variant/30">
        <div class="flex justify-between py-1 border-b border-outline-variant/20">
          <span class="text-on-surface-variant">Mã đơn hàng:</span>
          <span class="font-bold text-on-surface">{{ resultData?.order_code || (orderId ? '#' + orderId : 'N/A') }}</span>
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
          Giao dịch chưa hoàn tất
        </span>
        <h1 class="text-xl font-black text-on-surface mt-3">Thanh toán không thành công</h1>
        <p class="text-xs text-error font-medium mt-1">
          {{ errorMessage || 'Giao dịch qua cổng VNPay đã bị hủy hoặc gặp lỗi trong quá trình xử lý.' }}
        </p>
      </div>

      <!-- Tóm tắt đơn hàng nếu có mã đơn -->
      <div v-if="orderId || resultData?.order_code" class="p-4 rounded-2xl bg-surface-container-low text-left space-y-2 text-xs border border-outline-variant/30">
        <div class="flex justify-between py-1">
          <span class="text-on-surface-variant">Mã đơn hàng liên quan:</span>
          <span class="font-bold text-on-surface">{{ resultData?.order_code || ('#TS-' + String(orderId).padStart(6, '0')) }}</span>
        </div>
        <p class="text-[11px] text-on-surface-variant leading-relaxed">
          Đơn hàng của bạn vẫn được lưu giữ an toàn ở trạng thái <strong>Chờ thanh toán</strong>. Bạn có thể nhấn thử lại bên dưới để tiếp tục thanh toán qua VNPay.
        </p>
      </div>

      <div v-else class="p-4 rounded-2xl bg-surface-container-low text-xs text-on-surface-variant text-left leading-relaxed">
        Đơn hàng của bạn vẫn được lưu lại ở trạng thái <strong>Chờ thanh toán</strong>. Bạn có thể vào mục Đơn hàng của tôi trong tài khoản để thử thanh toán lại hoặc chọn phương thức khác.
      </div>

      <div class="flex flex-col sm:flex-row gap-3 pt-2">
        <!-- Nút Thử thanh toán lại VNPay -->
        <button
          v-if="orderId"
          type="button"
          :disabled="isRetrying"
          @click="retryPayment"
          class="flex-1 py-3 bg-primary text-on-primary font-bold rounded-xl text-xs hover:bg-primary-hover transition-all text-center flex items-center justify-center gap-1.5 shadow-sm disabled:opacity-60"
        >
          <span v-if="isRetrying" class="material-symbols-outlined text-sm animate-spin">progress_activity</span>
          <span v-else class="material-symbols-outlined text-sm">refresh</span>
          <span>{{ isRetrying ? 'Đang chuyển hướng...' : 'Thử thanh toán lại' }}</span>
        </button>

        <RouterLink
          to="/account"
          class="flex-1 py-3 border border-outline-variant/50 text-on-surface font-bold rounded-xl text-xs hover:bg-surface-container transition-all text-center flex items-center justify-center"
        >
          Đến trang Đơn hàng
        </RouterLink>

        <RouterLink
          to="/products"
          class="flex-1 py-3 border border-outline-variant/50 text-on-surface-variant font-bold rounded-xl text-xs hover:bg-surface-container transition-all text-center flex items-center justify-center"
        >
          Tiếp tục xem sản phẩm
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
const isRetrying = ref(false)
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
      resultData.value = res.data || null
    } else {
      isSuccess.value = false
      errorMessage.value = res.message || 'Thanh toán không thành công.'
      if (res.data) {
        resultData.value = res.data
      }
    }
  } catch (err: unknown) {
    console.error('Lỗi khi đối soát VNPay:', err)
    isSuccess.value = false
    const errResponse = (err as { response?: { data?: { message?: string; data?: { order_code?: string; response_code?: string } } } })?.response?.data
    errorMessage.value = errResponse?.message || 'Không thể xác thực giao dịch từ VNPay.'
    if (errResponse?.data) {
      resultData.value = errResponse.data
    }
  } finally {
    isLoading.value = false
  }
}

async function retryPayment() {
  const numericOrderId = Number(orderId.value)
  if (!numericOrderId) return

  isRetrying.value = true
  try {
    const data = await checkoutService.createVnpayPaymentUrl(numericOrderId)
    if (data?.payment_url) {
      window.location.href = data.payment_url
    }
  } catch (err: unknown) {
    console.error('Lỗi khi khởi tạo lại liên kết VNPay:', err)
    const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    alert(msg || 'Không thể khởi tạo lại giao dịch VNPay. Vui lòng thử lại sau.')
  } finally {
    isRetrying.value = false
  }
}

onMounted(() => {
  verifyPayment()
})
</script>
