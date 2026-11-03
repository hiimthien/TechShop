<template>
  <section class="rounded-2xl overflow-hidden bg-gradient-to-r from-red-600 via-rose-600 to-amber-600 p-4 sm:p-6 text-white shadow-lg space-y-4 sm:space-y-6">
    <!-- Header with Countdown -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center animate-pulse">
          <span class="material-symbols-outlined text-amber-300 text-2xl font-bold">local_fire_department</span>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h2 class="text-xl sm:text-2xl font-black uppercase tracking-wider text-white">
              Flash Sale Giá Sốc
            </h2>
            <span class="hidden sm:inline-block px-2 py-0.5 rounded-full bg-white/20 text-xs font-semibold backdrop-blur">
              Số lượng có hạn
            </span>
          </div>
          <p class="text-xs sm:text-sm text-white/90">Hàng công nghệ hạ giá cực đỉnh hôm nay</p>
        </div>
      </div>

      <!-- Realtime Countdown Timer -->
      <div class="flex items-center gap-2">
        <span class="text-xs uppercase tracking-wider text-white/90 font-medium hidden md:inline">Kết thúc trong</span>
        <div class="flex items-center gap-1.5 font-mono font-bold text-sm sm:text-base">
          <span class="w-8 h-8 rounded-lg bg-black/40 backdrop-blur flex items-center justify-center shadow-inner">
            {{ formatTime(hours) }}
          </span>
          <span class="text-amber-300">:</span>
          <span class="w-8 h-8 rounded-lg bg-black/40 backdrop-blur flex items-center justify-center shadow-inner">
            {{ formatTime(minutes) }}
          </span>
          <span class="text-amber-300">:</span>
          <span class="w-8 h-8 rounded-lg bg-black/40 backdrop-blur flex items-center justify-center shadow-inner">
            {{ formatTime(seconds) }}
          </span>
        </div>
      </div>
    </div>

    <!-- Product Grid -->
    <div v-if="isLoading" class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
      <div v-for="n in 4" :key="n" class="bg-white/10 rounded-xl h-64 animate-pulse"></div>
    </div>

    <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
      <article
        v-for="item in displayProducts"
        :key="item.id"
        class="bg-surface-container-lowest rounded-xl p-3 flex flex-col justify-between group shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 text-on-surface"
      >
        <RouterLink :to="`/products/${item.slug}`" class="block aspect-square relative bg-white rounded-lg p-2 overflow-hidden">
          <img
            :src="item.thumbnail || fallbackImage"
            :alt="item.name"
            class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
            loading="lazy"
          />
          <div class="absolute top-1.5 left-1.5 flex flex-col gap-1 items-start">
            <span class="bg-red-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded-md uppercase tracking-wider shadow-xs">
              -{{ item.discount_percent || 15 }}%
            </span>
          </div>
        </RouterLink>

        <div class="mt-2.5 flex flex-col flex-grow justify-between gap-2">
          <div>
            <span v-if="item.brand" class="text-[11px] font-bold text-outline">
              {{ item.brand }}
            </span>
            <RouterLink :to="`/products/${item.slug}`">
              <h3 class="text-xs sm:text-sm font-semibold line-clamp-2 h-10 group-hover:text-primary transition-colors">
                {{ item.name }}
              </h3>
            </RouterLink>
          </div>

          <div class="space-y-2">
            <div>
              <div class="text-red-600 dark:text-red-400 font-extrabold text-sm sm:text-base">
                {{ formatPrice(item.starting_price) }}
              </div>
              <div class="text-[11px] text-outline line-through">
                {{ formatPrice(calculateOriginalPrice(item.starting_price, item.discount_percent || 15)) }}
              </div>
            </div>

            <!-- Simulated Sales Progress Bar -->
            <div>
              <div class="w-full h-3 rounded-full bg-red-100 dark:bg-neutral-800 overflow-hidden relative flex items-center">
                <div
                  class="h-full bg-gradient-to-r from-red-500 to-amber-500 rounded-full"
                  :style="{ width: `${item.soldPercent || 72}%` }"
                ></div>
                <span class="absolute inset-0 flex items-center justify-center text-[9px] font-bold text-white drop-shadow-xs uppercase">
                  Đã bán {{ item.soldCount || 14 }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </article>
    </div>

    <!-- Bottom Action -->
    <div class="text-center pt-2">
      <RouterLink
        to="/products"
        class="inline-flex items-center gap-1.5 px-6 py-2 rounded-xl bg-white/20 hover:bg-white text-white hover:text-red-600 font-bold text-xs sm:text-sm backdrop-blur transition-all duration-300"
      >
        <span>Xem tất cả ưu đãi Flash Sale</span>
        <span class="material-symbols-outlined text-sm">arrow_forward</span>
      </RouterLink>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import type { Product } from '@/modules/products/types'

interface FlashProduct extends Product {
  soldPercent?: number
  soldCount?: number
}

const props = defineProps<{
  products: Product[]
  isLoading?: boolean
}>()

const fallbackImage =
  'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300"><rect fill="%23f0eded" width="300" height="300"/><text fill="%237b7487" font-family="sans-serif" font-size="16" dy="10.5" font-weight="bold" x="50%" y="50%" text-anchor="middle">TechShop</text></svg>'

const displayProducts = computed<FlashProduct[]>(() => {
  return props.products.slice(0, 4).map((item, idx) => ({
    ...item,
    soldPercent: 60 + (idx * 9) % 35,
    soldCount: 12 + idx * 3,
  }))
})

// Countdown logic (counting to 4 hours from now)
const hours = ref(3)
const minutes = ref(45)
const seconds = ref(20)
let timerId: ReturnType<typeof setInterval> | null = null

const tick = () => {
  if (seconds.value > 0) {
    seconds.value--
  } else if (minutes.value > 0) {
    minutes.value--
    seconds.value = 59
  } else if (hours.value > 0) {
    hours.value--
    minutes.value = 59
    seconds.value = 59
  } else {
    // Reset to 4 hours for endless dynamic feeling
    hours.value = 4
    minutes.value = 0
    seconds.value = 0
  }
}

const formatTime = (val: number): string => {
  return val < 10 ? `0${val}` : `${val}`
}

const formatPrice = (price: number | string | null | undefined): string => {
  if (!price) return 'Liên hệ'
  const numeric = typeof price === 'string' ? parseFloat(price) : price
  if (isNaN(numeric)) return 'Liên hệ'
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(numeric)
}

const calculateOriginalPrice = (price: number | string | null | undefined, discountPercent: number): number => {
  const numeric = typeof price === 'string' ? parseFloat(price) : Number(price || 0)
  if (!numeric) return 0
  return Math.round(numeric / (1 - discountPercent / 100))
}

onMounted(() => {
  timerId = setInterval(tick, 1000)
})

onUnmounted(() => {
  if (timerId) clearInterval(timerId)
})
</script>

