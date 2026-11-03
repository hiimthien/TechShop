<template>
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="translate-y-full opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="translate-y-0 opacity-100"
    leave-to-class="translate-y-full opacity-0"
  >
    <div
      v-if="show"
      class="fixed bottom-0 left-0 right-0 z-40 bg-surface-container-lowest/95 backdrop-blur-md border-t border-outline-variant/40 py-3 px-4 shadow-2xl"
    >
      <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
        <!-- Thumbnail & Tên sản phẩm -->
        <div class="flex items-center gap-3 min-w-0">
          <img
            :src="image"
            :alt="name"
            class="w-12 h-12 rounded-xl object-contain bg-surface border border-outline-variant/30 p-1 shrink-0 hidden sm:block"
          />
          <div class="truncate">
            <h3 class="text-xs sm:text-sm font-bold text-on-surface truncate">
              {{ name }}
            </h3>
            <p v-if="selectedVariantLabel" class="text-[11px] text-outline truncate">
              Phiên bản: <strong class="text-primary">{{ selectedVariantLabel }}</strong>
            </p>
          </div>
        </div>

        <!-- Giá & Các nút Action -->
        <div class="flex items-center gap-3 shrink-0">
          <div class="text-right">
            <div class="text-sm sm:text-base font-black text-primary">
              {{ formatPrice(price) }}
            </div>
            <div v-if="originalPrice" class="text-[11px] text-outline line-through hidden sm:block">
              {{ formatPrice(originalPrice) }}
            </div>
          </div>

          <button
            type="button"
            @click="$emit('add-to-cart')"
            class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border-2 border-primary text-primary hover:bg-primary/5 text-xs font-bold transition-colors"
          >
            <span class="material-symbols-outlined text-[16px]">add_shopping_cart</span>
            <span>Thêm giỏ</span>
          </button>

          <button
            type="button"
            @click="$emit('buy-now')"
            class="px-5 py-2.5 rounded-xl bg-primary text-on-primary hover:bg-primary-hover text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-primary/20"
          >
            Mua ngay
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { useFormatters } from '@/core/composables/useFormatters'

interface Props {
  show: boolean
  name: string
  image: string
  price: number
  originalPrice?: number
  selectedVariantLabel?: string
}

defineProps<Props>()
defineEmits<{
  (e: 'buy-now'): void
  (e: 'add-to-cart'): void
}>()

const { formatPrice } = useFormatters()
</script>

