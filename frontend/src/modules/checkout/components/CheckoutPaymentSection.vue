<template>
  <section class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/30">
    <div class="flex items-center gap-2 mb-6 pb-3 border-b border-outline-variant/20">
      <span class="material-symbols-outlined text-primary text-2xl">account_balance_wallet</span>
      <h2 class="text-lg font-black text-on-surface">Phương thức thanh toán</h2>
    </div>

    <div class="space-y-3">
      <!-- VNPAY (Khuyên dùng) -->
      <label
        class="flex items-center p-4 rounded-xl border cursor-pointer transition-all hover:bg-surface-container-low"
        :class="modelValue === 'vnpay' ? 'border-2 border-primary bg-primary/5 shadow-sm' : 'border-outline-variant/50'"
      >
        <input
          type="radio"
          name="payment_method"
          value="vnpay"
          :checked="modelValue === 'vnpay'"
          @change="$emit('update:modelValue', 'vnpay')"
          class="text-primary focus:ring-primary w-4 h-4"
        />
        <div class="ml-3 flex flex-col">
          <div class="flex items-center gap-2">
            <span class="text-sm font-bold text-on-surface">Cổng thanh toán VNPAY</span>
            <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary text-[10px] font-black uppercase">Khuyên dùng</span>
          </div>
          <span class="text-xs text-on-surface-variant">Thẻ ATM nội địa, Visa/Mastercard, VNPay-QR bảo mật cao</span>
        </div>
        <span class="ml-auto px-2 py-1 bg-[#005baa] text-white text-[10px] font-black rounded tracking-wider">VNPAY</span>
      </label>

      <!-- COD -->
      <label
        class="flex items-center p-4 rounded-xl border cursor-pointer transition-all hover:bg-surface-container-low"
        :class="modelValue === 'cod' ? 'border-2 border-primary bg-primary/5 shadow-sm' : 'border-outline-variant/50'"
      >
        <input
          type="radio"
          name="payment_method"
          value="cod"
          :checked="modelValue === 'cod'"
          @change="$emit('update:modelValue', 'cod')"
          class="text-primary focus:ring-primary w-4 h-4"
        />
        <div class="ml-3 flex flex-col">
          <span class="text-sm font-bold text-on-surface">Thanh toán khi nhận hàng (COD)</span>
          <span class="text-xs text-on-surface-variant">Thanh toán tiền mặt cho nhân viên giao hàng khi nhận kiện hàng</span>
        </div>
        <span class="material-symbols-outlined ml-auto text-primary text-2xl">payments</span>
      </label>

      <!-- VietQR Transfer -->
      <label
        class="flex items-center p-4 rounded-xl border cursor-pointer transition-all hover:bg-surface-container-low"
        :class="modelValue === 'bank_transfer' ? 'border-2 border-primary bg-primary/5 shadow-sm' : 'border-outline-variant/50'"
      >
        <input
          type="radio"
          name="payment_method"
          value="bank_transfer"
          :checked="modelValue === 'bank_transfer'"
          @change="$emit('update:modelValue', 'bank_transfer')"
          class="text-primary focus:ring-primary w-4 h-4"
        />
        <div class="ml-3 flex flex-col">
          <span class="text-sm font-bold text-on-surface">Chuyển khoản trực tiếp VietQR</span>
          <span class="text-xs text-on-surface-variant">Quét mã QR qua app ngân hàng (Hỗ trợ 40+ ngân hàng Việt Nam)</span>
        </div>
        <span class="material-symbols-outlined ml-auto text-secondary text-2xl">qr_code_2</span>
      </label>
    </div>
  </section>
</template>

<script setup lang="ts">
interface Props {
  modelValue: string
}

defineProps<Props>()
defineEmits<{
  (e: 'update:modelValue', val: string): void
}>()
</script>
