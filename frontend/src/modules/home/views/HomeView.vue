<template>
  <div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 space-y-10 sm:space-y-14 py-6 sm:py-8">
    <!-- 1. Hero Banner Slider + Side Promotional Cards -->
    <HeroBannerSlider />

    <!-- 2. Value Proposition & Trust Badges -->
    <TrustBadgesBar />

    <!-- 3. Flash Sale / Hot Deals with Countdown -->
    <FlashSaleSection
      :products="flashSaleProducts"
      :is-loading="isFlashLoading"
    />

    <!-- 4. Quick Category Icons Navigation -->
    <section class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-lg sm:text-xl font-black text-on-surface">Danh Mục Nổi Bật</h2>
          <p class="text-xs sm:text-sm text-on-surface-variant">Khám phá các ngành hàng công nghệ chính hãng</p>
        </div>
        <RouterLink
          to="/products"
          class="text-primary font-bold text-xs sm:text-sm flex items-center gap-1 hover:underline"
        >
          <span>Tất cả ngành hàng</span>
          <span class="material-symbols-outlined text-sm">arrow_forward</span>
        </RouterLink>
      </div>

      <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 sm:gap-4">
        <RouterLink
          v-for="cat in featuredCategories"
          :key="cat.slug"
          :to="`/products?category=${cat.slug}`"
          class="group flex flex-col items-center gap-2.5 p-3 rounded-2xl transition-all bg-surface-container-low hover:bg-surface-container hover:shadow-xs border border-outline-variant/20 hover:border-primary/30"
        >
          <div
            class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-surface-container-high flex items-center justify-center overflow-hidden border border-transparent group-hover:border-primary group-hover:scale-105 transition-all duration-300"
          >
            <span class="material-symbols-outlined text-2xl sm:text-3xl text-primary">
              {{ cat.icon }}
            </span>
          </div>
          <span class="text-xs sm:text-sm font-semibold text-on-surface group-hover:text-primary text-center line-clamp-1">
            {{ cat.name }}
          </span>
        </RouterLink>
      </div>
    </section>

    <!-- 5. Category Showcase: Smartphones -->
    <HomeCategoryBlock
      title="Điện Thoại Thông Minh"
      icon="smartphone"
      category-slug="dien-thoai"
      :quick-brands="[
        { name: 'Apple', slug: 'apple' },
        { name: 'Samsung', slug: 'samsung' },
        { name: 'Xiaomi', slug: 'xiaomi' },
        { name: 'OPPO', slug: 'oppo' },
      ]"
      :banner="{
        title: 'iPhone 16 Series',
        subtitle: 'Sẵn hàng đủ màu titan, trợ giá thu cũ 3 triệu.',
        image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=400&auto=format&fit=crop&q=80',
        link: '/products?category=dien-thoai&brands=apple',
      }"
    />

    <!-- 6. Category Showcase: Laptops & Tablets -->
    <HomeCategoryBlock
      title="Laptop & Máy Tính Bảng"
      icon="laptop"
      category-slug="laptop"
      :quick-brands="[
        { name: 'Apple', slug: 'apple' },
        { name: 'Dell', slug: 'dell' },
        { name: 'ASUS', slug: 'asus' },
        { name: 'Lenovo', slug: 'lenovo' },
      ]"
      :banner="{
        title: 'Laptop Gaming & AI',
        subtitle: 'Cấu hình khủng, đồ họa đỉnh cao, tặng kèm balo.',
        image: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400&auto=format&fit=crop&q=80',
        link: '/products?category=laptop',
      }"
    />

    <!-- 7. Category Showcase: Bàn Phím Cơ & Gear -->
    <HomeCategoryBlock
      title="Bàn Phím & Chuột Gaming"
      icon="keyboard"
      category-slug="ban-phim"
      :quick-brands="[
        { name: 'Keychron', slug: 'keychron' },
        { name: 'Logitech', slug: 'logitech' },
        { name: 'Razer', slug: 'razer' },
      ]"
      :banner="{
        title: 'Keychron Custom',
        subtitle: 'Gõ êm mượt mà, layout chuẩn Mac & Windows.',
        image: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400&auto=format&fit=crop&q=80',
        link: '/products?category=ban-phim',
      }"
    />

    <!-- 8. Category Showcase: Âm Thanh & Phụ Kiện -->
    <HomeCategoryBlock
      title="Tai Nghe & Âm Thanh"
      icon="headphones"
      category-slug="tai-nghe"
      :quick-brands="[
        { name: 'Sony', slug: 'sony' },
        { name: 'Marshall', slug: 'marshall' },
        { name: 'JBL', slug: 'jbl' },
        { name: 'Apple', slug: 'apple' },
      ]"
      :banner="{
        title: 'Thế Giới Âm Thanh',
        subtitle: 'Chống ồn đỉnh cao, trải nghiệm âm nhạc sống động.',
        image: 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=400&auto=format&fit=crop&q=80',
        link: '/products?category=tai-nghe',
      }"
    />

    <!-- 9. Tech News & Buying Advice -->
    <TechNewsSection />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { productService } from '@/modules/products/services/productService'
import type { Product } from '@/modules/products/types'

import HeroBannerSlider from '../components/HeroBannerSlider.vue'
import TrustBadgesBar from '../components/TrustBadgesBar.vue'
import FlashSaleSection from '../components/FlashSaleSection.vue'
import HomeCategoryBlock from '../components/HomeCategoryBlock.vue'
import TechNewsSection from '../components/TechNewsSection.vue'

const flashSaleProducts = ref<Product[]>([])
const isFlashLoading = ref<boolean>(false)

const featuredCategories = [
  { name: 'Điện thoại', slug: 'dien-thoai', icon: 'smartphone' },
  { name: 'Laptop', slug: 'laptop', icon: 'laptop' },
  { name: 'Bàn phím cơ', slug: 'ban-phim', icon: 'keyboard' },
  { name: 'Chuột máy tính', slug: 'chuot', icon: 'mouse' },
  { name: 'Tai nghe', slug: 'tai-nghe', icon: 'headphones' },
  { name: 'Đồng hồ', slug: 'dong-ho-thong-minh', icon: 'watch' },
]

onMounted(async () => {
  isFlashLoading.value = true
  try {
    const res = await productService.getProducts({ per_page: 8 })
    flashSaleProducts.value = res.data
  } catch (error) {
    console.error('Lỗi khi tải dữ liệu Flash Sale trang chủ:', error)
  } finally {
    isFlashLoading.value = false
  }
})
</script>
