<template>
  <article
    class="bg-surface-container-lowest rounded-xl overflow-hidden product-card-shadow transition-all group border border-outline-variant/30 flex flex-col justify-between hover:-translate-y-1 hover:shadow-lg"
  >
    <RouterLink :to="`/products/${product.slug}`" class="block aspect-square relative p-md bg-white overflow-hidden">
      <img
        :src="product.thumbnail || fallbackImage"
        :alt="product.name"
        class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
        loading="lazy"
      />
      <div class="absolute top-2 left-2 right-2 flex flex-row items-center gap-1.5 flex-wrap pointer-events-none">
        <span
          class="bg-primary text-on-primary text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider shadow-xs"
        >
          Trả góp 0%
        </span>
        <span
          v-if="product.discount_percent && product.discount_percent > 0"
          class="bg-rose-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider shadow-xs"
        >
          -{{ product.discount_percent }}%
        </span>
      </div>
    </RouterLink>

    <div class="p-md flex flex-col gap-xs flex-grow justify-between">
      <div>
        <div class="flex items-center gap-xs mb-xs">
          <span
            class="material-symbols-outlined text-secondary text-sm"
            style="font-variation-settings: 'FILL' 1"
          >
            star
          </span>
          <span class="text-label-sm text-on-surface-variant font-bold">5.0</span>
          <span v-if="product.brand" class="text-label-sm text-outline ml-auto font-medium">
            {{ product.brand }}
          </span>
        </div>

        <RouterLink :to="`/products/${product.slug}`">
          <h2
            class="font-body-md text-body-md text-on-surface line-clamp-2 h-12 group-hover:text-primary transition-colors font-medium"
            :title="product.name"
          >
            {{ product.name }}
          </h2>
        </RouterLink>
      </div>

      <div class="flex flex-col mt-xs pt-xs border-t border-outline-variant/20">
        <span class="text-primary font-bold text-lg">
          {{ formatPrice(product.starting_price) }}
        </span>
      </div>
    </div>
  </article>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
import type { Product } from '../types'

defineProps<{
  product: Product
}>()

const fallbackImage =
  'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300"><rect fill="%23f0eded" width="300" height="300"/><text fill="%237b7487" font-family="sans-serif" font-size="16" dy="10.5" font-weight="bold" x="50%" y="50%" text-anchor="middle">TechShop</text></svg>'

const formatPrice = (price: number | string | null | undefined): string => {
  if (price === null || price === undefined || price === '') return 'Liên hệ'
  const numeric = typeof price === 'string' ? parseFloat(price) : price
  if (isNaN(numeric)) return 'Liên hệ'
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(numeric)
}
</script>
