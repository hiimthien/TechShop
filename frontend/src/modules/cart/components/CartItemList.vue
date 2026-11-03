<template>
  <div class="flex flex-col gap-4">
    <!-- List of rows -->
    <CartItemRow
      v-for="item in items"
      :key="item.variant_id"
      :item="item"
      @update-quantity="$emit('update-quantity', { id: item.id, quantity: $event })"
      @remove="$emit('remove-item', item.id)"
    />

    <!-- Back to shopping & Clear Cart -->
    <div class="flex justify-between items-center pt-2">
      <RouterLink
        to="/products"
        class="text-primary hover:underline text-sm font-bold flex items-center gap-1"
      >
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        <span>Tiếp tục mua sắm</span>
      </RouterLink>
      <button
        type="button"
        @click="$emit('clear-cart')"
        class="text-xs text-outline hover:text-error transition-colors cursor-pointer"
      >
        Xóa toàn bộ giỏ hàng
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
import CartItemRow from './CartItemRow.vue'
import type { CartItem } from '../types'

interface Props {
  items: CartItem[]
}

defineProps<Props>()
defineEmits<{
  (e: 'update-quantity', payload: { id: number; quantity: number }): void
  (e: 'remove-item', id: number): void
  (e: 'clear-cart'): void
}>()
</script>
