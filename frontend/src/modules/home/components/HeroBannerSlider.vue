<template>
  <section class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">
    <!-- Main Interactive Slider (8 cols on lg) -->
    <div
      class="lg:col-span-8 relative rounded-2xl overflow-hidden bg-surface-container-highest min-h-[340px] sm:min-h-[400px] md:min-h-[440px] flex items-center shadow-md group select-none"
      @mouseenter="pauseTimer"
      @mouseleave="startTimer"
    >
      <TransitionGroup name="fade">
        <div
          v-for="(slide, index) in slides"
          v-show="currentSlide === index"
          :key="slide.id"
          class="absolute inset-0"
        >
          <img
            :src="slide.image"
            :alt="slide.title"
            class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-700"
          />
          <div
            class="absolute inset-0 bg-gradient-to-r from-surface-container-highest/95 via-surface-container-highest/75 md:via-surface-container-highest/50 to-transparent"
          ></div>

          <div class="relative z-10 px-6 sm:px-10 max-w-xl py-8 sm:py-12 h-full flex flex-col justify-center">
            <span
              class="inline-block self-start px-3 py-1 bg-primary text-on-primary rounded-full text-xs font-bold uppercase tracking-wider mb-3 shadow-xs"
            >
              {{ slide.badge }}
            </span>
            <h1 class="text-2xl sm:text-4xl md:text-5xl font-black text-on-surface mb-3 leading-tight">
              {{ slide.title }} <br />
              <span class="text-primary">{{ slide.highlight }}</span>
            </h1>
            <p class="text-sm sm:text-base text-on-surface-variant mb-6 font-normal line-clamp-2 sm:line-clamp-none max-w-md">
              {{ slide.description }}
            </p>
            <div class="flex flex-wrap gap-3">
              <RouterLink
                :to="slide.link"
                class="px-5 py-2.5 bg-primary text-on-primary rounded-xl font-bold shadow-sm hover:shadow-md hover:bg-primary-container transition-all active:scale-95 text-sm inline-flex items-center gap-1.5"
              >
                <span>Xem Ngay</span>
                <span class="material-symbols-outlined text-base">arrow_forward</span>
              </RouterLink>
              <RouterLink
                to="/products"
                class="px-5 py-2.5 border-2 border-primary/40 text-primary hover:border-primary rounded-xl font-bold hover:bg-primary/5 transition-all active:scale-95 text-sm"
              >
                Tất Cả Sản Phẩm
              </RouterLink>
            </div>
          </div>
        </div>
      </TransitionGroup>

      <!-- Carousel Arrows -->
      <button
        type="button"
        aria-label="Previous Slide"
        class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-surface/80 backdrop-blur text-on-surface hover:bg-surface flex items-center justify-center shadow-md opacity-0 group-hover:opacity-100 transition-opacity z-20 cursor-pointer"
        @click="prevSlide"
      >
        <span class="material-symbols-outlined text-xl">chevron_left</span>
      </button>
      <button
        type="button"
        aria-label="Next Slide"
        class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-surface/80 backdrop-blur text-on-surface hover:bg-surface flex items-center justify-center shadow-md opacity-0 group-hover:opacity-100 transition-opacity z-20 cursor-pointer"
        @click="nextSlide"
      >
        <span class="material-symbols-outlined text-xl">chevron_right</span>
      </button>

      <!-- Carousel Indicators -->
      <div class="absolute bottom-4 left-6 sm:left-10 z-20 flex gap-2">
        <button
          v-for="(_, idx) in slides"
          :key="idx"
          type="button"
          :aria-label="`Chuyển slide ${idx + 1}`"
          class="h-2 rounded-full transition-all duration-300 cursor-pointer"
          :class="currentSlide === idx ? 'w-8 bg-primary' : 'w-2 bg-on-surface-variant/40 hover:bg-on-surface-variant/70'"
          @click="currentSlide = idx"
        ></button>
      </div>
    </div>

    <!-- Side Promotional Banners (4 cols on lg) -->
    <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-4">
      <!-- Card 1: Thu Cũ Đổi Mới -->
      <RouterLink
        to="/products?category=dien-thoai"
        class="flex-1 relative rounded-2xl p-5 overflow-hidden bg-gradient-to-br from-amber-50 to-orange-100 dark:from-neutral-800 dark:to-neutral-900 border border-amber-200/60 dark:border-neutral-700 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between"
      >
        <div class="z-10 relative">
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-700 dark:text-amber-300 text-xs font-bold mb-2">
            <span class="material-symbols-outlined text-sm">autorenew</span>
            <span>Trợ giá lên đời</span>
          </div>
          <h3 class="text-base sm:text-lg font-black text-on-surface group-hover:text-primary transition-colors">
            Thu Cũ Đổi Mới
          </h3>
          <p class="text-xs text-on-surface-variant mt-1 max-w-[200px]">
            Hỗ trợ đến <strong class="text-amber-600 dark:text-amber-400">3.000.000₫</strong> không lo phụ phí
          </p>
        </div>
        <div class="z-10 relative mt-4 flex items-center text-xs font-bold text-primary gap-1 group-hover:translate-x-1 transition-transform">
          <span>Khám phá ngay</span>
          <span class="material-symbols-outlined text-sm">arrow_forward</span>
        </div>
        <img
          src="https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=300&auto=format&fit=crop&q=80"
          alt="Thu cũ đổi mới"
          class="absolute -right-4 -bottom-6 w-32 h-32 object-contain opacity-70 group-hover:scale-110 transition-transform duration-500 pointer-events-none"
        />
      </RouterLink>

      <!-- Card 2: Ưu đãi HSSV -->
      <RouterLink
        to="/products?category=laptop"
        class="flex-1 relative rounded-2xl p-5 overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-neutral-800 dark:to-neutral-900 border border-blue-200/60 dark:border-neutral-700 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between"
      >
        <div class="z-10 relative">
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-700 dark:text-blue-300 text-xs font-bold mb-2">
            <span class="material-symbols-outlined text-sm">school</span>
            <span>Back to School</span>
          </div>
          <h3 class="text-base sm:text-lg font-black text-on-surface group-hover:text-primary transition-colors">
            Đặc Quyền Sinh Viên
          </h3>
          <p class="text-xs text-on-surface-variant mt-1 max-w-[200px]">
            Giảm thêm <strong class="text-blue-600 dark:text-blue-400">500.000₫</strong> khi mua Laptop & Gear
          </p>
        </div>
        <div class="z-10 relative mt-4 flex items-center text-xs font-bold text-primary gap-1 group-hover:translate-x-1 transition-transform">
          <span>Xem chi tiết</span>
          <span class="material-symbols-outlined text-sm">arrow_forward</span>
        </div>
        <img
          src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=300&auto=format&fit=crop&q=80"
          alt="Ưu đãi sinh viên"
          class="absolute -right-4 -bottom-6 w-32 h-32 object-contain opacity-70 group-hover:scale-110 transition-transform duration-500 pointer-events-none"
        />
      </RouterLink>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'

interface SlideItem {
  id: number
  badge: string
  title: string
  highlight: string
  description: string
  link: string
  image: string
}

const slides: SlideItem[] = [
  {
    id: 1,
    badge: 'Siêu Phẩm 2026',
    title: 'iPhone 16 Pro Max',
    highlight: 'Titan Sa Mạc Đỉnh Cao',
    description: 'Sức mạnh Apple A18 Pro và camera điện ảnh đỉnh cao, trả góp 0% duyệt 5 phút.',
    link: '/products?category=dien-thoai&brands=apple',
    image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=1200&auto=format&fit=crop&q=80',
  },
  {
    id: 2,
    badge: 'Hiệu Năng Vô Song',
    title: 'Laptop Gaming AI',
    highlight: 'Trợ Giá Đến 5 Triệu',
    description: 'Chiến mượt mọi tựa game AAA và đồ họa 3D render nặng cùng GPU RTX 40 Series.',
    link: '/products?category=laptop',
    image: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=1200&auto=format&fit=crop&q=80',
  },
  {
    id: 3,
    badge: 'Âm Thanh Chuẩn Phòng Thu',
    title: 'Sony & Marshall',
    highlight: 'Chống Ồn Chủ Động',
    description: 'Tận hưởng thế giới âm thanh sống động với ưu đãi giảm giá độc quyền đến 35%.',
    link: '/products?category=tai-nghe',
    image: 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=1200&auto=format&fit=crop&q=80',
  },
]

const currentSlide = ref(0)
let timer: ReturnType<typeof setInterval> | null = null

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % slides.length
}

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length
}

const startTimer = () => {
  if (!timer) {
    timer = setInterval(nextSlide, 5000)
  }
}

const pauseTimer = () => {
  if (timer) {
    clearInterval(timer)
    timer = null
  }
}

onMounted(() => {
  startTimer()
})

onUnmounted(() => {
  pauseTimer()
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>

