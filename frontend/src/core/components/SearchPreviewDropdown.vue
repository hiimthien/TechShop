<template>
  <div
    v-if="isOpen && query.trim().length >= 2"
    class="absolute top-full left-0 right-0 mt-2 bg-surface-container-lowest rounded-2xl shadow-xl border border-outline-variant/40 overflow-hidden z-50 animate-in fade-in zoom-in-95 duration-150"
  >
    <!-- Đang tìm kiếm (Skeleton) -->
    <div v-if="isLoading" class="p-4 space-y-3">
      <div class="flex items-center gap-2 text-xs text-on-surface-variant">
        <span class="material-symbols-outlined text-[16px] animate-spin text-primary">progress_activity</span>
        <span>Đang tìm kiếm "<strong>{{ query }}</strong>"...</span>
      </div>
      <div v-for="n in 3" :key="n" class="flex gap-3 items-center animate-pulse">
        <div class="w-10 h-10 bg-surface-container rounded-lg shrink-0"></div>
        <div class="flex-1 space-y-1.5">
          <div class="h-3 bg-surface-container rounded w-3/4"></div>
          <div class="h-2.5 bg-surface-container rounded w-1/3"></div>
        </div>
      </div>
    </div>

    <!-- Có kết quả -->
    <div v-else-if="products.length > 0" class="divide-y divide-outline-variant/20">
      <div class="p-2.5 bg-surface-container-low/50 flex items-center justify-between text-[11px] text-on-surface-variant font-medium">
        <span>Gợi ý sản phẩm phù hợp:</span>
        <span class="text-primary font-bold">{{ products.length }} kết quả</span>
      </div>

      <div class="max-h-80 overflow-y-auto divide-y divide-outline-variant/20">
        <RouterLink
          v-for="p in products"
          :key="p.id"
          :to="`/products/${p.slug}`"
          @click="$emit('close')"
          class="flex items-center gap-3 p-3 hover:bg-surface-container-low transition-colors group"
        >
          <div class="w-12 h-12 rounded-xl bg-white border border-outline-variant/30 p-1 flex items-center justify-center shrink-0">
            <img
              :src="p.thumbnail || fallbackImage"
              :alt="p.name"
              class="w-full h-full object-contain group-hover:scale-105 transition-transform"
            />
          </div>

          <div class="flex-1 min-w-0">
            <h4 class="text-xs font-bold text-on-surface truncate group-hover:text-primary transition-colors">
              {{ p.name }}
            </h4>
            <div class="flex items-center gap-2 mt-0.5">
              <span class="text-xs font-black text-primary">{{ formatPrice(p.starting_price) }}</span>
              <span v-if="p.brand" class="text-[10px] text-on-surface-variant bg-surface-container px-1.5 py-0.2 rounded font-medium">
                {{ p.brand }}
              </span>
            </div>
          </div>

          <span class="material-symbols-outlined text-outline group-hover:text-primary group-hover:translate-x-0.5 transition-all text-[18px]">
            chevron_right
          </span>
        </RouterLink>
      </div>

      <!-- Footer: Xem tất cả -->
      <RouterLink
        :to="`/products?search=${encodeURIComponent(query.trim())}`"
        @click="$emit('close')"
        class="block p-3 text-center bg-primary/5 hover:bg-primary/10 text-primary text-xs font-bold transition-colors"
      >
        Xem tất cả kết quả cho "{{ query }}" →
      </RouterLink>
    </div>

    <!-- Không tìm thấy -->
    <div v-else class="p-6 text-center space-y-2">
      <span class="material-symbols-outlined text-3xl text-outline">search_off</span>
      <p class="text-xs text-on-surface-variant">
        Không tìm thấy sản phẩm nào khớp với từ khóa "<strong>{{ query }}</strong>"
      </p>
      <div class="pt-2 flex flex-wrap justify-center gap-1.5">
        <span class="text-[11px] text-outline self-center mr-1">Gợi ý nhanh:</span>
        <button
          v-for="kw in quickKeywords"
          :key="kw"
          type="button"
          @click="$emit('select-keyword', kw)"
          class="px-2 py-0.5 rounded-full bg-surface-container text-[11px] font-semibold text-on-surface-variant hover:bg-primary/10 hover:text-primary transition-colors"
        >
          {{ kw }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { RouterLink } from 'vue-router'
import { productService } from '@/modules/products/services/productService'
import type { Product } from '@/modules/products/types'
import { useFormatters } from '@/core/composables/useFormatters'
import { useDebounceFn } from '@/core/composables/useDebounce'

interface Props {
  query: string
  isOpen: boolean
}

const props = defineProps<Props>()
defineEmits<{
  (e: 'close'): void
  (e: 'select-keyword', kw: string): void
}>()

const { formatPrice } = useFormatters()
const fallbackImage =
  'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect fill="%23f0eded" width="100" height="100"/><text fill="%237b7487" font-family="sans-serif" font-size="12" dy="4" font-weight="bold" x="50%" y="50%" text-anchor="middle">TechShop</text></svg>'

const quickKeywords = ['iPhone 16 Pro', 'MacBook M3', 'Samsung S24 Ultra', 'Bàn phím cơ', 'Tai nghe Sony']

const products = ref<Product[]>([])
const isLoading = ref(false)

const executeSearch = useDebounceFn(async (kw: string) => {
  const trimmed = kw.trim()
  if (trimmed.length < 2) {
    products.value = []
    isLoading.value = false
    return
  }

  isLoading.value = true
  try {
    const res = await productService.getProducts({
      search: trimmed,
      per_page: 5,
    })
    products.value = res.data || []
  } catch (err) {
    console.error('Lỗi khi tìm kiếm nhanh:', err)
    products.value = []
  } finally {
    isLoading.value = false
  }
}, 280)

watch(
  () => props.query,
  (newVal) => {
    if (newVal.trim().length >= 2) {
      isLoading.value = true
      executeSearch(newVal)
    } else {
      products.value = []
      isLoading.value = false
    }
  }
)
</script>

