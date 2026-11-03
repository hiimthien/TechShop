<template>
  <div class="p-4 md:p-5 bg-primary/5 rounded-2xl border border-primary/20 flex flex-wrap items-baseline justify-between gap-3">
    <div class="flex items-baseline gap-3">
      <span class="text-2xl md:text-3xl font-black text-primary">
        {{ formatPrice(price) }}
      </span>
      <span v-if="originalPrice" class="text-sm font-semibold text-outline line-through">
        {{ formatPrice(originalPrice) }}
      </span>
    </div>
    <div class="text-xs font-bold text-secondary bg-secondary/10 px-3 py-1 rounded-lg">
      Hoặc Trả góp từ {{ formatPrice(installmentMonthly) }}/tháng
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useFormatters } from '@/core/composables/useFormatters'

interface Props {
  price: number
  originalPrice?: number
}

const props = defineProps<Props>()
const { formatPrice } = useFormatters()

const installmentMonthly = computed(() => {
  return Math.round((props.price * 1.05) / 12)
})
</script>

