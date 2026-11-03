<template>
  <div class="max-w-[800px] mx-auto py-8">
    <section class="text-center mb-10">
      <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary-container text-on-primary mb-4 shadow-lg ring-8 ring-primary-fixed/20">
        <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
      </div>
      <h1 class="text-2xl md:text-4xl font-black text-on-surface mb-2">Đặt hàng thành công!</h1>
      <p class="text-sm md:text-base text-on-surface-variant max-w-md mx-auto">
        Cảm ơn bạn đã mua sắm tại TechShop. Đơn hàng của bạn đang được nhân viên chuẩn bị và sẽ sớm được giao đến bạn.
      </p>
    </section>

    <div class="bg-surface-container-lowest p-6 md:p-8 rounded-2xl shadow-sm border border-outline-variant/30 space-y-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-outline-variant/20">
        <div>
          <span class="text-xs uppercase tracking-wider text-outline block">Mã đơn hàng</span>
          <span class="text-xl font-black text-primary">{{ orderNumber }}</span>
        </div>
        <div class="sm:text-right">
          <span class="text-xs uppercase tracking-wider text-outline block">Dự kiến giao hàng</span>
          <span class="text-base font-bold text-on-surface">2 - 3 ngày làm việc</span>
        </div>
      </div>

      <!-- Progress bar -->
      <div class="space-y-3">
        <div class="flex justify-between text-xs font-semibold">
          <span class="text-primary font-bold">1. Đã đặt hàng</span>
          <span class="text-on-surface-variant">2. Đang chuẩn bị</span>
          <span class="text-outline">3. Đang giao</span>
          <span class="text-outline">4. Đã nhận</span>
        </div>
        <div class="w-full h-2 bg-surface-container-highest rounded-full overflow-hidden">
          <div class="h-full bg-primary w-1/4 rounded-full transition-all duration-500"></div>
        </div>
      </div>

      <!-- Shipping detail -->
      <div class="pt-4 border-t border-outline-variant/20 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs md:text-sm">
        <div>
          <h4 class="font-bold text-on-surface mb-1">Địa chỉ nhận hàng</h4>
          <p class="text-on-surface-variant leading-relaxed">
            <strong>{{ shippingInfo.name }}</strong> ({{ shippingInfo.phone }})<br />
            {{ shippingInfo.address }}, {{ shippingInfo.city }}
          </p>
        </div>
        <div>
          <h4 class="font-bold text-on-surface mb-1">Phương thức thanh toán</h4>
          <p class="text-on-surface-variant leading-relaxed">
            {{ paymentMethodLabel }}<br />
            <span class="text-primary font-bold">Tổng thanh toán: {{ formatPrice(totalAmount) }}</span>
          </p>
        </div>
      </div>
    </div>

    <div class="mt-8 flex justify-center gap-4">
      <RouterLink
        to="/products"
        class="px-8 py-3.5 bg-primary text-on-primary font-bold rounded-xl text-sm hover:bg-primary-hover transition-all shadow-md active:scale-95"
      >
        Tiếp tục mua sắm
      </RouterLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useFormatters } from '@/core/composables/useFormatters'

interface ShippingInfo {
  name: string
  phone: string
  address: string
  city: string
}

interface Props {
  orderNumber: string
  shippingInfo: ShippingInfo
  paymentMethodLabel: string
  totalAmount: number
}

defineProps<Props>()
const { formatPrice } = useFormatters()
</script>

