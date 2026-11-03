<template>
  <section class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/30">
    <div class="flex items-center justify-between mb-6 pb-3 border-b border-outline-variant/20">
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-2xl">local_shipping</span>
        <h2 class="text-lg font-black text-on-surface">Thông tin nhận hàng</h2>
      </div>
      <button
        v-if="savedAddresses.length > 0 && !showNewAddressForm"
        type="button"
        @click="$emit('update:showNewAddressForm', true)"
        class="text-xs text-primary font-bold hover:underline flex items-center gap-1"
      >
        <span class="material-symbols-outlined text-sm">add</span>
        Thêm địa chỉ mới
      </button>
      <button
        v-if="showNewAddressForm && savedAddresses.length > 0"
        type="button"
        @click="$emit('update:showNewAddressForm', false)"
        class="text-xs text-outline hover:text-on-surface font-medium"
      >
        Chọn từ danh sách đã lưu
      </button>
    </div>

    <!-- List Saved Addresses -->
    <div v-if="savedAddresses.length > 0 && !showNewAddressForm" class="flex flex-col gap-3">
      <label
        v-for="addr in savedAddresses"
        :key="addr.id"
        class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all hover:bg-surface-container-low"
        :class="selectedAddressId === addr.id ? 'border-2 border-primary bg-primary/5 shadow-sm' : 'border-outline-variant/50'"
      >
        <input
          type="radio"
          name="saved_address"
          :value="addr.id"
          :checked="selectedAddressId === addr.id"
          @change="$emit('update:selectedAddressId', addr.id)"
          class="mt-1 text-primary focus:ring-primary"
        />
        <div class="flex-grow text-xs md:text-sm">
          <div class="flex items-center gap-2">
            <span class="font-bold text-on-surface">{{ addr.name }}</span>
            <span class="text-outline font-medium">• {{ addr.phone }}</span>
            <span v-if="addr.is_default" class="bg-primary/10 text-primary text-[10px] font-bold px-1.5 py-0.5 rounded">Mặc định</span>
          </div>
          <p class="text-on-surface-variant mt-1 leading-relaxed">
            {{ addr.address }}, {{ addr.city }}
          </p>
        </div>
      </label>
    </div>

    <!-- New Address Form -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="space-y-1">
        <label class="text-xs font-semibold text-on-surface-variant">Họ và tên người nhận *</label>
        <input
          v-model="newAddress.name"
          type="text"
          placeholder="Nguyễn Văn A"
          class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 text-sm focus:outline-none focus:border-primary bg-surface-container-low"
        />
      </div>
      <div class="space-y-1">
        <label class="text-xs font-semibold text-on-surface-variant">Số điện thoại *</label>
        <input
          v-model="newAddress.phone"
          type="tel"
          placeholder="0901 234 567"
          class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 text-sm focus:outline-none focus:border-primary bg-surface-container-low"
        />
      </div>
      <div class="space-y-1 md:col-span-2">
        <label class="text-xs font-semibold text-on-surface-variant">Tỉnh / Thành phố *</label>
        <input
          v-model="newAddress.city"
          type="text"
          placeholder="Ví dụ: TP. Hồ Chí Minh hoặc Hà Nội"
          class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 text-sm focus:outline-none focus:border-primary bg-surface-container-low"
        />
      </div>
      <div class="space-y-1 md:col-span-2">
        <label class="text-xs font-semibold text-on-surface-variant">Địa chỉ chi tiết (Số nhà, đường, phường/xã) *</label>
        <textarea
          v-model="newAddress.address"
          rows="2"
          placeholder="Ví dụ: 123 Lê Lợi, Phường Bến Nghé, Quận 1"
          class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 text-sm focus:outline-none focus:border-primary bg-surface-container-low"
        ></textarea>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import type { UserAddress } from '../services/checkoutService'

export interface NewAddressForm {
  name: string
  phone: string
  address: string
  city: string
}

interface Props {
  savedAddresses: UserAddress[]
  selectedAddressId: number | null
  showNewAddressForm: boolean
  newAddress: NewAddressForm
}

defineProps<Props>()
defineEmits<{
  (e: 'update:selectedAddressId', val: number): void
  (e: 'update:showNewAddressForm', val: boolean): void
}>()
</script>

