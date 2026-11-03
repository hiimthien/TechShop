<template>
  <div class="flex flex-col sm:flex-row gap-4 p-4 md:p-6 bg-surface-container-lowest rounded-2xl shadow-xs border border-outline-variant/30 transition-all hover:shadow-md">
    <!-- Thumbnail -->
    <div class="w-full sm:w-28 h-28 bg-white rounded-xl overflow-hidden shrink-0 border border-outline-variant/20 p-2 flex items-center justify-center">
      <img
        :src="item.thumbnail || fallbackImage"
        :alt="item.product_name"
        class="w-full h-full object-contain"
      />
    </div>

    <!-- Item Details -->
    <div class="flex-grow flex flex-col justify-between py-1">
      <div>
        <div class="flex justify-between items-start gap-2">
          <RouterLink
            :to="`/products/${item.product_slug}`"
            class="text-base font-bold text-on-surface hover:text-primary transition-colors line-clamp-2"
          >
            {{ item.product_name }}
          </RouterLink>
          <button
            type="button"
            @click="$emit('remove')"
            class="text-outline hover:text-error transition-colors p-1 cursor-pointer"
            title="Xóa khỏi giỏ hàng"
          >
            <span class="material-symbols-outlined text-[20px]">delete</span>
          </button>
        </div>

        <p class="text-xs text-on-surface-variant mt-1">
          Phiên bản: <span class="font-medium text-on-surface">{{ item.variant_option || item.variant_sku }}</span>
        </p>
      </div>

      <div class="flex flex-wrap justify-between items-end gap-3 mt-4 pt-3 border-t border-outline-variant/20">
        <!-- Quantity Controls -->
        <div class="flex items-center bg-surface-container rounded-lg p-1">
          <button
            type="button"
            @click="$emit('update-quantity', item.quantity - 1)"
            class="w-7 h-7 flex items-center justify-center hover:bg-surface-container-high rounded transition-colors text-on-surface cursor-pointer"
            aria-label="Giảm số lượng"
          >
            <span class="material-symbols-outlined text-sm">remove</span>
          </button>
          <input
            type="number"
            :value="item.quantity"
            @change="handleInput"
            min="1"
            class="w-10 bg-transparent border-none text-center focus:outline-none font-bold text-xs"
          />
          <button
            type="button"
            @click="$emit('update-quantity', item.quantity + 1)"
            class="w-7 h-7 flex items-center justify-center hover:bg-surface-container-high rounded transition-colors text-on-surface cursor-pointer"
            aria-label="Tăng số lượng"
          >
            <span class="material-symbols-outlined text-sm">add</span>
          </button>
        </div>

        <!-- Price -->
        <div class="text-right">
          <span class="text-lg font-black text-primary">
            {{ formatPrice(item.price * item.quantity) }}
          </span>
          <span v-if="item.quantity > 1" class="block text-[11px] text-on-surface-variant">
            ({{ formatPrice(item.price) }}/cái)
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { useFormatters } from '@/core/composables/useFormatters'
import type { CartItem } from '../types'

interface Props {
  item: CartItem
}

defineProps<Props>()
const emit = defineEmits<{
  (e: 'update-quantity', val: number): void
  (e: 'remove'): void
}>()

const { formatPrice } = useFormatters()
const fallbackImage =
  'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect fill="%23f0eded" width="200" height="200"/><text fill="%237b7487" font-family="sans-serif" font-size="14" dy="5" font-weight="bold" x="50%" y="50%" text-anchor="middle">TechShop</text></svg>'

function handleInput(event: Event) {
  const target = event.target as HTMLInputElement
  const val = parseInt(target.value, 10)
  if (!isNaN(val) && val > 0) {
    emit('update-quantity', val)
  }
}
</script>
