<template>
  <div class="relative" ref="menuContainer">
    <!-- Nút Bấm mở Mega Menu -->
    <button
      type="button"
      @click="toggleMenu"
      class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-bold text-xs uppercase tracking-wider transition-all shadow-sm active:scale-95"
    >
      <span class="material-symbols-outlined text-[18px]">menu</span>
      <span>Danh mục</span>
      <span
        class="material-symbols-outlined text-[16px] transition-transform duration-200"
        :class="isOpen ? 'rotate-180' : ''"
      >
        expand_more
      </span>
    </button>

    <!-- Mega Menu Dropdown Panel -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 translate-y-2 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 translate-y-2 scale-95"
    >
      <div
        v-if="isOpen"
        class="fixed md:absolute top-16 md:top-full left-4 right-4 md:left-0 md:right-auto mt-2 md:w-[860px] bg-surface-container-lowest rounded-2xl shadow-2xl border border-outline-variant/40 overflow-hidden z-50 flex flex-col md:flex-row max-h-[80vh] md:max-h-[520px]"
      >
        <!-- Cột trái: Danh sách Ngành hàng -->
        <div class="w-full md:w-64 bg-surface-container-low/60 border-r border-outline-variant/30 py-2 overflow-y-auto">
          <button
            v-for="cat in categories"
            :key="cat.id"
            @mouseenter="activeCategoryId = cat.id"
            @click="navigateCategory(cat)"
            type="button"
            class="w-full px-4 py-3 text-left flex items-center justify-between text-xs font-bold transition-all"
            :class="
              activeCategoryId === cat.id
                ? 'bg-primary/10 text-primary border-l-4 border-primary pl-3'
                : 'text-on-surface hover:bg-surface-container hover:text-primary'
            "
          >
            <div class="flex items-center gap-2.5 truncate">
              <span class="material-symbols-outlined text-[18px] text-primary shrink-0">
                {{ getCategoryIcon(cat.slug) }}
              </span>
              <span class="truncate">{{ cat.name }}</span>
            </div>
            <span class="material-symbols-outlined text-[16px] text-outline">chevron_right</span>
          </button>
        </div>

        <!-- Khối phải: Chi tiết Hãng, Phân loại & Mức giá -->
        <div class="flex-1 p-5 overflow-y-auto bg-surface-container-lowest space-y-6">
          <div v-if="activeCategory" class="space-y-6">
            <!-- Header tên danh mục -->
            <div class="flex items-center justify-between border-b border-outline-variant/20 pb-3">
              <h3 class="font-black text-sm text-on-surface uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-4 bg-primary rounded-full"></span>
                {{ activeCategory.name }}
              </h3>
              <RouterLink
                :to="{ path: '/products', query: { category: activeCategory.slug } }"
                @click="isOpen = false"
                class="text-xs font-bold text-primary hover:underline flex items-center gap-0.5"
              >
                Xem tất cả
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
              </RouterLink>
            </div>

            <!-- 1. Hãng sản xuất nổi bật có logo -->
            <div class="space-y-2.5">
              <span class="text-[11px] font-black text-on-surface-variant uppercase tracking-wider block">
                Thương hiệu nổi bật:
              </span>
              <div class="grid grid-cols-3 sm:grid-cols-4 gap-2.5">
                <button
                  v-for="brand in activeCategory.brands"
                  :key="brand.id"
                  @click="navigateBrand(brand)"
                  type="button"
                  class="p-2.5 rounded-xl border border-outline-variant/40 hover:border-primary hover:shadow-xs transition-all flex items-center gap-2 group text-left bg-surface-container-lowest"
                >
                  <img
                    v-if="brand.logo_url"
                    :src="brand.logo_url"
                    :alt="brand.name"
                    class="w-6 h-6 object-contain shrink-0 filter grayscale group-hover:grayscale-0 transition-all"
                  />
                  <span class="text-xs font-bold text-on-surface group-hover:text-primary truncate">
                    {{ brand.name }}
                  </span>
                </button>
              </div>
            </div>

            <!-- 2. Lọc theo Mức giá -->
            <div class="space-y-2.5">
              <span class="text-[11px] font-black text-on-surface-variant uppercase tracking-wider block">
                Chọn theo mức giá:
              </span>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="pr in priceRanges"
                  :key="pr.label"
                  @click="navigatePrice(pr)"
                  type="button"
                  class="px-3 py-1.5 rounded-lg border border-outline-variant/40 hover:border-primary hover:text-primary text-xs font-medium text-on-surface transition-all bg-surface-container-lowest"
                >
                  {{ pr.label }}
                </button>
              </div>
            </div>

            <!-- 3. Gợi ý dòng hot trend -->
            <div class="p-3.5 bg-primary/5 rounded-xl border border-primary/20 flex items-center justify-between text-xs">
              <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-[18px]">local_fire_department</span>
                <span class="font-bold text-on-surface">Khám phá Deal Hot Trend hôm nay:</span>
              </div>
              <RouterLink
                :to="{ path: '/products', query: { in_stock: 'true' } }"
                @click="isOpen = false"
                class="px-3 py-1 bg-primary text-on-primary rounded-lg font-bold text-[11px] hover:bg-primary-container transition"
              >
                Săn Deal Ngay
              </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { menuService, type MegaCategory } from '../services/menuService'

const router = useRouter()
const isOpen = ref(false)
const categories = ref<MegaCategory[]>([])
const activeCategoryId = ref<number | null>(null)
const menuContainer = ref<HTMLElement | null>(null)

const priceRanges = [
  { label: 'Dưới 2 triệu', max: 2000000 },
  { label: '2 - 5 triệu', min: 2000000, max: 5000000 },
  { label: '5 - 10 triệu', min: 5000000, max: 10000000 },
  { label: '10 - 20 triệu', min: 10000000, max: 20000000 },
  { label: 'Trên 20 triệu', min: 20000000 },
]

const activeCategory = computed(() => {
  return categories.value.find((c) => c.id === activeCategoryId.value) || categories.value[0] || null
})

function toggleMenu() {
  isOpen.value = !isOpen.value
}

function getCategoryIcon(slug: string): string {
  if (slug.includes('dien-thoai')) return 'smartphone'
  if (slug.includes('laptop')) return 'laptop_mac'
  if (slug.includes('dong-ho')) return 'watch'
  if (slug.includes('tai-nghe') || slug.includes('am-thanh')) return 'headphones'
  if (slug.includes('man-hinh')) return 'monitor'
  if (slug.includes('ban-phim')) return 'keyboard'
  if (slug.includes('chuot')) return 'mouse'
  return 'devices'
}

function navigateCategory(cat: { id: number; slug?: string }) {
  isOpen.value = false
  router.push({ path: '/products', query: { category: cat.slug || cat.id } })
}

function navigateBrand(brand: { id: number; slug?: string }) {
  isOpen.value = false
  const query: Record<string, string> = { brand: brand.slug || String(brand.id) }
  if (activeCategory.value?.slug) {
    query.category = activeCategory.value.slug
  }
  router.push({ path: '/products', query })
}

function navigatePrice(pr: { min?: number; max?: number }) {
  isOpen.value = false
  const query: Record<string, string | number> = {}
  if (activeCategory.value?.slug) query.category = activeCategory.value.slug
  if (pr.min) query.min_price = pr.min
  if (pr.max) query.max_price = pr.max
  router.push({ path: '/products', query })
}

function handleClickOutside(event: MouseEvent) {
  if (menuContainer.value && !menuContainer.value.contains(event.target as Node)) {
    isOpen.value = false
  }
}

onMounted(async () => {
  categories.value = await menuService.getMegaMenu()
  if (categories.value.length > 0) {
    activeCategoryId.value = categories.value[0]?.id ?? null
  }
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
