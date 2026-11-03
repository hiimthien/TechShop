<template>
  <div class="space-y-4">
    <!-- 1. Bộ chọn Phiên bản Bộ nhớ (ROM / Cấu hình) -->
    <div v-if="storageOptions.length > 0" class="space-y-2">
      <span class="text-xs font-bold text-on-surface uppercase tracking-wider block">
        1. Chọn phiên bản bộ nhớ:
      </span>
      <div class="flex flex-wrap gap-2.5">
        <button
          v-for="st in storageOptions"
          :key="st"
          type="button"
          @click="$emit('update:selectedStorage', st)"
          class="px-4 py-2 rounded-xl text-xs font-bold border transition-all relative flex items-center justify-center min-w-[80px]"
          :class="
            selectedStorage === st
              ? 'border-2 border-primary bg-primary/10 text-primary shadow-xs'
              : 'border-outline-variant/60 hover:border-primary/50 text-on-surface bg-surface-container-lowest'
          "
        >
          {{ st }}
          <span
            v-if="selectedStorage === st"
            class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-primary text-on-primary rounded-full flex items-center justify-center text-[10px]"
          >
            ✓
          </span>
        </button>
      </div>
    </div>

    <!-- 2. Bộ chọn Màu sắc (kèm thumbnail & giá từng màu) -->
    <div v-if="variants.length > 0" class="space-y-2">
      <div class="flex justify-between items-center text-xs">
        <span class="font-bold text-on-surface uppercase tracking-wider">
          2. Chọn màu sắc yêu thích:
        </span>
        <span class="text-on-surface-variant font-medium">
          Màu đang chọn: <strong class="text-primary">{{ activeColorName }}</strong>
        </span>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
        <button
          v-for="v in variants"
          :key="v.id"
          type="button"
          @click="$emit('select-variant', v)"
          class="p-2.5 rounded-xl border text-left transition-all text-xs flex items-center gap-2.5 relative bg-surface-container-lowest"
          :class="
            selectedVariantId === v.id
              ? 'border-2 border-primary bg-primary/5 shadow-xs'
              : 'border-outline-variant/60 hover:border-primary/50'
          "
        >
          <img
            v-if="v.image_url"
            :src="v.image_url"
            class="w-10 h-10 object-contain rounded-lg border border-outline-variant/40 p-0.5 shrink-0 bg-white"
            :alt="v.sku"
          />
          <div class="flex-1 min-w-0">
            <span class="font-bold text-on-surface block truncate text-[11px]">
              {{ getColorName(v) }}
            </span>
            <span class="text-xs font-black text-primary block mt-0.5">
              {{ formatPrice(v.price) }}
            </span>
          </div>
          <!-- Checkmark icon khi active -->
          <span
            v-if="selectedVariantId === v.id"
            class="absolute top-1 right-1 w-3.5 h-3.5 bg-primary text-on-primary rounded-full flex items-center justify-center text-[9px] font-bold"
          >
            ✓
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { ProductVariant } from '../types'
import { useFormatters } from '@/core/composables/useFormatters'

interface Props {
  storageOptions: string[]
  selectedStorage: string
  variants: ProductVariant[]
  selectedVariantId?: number
  activeColorName: string
}

defineProps<Props>()
defineEmits<{
  (e: 'update:selectedStorage', val: string): void
  (e: 'select-variant', val: ProductVariant): void
}>()

const { formatPrice } = useFormatters()

function getColorName(v: ProductVariant): string {
  const raw = v.options?.[0] || v.sku || ''
  if (raw.includes('-')) {
    const parts = raw.split('-')
    const first = parts[0]?.trim()
    if (first) return first
  }
  return (
    raw
      .replace(/\b\d+\s*(GB|TB)(\s*RAM)?\b/gi, '')
      .replace(/\/\s*RAM\b/gi, '')
      .replace(/[-_]/g, ' ')
      .trim() || 'Mặc định'
  )
}
</script>
