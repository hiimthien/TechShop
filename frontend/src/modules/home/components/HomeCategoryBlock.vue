<template>
  <section class="space-y-4">
    <!-- Section Header with Quick Brand Filter Pills -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-2 border-b border-outline-variant/30">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <span class="material-symbols-outlined text-xl">{{ icon }}</span>
        </div>
        <h2 class="text-xl sm:text-2xl font-black text-on-surface">
          {{ title }}
        </h2>
      </div>

      <!-- Quick Brand Tabs -->
      <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
        <button
          type="button"
          class="px-3 py-1 rounded-full text-xs font-semibold transition-all shrink-0 cursor-pointer"
          :class="!selectedBrand ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest hover:text-on-surface'"
          @click="selectBrand(null)"
        >
          Tất cả
        </button>
        <button
          v-for="brand in quickBrands"
          :key="brand.slug"
          type="button"
          class="px-3 py-1 rounded-full text-xs font-semibold transition-all shrink-0 cursor-pointer"
          :class="selectedBrand === brand.slug ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest hover:text-on-surface'"
          @click="selectBrand(brand.slug)"
        >
          {{ brand.name }}
        </button>

        <RouterLink
          :to="viewAllLink"
          class="hidden sm:inline-flex items-center gap-0.5 text-xs font-bold text-primary hover:underline ml-2 shrink-0"
        >
          <span>Xem tất cả</span>
          <span class="material-symbols-outlined text-sm">arrow_forward</span>
        </RouterLink>
      </div>
    </div>

    <!-- Main Content Layout (Side banner + Products grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">
      <!-- Side Featured Banner (3 cols on desktop) -->
      <RouterLink
        v-if="banner"
        :to="banner.link"
        class="lg:col-span-3 rounded-2xl overflow-hidden relative p-6 flex flex-col justify-between group shadow-sm hover:shadow-md border border-outline-variant/30 min-h-[220px] lg:min-h-[340px] text-white transition-all duration-300"
      >
        <!-- Background Image with Overlay -->
        <img
          :src="banner.image"
          :alt="banner.title"
          class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
        />
        <div
          class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-black/20 group-hover:via-black/50 transition-colors"
        ></div>

        <!-- Top Badge -->
        <div class="z-10 relative">
          <span
            class="text-[11px] font-extrabold text-white uppercase tracking-wider bg-white/20 backdrop-blur px-2.5 py-1 rounded-full inline-block shadow-xs"
          >
            Đặc quyền TechShop
          </span>
        </div>

        <!-- Bottom Content -->
        <div class="z-10 relative space-y-2">
          <h3 class="text-xl sm:text-2xl font-black text-white leading-snug drop-shadow-sm">
            {{ banner.title }}
          </h3>
          <p class="text-xs text-white/90 font-normal leading-relaxed line-clamp-2">
            {{ banner.subtitle }}
          </p>
          <div
            class="pt-2 flex items-center gap-1.5 text-xs font-bold text-amber-300 group-hover:translate-x-1.5 transition-transform"
          >
            <span>Khám phá ngay</span>
            <span class="material-symbols-outlined text-sm font-bold">arrow_forward</span>
          </div>
        </div>
      </RouterLink>

      <!-- Products Grid (9 cols if banner exists, otherwise 12) -->
      <div :class="banner ? 'lg:col-span-9' : 'lg:col-span-12'">
        <!-- Skeleton -->
        <div v-if="isLoading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
          <ProductCardSkeleton v-for="n in 4" :key="n" />
        </div>

        <!-- Real Products -->
        <div
          v-else-if="products.length > 0"
          class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4"
        >
          <ProductCard
            v-for="prod in products"
            :key="prod.id"
            :product="prod"
          />
        </div>

        <!-- Empty State -->
        <div
          v-else
          class="h-64 flex flex-col items-center justify-center rounded-2xl bg-surface-container-lowest border border-dashed border-outline-variant/40 p-6 text-center"
        >
          <span class="material-symbols-outlined text-3xl text-outline mb-2">inventory_2</span>
          <p class="text-sm text-on-surface-variant font-medium">Chưa có sản phẩm cho thương hiệu này</p>
          <button
            type="button"
            class="mt-3 text-xs text-primary font-bold hover:underline cursor-pointer"
            @click="selectBrand(null)"
          >
            Quay lại xem tất cả
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile View All Button -->
    <div class="text-center pt-1 sm:hidden">
      <RouterLink
        :to="viewAllLink"
        class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:underline"
      >
        <span>Xem tất cả sản phẩm {{ title }}</span>
        <span class="material-symbols-outlined text-sm">arrow_forward</span>
      </RouterLink>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { productService } from '@/modules/products/services/productService'
import type { Product } from '@/modules/products/types'
import ProductCard from '@/modules/products/components/ProductCard.vue'
import ProductCardSkeleton from '@/core/components/ui/ProductCardSkeleton.vue'

interface BrandItem {
  name: string
  slug: string
}

interface BannerItem {
  title: string
  subtitle: string
  image: string
  link: string
}

const props = defineProps<{
  title: string
  icon: string
  categorySlug: string
  quickBrands: BrandItem[]
  banner?: BannerItem
}>()

const products = ref<Product[]>([])
const isLoading = ref<boolean>(false)
const selectedBrand = ref<string | null>(null)

const viewAllLink = computed(() => {
  if (selectedBrand.value) {
    return `/products?category=${props.categorySlug}&brands=${selectedBrand.value}`
  }
  return `/products?category=${props.categorySlug}`
})

const fetchProducts = async () => {
  isLoading.value = true
  try {
    const params: Record<string, string | number> = {
      category: props.categorySlug,
      per_page: 4,
    }
    if (selectedBrand.value) {
      params.brand = selectedBrand.value
    }
    const res = await productService.getProducts(params)
    products.value = res.data
  } catch (error) {
    console.error(`Lỗi tải sản phẩm category ${props.categorySlug}:`, error)
  } finally {
    isLoading.value = false
  }
}

const selectBrand = (brandSlug: string | null) => {
  selectedBrand.value = brandSlug
  fetchProducts()
}

onMounted(() => {
  fetchProducts()
})
</script>

