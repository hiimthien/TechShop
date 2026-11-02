<template>
  <div class="max-w-container-max mx-auto px-lg md:px-lg space-y-12 py-lg">
    <!-- Hero Banner (Cắt chuẩn từ techvina_homepage) -->
    <section
      class="relative rounded-2xl overflow-hidden bg-surface-container-highest min-h-[420px] md:min-h-[480px] flex items-center shadow-lg group"
    >
      <div class="absolute inset-0 z-0">
        <img
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
          alt="Siêu ưu đãi iPhone 15 Pro Max"
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuBmmaiQcdFIDz_uQeJr0pjACPWS8UEtZvhoR7ipfPXdO3vm5KKLCu7a6mgiiIWc5IQv_9Fda4bYSnHHsEpGoYFXRho__qCzByS4z_Sgx9gejc2lAnstPG0uzObNXJ9lwfd52sTIpOi9q0wBbczuVwOeoAesJe8eRx8CEMiLDfulNcoDDiFim5L0d5_3CiyxNIbe3BngRMuM4Bv3bCp2lO1ckuEl1sFhoIChuX74urRbosVZBdwn-ify9GX6HosI7d5A_GZGnvhc9J8"
        />
        <div
          class="absolute inset-0 bg-gradient-to-r from-surface-container-highest/95 via-surface-container-highest/75 md:via-surface-container-highest/50 to-transparent"
        ></div>
      </div>

      <div class="relative z-10 px-lg md:px-3xl max-w-2xl py-8">
        <span
          class="inline-block px-md py-1 bg-primary text-on-primary rounded-full text-xs font-bold uppercase tracking-wider mb-md shadow-sm"
        >
          Ưu Đãi Đặc Biệt
        </span>
        <h1 class="text-3xl md:text-5xl font-black text-on-surface mb-md leading-tight">
          Siêu ưu đãi Flagship <br />
          <span class="text-primary">Giảm đến 5.000.000₫</span>
        </h1>
        <p class="text-base md:text-lg text-on-surface-variant mb-xl font-normal leading-relaxed">
          Sở hữu ngay siêu phẩm công nghệ mạnh mẽ nhất với mức giá hấp dẫn cùng chính sách trả góp 0% và bảo hành chính hãng.
        </p>
        <div class="flex flex-wrap gap-md">
          <RouterLink
            to="/products"
            class="px-xl py-md bg-primary text-on-primary rounded-lg font-bold shadow-md hover:shadow-lg hover:bg-primary-container transition-all active:scale-95 text-sm"
          >
            Mua ngay
          </RouterLink>
          <RouterLink
            to="/products"
            class="px-xl py-md border-2 border-primary text-primary rounded-lg font-bold hover:bg-primary/5 transition-all active:scale-95 text-sm"
          >
            Xem danh sách
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- Category Grid -->
    <section class="space-y-6">
      <div class="flex justify-between items-end">
        <div>
          <h2 class="text-2xl font-black text-on-surface">Danh mục nổi bật</h2>
          <p class="text-sm text-on-surface-variant">Khám phá theo danh mục sản phẩm</p>
        </div>
        <RouterLink
          to="/products"
          class="text-primary font-bold text-sm flex items-center gap-xs hover:underline"
        >
          <span>Khám phá tất cả</span>
          <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
        </RouterLink>
      </div>

      <div class="grid grid-cols-3 md:grid-cols-6 gap-4 md:gap-6">
        <RouterLink
          v-for="cat in featuredCategories"
          :key="cat.slug"
          :to="`/products?category_slug=${cat.slug}`"
          class="group flex flex-col items-center gap-3 p-3 rounded-2xl transition-all hover:bg-surface-container-low"
        >
          <div
            class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-surface-container-high flex items-center justify-center overflow-hidden border border-transparent group-hover:border-primary group-hover:shadow-md transition-all duration-300"
          >
            <span class="material-symbols-outlined text-3xl md:text-4xl text-primary">
              {{ cat.icon }}
            </span>
          </div>
          <span class="text-xs md:text-sm font-semibold text-on-surface group-hover:text-primary text-center">
            {{ cat.name }}
          </span>
        </RouterLink>
      </div>
    </section>

    <!-- Featured Products Section -->
    <section class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-black text-on-surface">Sản phẩm nổi bật</h2>
          <p class="text-sm text-on-surface-variant">Lựa chọn được ưa chuộng nhất tuần qua</p>
        </div>
        <RouterLink
          to="/products"
          class="text-primary hover:underline text-sm font-bold flex items-center gap-1"
        >
          <span>Xem tất cả</span>
          <span class="material-symbols-outlined text-sm">arrow_forward</span>
        </RouterLink>
      </div>

      <!-- Loading Skeleton -->
      <div
        v-if="isLoading"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
      >
        <div
          v-for="n in 4"
          :key="n"
          class="bg-surface-container-lowest rounded-xl p-md border border-outline-variant/20 animate-pulse flex flex-col gap-4"
        >
          <div class="aspect-square bg-surface-container rounded-lg"></div>
          <div class="h-4 bg-surface-container rounded w-3/4"></div>
          <div class="h-6 bg-surface-container rounded w-1/3"></div>
        </div>
      </div>

      <!-- Grid -->
      <div
        v-else-if="products.length > 0"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
      >
        <ProductCard
          v-for="product in products"
          :key="product.id"
          :product="product"
        />
      </div>

      <!-- Empty -->
      <div v-else class="text-center py-12 text-on-surface-variant text-sm bg-surface-container-lowest rounded-xl border border-outline-variant/30">
        Chưa có sản phẩm nào.
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { productService } from '@/modules/products/services/productService'
import type { Product } from '@/modules/products/types'
import ProductCard from '@/modules/products/components/ProductCard.vue'

const products = ref<Product[]>([])
const isLoading = ref<boolean>(false)

const featuredCategories = [
  { name: 'Điện thoại', slug: 'smartphones', icon: 'smartphone' },
  { name: 'Laptop', slug: 'laptops', icon: 'laptop' },
  { name: 'Màn hình', slug: 'monitors', icon: 'desktop_windows' },
  { name: 'Tablet', slug: 'tablets', icon: 'tablet' },
  { name: 'Phụ kiện', slug: 'accessories', icon: 'headphones' },
  { name: 'Linh kiện', slug: 'components', icon: 'memory' },
]

onMounted(async () => {
  isLoading.value = true
  try {
    const res = await productService.getProducts({ per_page: 8 })
    products.value = res.data
  } catch (error) {
    console.error('Lỗi khi tải sản phẩm trang chủ:', error)
  } finally {
    isLoading.value = false
  }
})
</script>
