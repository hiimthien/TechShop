<template>
  <aside class="w-full lg:w-[280px] flex-shrink-0">
    <div class="sticky top-24 flex flex-col gap-6 bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 shadow-sm">
      <div class="flex items-center justify-between pb-3 border-b border-outline-variant/20">
        <h3 class="font-black text-base text-on-surface flex items-center gap-1.5">
          <span class="material-symbols-outlined text-primary text-xl">tune</span>
          Bộ lọc tìm kiếm
        </h3>
        <button
          v-if="hasActiveFilter"
          type="button"
          @click="resetFilter"
          class="text-xs text-primary hover:underline font-bold"
        >
          Đặt lại
        </button>
      </div>

      <div class="flex flex-col gap-6">
        <!-- 1. Tình trạng hàng -->
        <section class="pb-3 border-b border-outline-variant/20">
          <label class="flex items-center justify-between cursor-pointer select-none group">
            <span class="text-xs font-bold text-on-surface group-hover:text-primary transition-colors">
              Chỉ hiện hàng có sẵn
            </span>
            <input
              type="checkbox"
              :checked="inStock"
              @change="toggleInStock(($event.target as HTMLInputElement).checked)"
              class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary cursor-pointer"
            />
          </label>
        </section>

        <!-- 2. Danh mục sản phẩm -->
        <section v-if="categories.length > 0">
          <h4 class="text-xs font-bold uppercase tracking-wider text-outline mb-2.5">
            Danh mục
          </h4>
          <div class="flex flex-col gap-1">
            <button
              type="button"
              @click="selectCategory(null)"
              class="flex items-center justify-between px-3 py-2 rounded-xl text-xs text-left transition-colors font-medium"
              :class="selectedCategory === null ? 'bg-primary text-on-primary font-bold shadow-xs' : 'hover:bg-surface-container-low text-on-surface'"
            >
              <span>Tất cả</span>
            </button>
            <button
              v-for="cat in categories"
              :key="cat.id"
              type="button"
              @click="selectCategory(cat.id)"
              class="flex items-center justify-between px-3 py-2 rounded-xl text-xs text-left transition-colors font-medium"
              :class="selectedCategory === cat.id ? 'bg-primary text-on-primary font-bold shadow-xs' : 'hover:bg-surface-container-low text-on-surface'"
            >
              <span>{{ cat.name }}</span>
            </button>
          </div>
        </section>

        <!-- 3. Thương hiệu (Multi-Select) -->
        <section v-if="brands.length > 0">
          <div class="flex items-center justify-between mb-2.5">
            <h4 class="text-xs font-bold uppercase tracking-wider text-outline">
              Thương hiệu
            </h4>
            <span v-if="selectedBrandIds.length > 0" class="text-[10px] bg-primary/10 text-primary font-bold px-1.5 py-0.5 rounded">
              Đã chọn {{ selectedBrandIds.length }}
            </span>
          </div>
          <div class="flex flex-col gap-1.5">
            <label
              v-for="brand in brands"
              :key="brand.id"
              class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg hover:bg-surface-container-low cursor-pointer transition-colors group select-none"
            >
              <input
                type="checkbox"
                :value="brand.id"
                :checked="selectedBrandIds.includes(brand.id)"
                @change="toggleBrand(brand.id)"
                class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary cursor-pointer"
              />
              <span class="text-xs text-on-surface group-hover:text-primary transition-colors font-medium">
                {{ brand.name }}
              </span>
            </label>
          </div>
        </section>

        <!-- 4. Khoảng giá -->
        <section>
          <h4 class="text-xs font-bold uppercase tracking-wider text-outline mb-2.5">
            Khoảng giá
          </h4>
          <div class="flex flex-col gap-1">
            <button
              v-for="preset in pricePresets"
              :key="preset.label"
              type="button"
              @click="selectPricePreset(preset)"
              class="flex items-center justify-between px-3 py-2 rounded-xl text-xs text-left transition-colors font-medium"
              :class="
                selectedMinPrice === preset.min && selectedMaxPrice === preset.max
                  ? 'bg-primary text-on-primary font-bold shadow-xs'
                  : 'hover:bg-surface-container-low text-on-surface'
              "
            >
              <span>{{ preset.label }}</span>
            </button>
          </div>
        </section>

        <!-- 5. BỘ LỌC ĐỘNG RIÊNG CHO LAPTOP: RAM & SSD -->
        <template v-if="currentCategorySlug === 'laptop'">
          <!-- RAM -->
          <section>
            <div class="flex items-center justify-between mb-2.5">
              <h4 class="text-xs font-bold uppercase tracking-wider text-outline">
                Dung lượng RAM
              </h4>
              <button
                v-if="selectedRam"
                @click="clearSpecFilter('ram')"
                class="text-[10px] text-primary hover:underline font-bold"
              >
                Bỏ chọn
              </button>
            </div>
            <div class="grid grid-cols-3 gap-1.5">
              <button
                v-for="ram in ['8GB', '16GB', '32GB']"
                :key="ram"
                type="button"
                @click="toggleRam(ram)"
                class="py-2 text-center rounded-xl border text-xs font-semibold transition-all"
                :class="
                  selectedRam === ram
                    ? 'border-2 border-primary bg-primary text-on-primary shadow-xs'
                    : 'border-outline-variant/50 hover:border-primary text-on-surface'
                "
              >
                {{ ram }}
              </button>
            </div>
          </section>

          <!-- Ổ cứng SSD -->
          <section>
            <div class="flex items-center justify-between mb-2.5">
              <h4 class="text-xs font-bold uppercase tracking-wider text-outline">
                Ổ cứng SSD
              </h4>
              <button
                v-if="selectedStorage"
                @click="clearSpecFilter('storage')"
                class="text-[10px] text-primary hover:underline font-bold"
              >
                Bỏ chọn
              </button>
            </div>
            <div class="grid grid-cols-3 gap-1.5">
              <button
                v-for="storage in ['256GB', '512GB', '1TB']"
                :key="storage"
                type="button"
                @click="toggleStorage(storage)"
                class="py-2 text-center rounded-xl border text-xs font-semibold transition-all"
                :class="
                  selectedStorage === storage
                    ? 'border-2 border-primary bg-primary text-on-primary shadow-xs'
                    : 'border-outline-variant/50 hover:border-primary text-on-surface'
                "
              >
                {{ storage }}
              </button>
            </div>
          </section>
        </template>

        <!-- 6. BỘ LỌC ĐỘNG CHO CHUỘT / BÀN PHÍM / TAI NGHE: KIỂU KẾT NỐI -->
        <template v-else-if="currentCategorySlug === 'chuot' || currentCategorySlug === 'ban-phim' || currentCategorySlug === 'tai-nghe'">
          <section>
            <div class="flex items-center justify-between mb-2.5">
              <h4 class="text-xs font-bold uppercase tracking-wider text-outline">
                Kiểu kết nối
              </h4>
              <button
                v-if="selectedConnection"
                @click="clearSpecFilter('connection')"
                class="text-[10px] text-primary hover:underline font-bold"
              >
                Bỏ chọn
              </button>
            </div>
            <div class="flex flex-col gap-1.5">
              <button
                v-for="conn in [
                  { label: 'Không dây (Wireless 2.4GHz)', val: 'Wireless' },
                  { label: 'Không dây (Bluetooth)', val: 'Bluetooth' },
                  { label: 'Có dây (USB / Type-C)', val: 'Có dây' }
                ]"
                :key="conn.val"
                type="button"
                @click="toggleConnection(conn.val)"
                class="px-3 py-2 text-left rounded-xl border text-xs font-semibold transition-all"
                :class="
                  selectedConnection === conn.val
                    ? 'border-2 border-primary bg-primary text-on-primary shadow-xs'
                    : 'border-outline-variant/50 hover:border-primary text-on-surface'
                "
              >
                {{ conn.label }}
              </button>
            </div>
          </section>
        </template>

        <!-- 7. BỘ LỌC ĐỘNG CHO MÀN HÌNH: ĐỘ PHÂN GIẢI & TẦN SỐ QUÉT -->
        <template v-else-if="currentCategorySlug === 'man-hinh'">
          <section>
            <div class="flex items-center justify-between mb-2.5">
              <h4 class="text-xs font-bold uppercase tracking-wider text-outline">
                Đặc tính màn hình
              </h4>
              <button
                v-if="selectedScreenFeature"
                @click="clearSpecFilter('screen')"
                class="text-[10px] text-primary hover:underline font-bold"
              >
                Bỏ chọn
              </button>
            </div>
            <div class="grid grid-cols-2 gap-1.5">
              <button
                v-for="feat in ['4K', '2K', '144Hz', '165Hz', 'OLED', 'UltraSharp']"
                :key="feat"
                type="button"
                @click="toggleScreenFeature(feat)"
                class="py-2 text-center rounded-xl border text-xs font-semibold transition-all"
                :class="
                  selectedScreenFeature === feat
                    ? 'border-2 border-primary bg-primary text-on-primary shadow-xs'
                    : 'border-outline-variant/50 hover:border-primary text-on-surface'
                "
              >
                {{ feat }}
              </button>
            </div>
          </section>
        </template>
      </div>

      <!-- Action Button -->
      <button
        type="button"
        @click="$emit('apply')"
        class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold text-xs hover:bg-primary-container transition-all active:scale-[0.98] shadow-sm flex items-center justify-center gap-1.5"
      >
        <span class="material-symbols-outlined text-[16px]">filter_alt</span>
        <span>Áp dụng bộ lọc</span>
      </button>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { computed, nextTick } from 'vue'
import type { Category, Brand } from '../types'

interface PricePreset {
  label: string
  min: number | null
  max: number | null
}

const pricePresets: PricePreset[] = [
  { label: 'Tất cả mức giá', min: null, max: null },
  { label: 'Dưới 1 triệu', min: null, max: 1000000 },
  { label: 'Từ 1 - 3 triệu', min: 1000000, max: 3000000 },
  { label: 'Từ 3 - 10 triệu', min: 3000000, max: 10000000 },
  { label: 'Từ 10 - 25 triệu', min: 10000000, max: 25000000 },
  { label: 'Trên 25 triệu', min: 25000000, max: null },
]

const props = defineProps<{
  categories: Category[]
  brands: Brand[]
  selectedCategory: number | null
  selectedBrandIds: number[]
  selectedMinPrice: number | null
  selectedMaxPrice: number | null
  selectedRam: string | null
  selectedStorage: string | null
  selectedConnection: string | null
  selectedScreenFeature: string | null
  inStock: boolean
}>()

const emit = defineEmits<{
  (e: 'update:selectedCategory', value: number | null): void
  (e: 'update:selectedBrandIds', value: number[]): void
  (e: 'update:selectedMinPrice', value: number | null): void
  (e: 'update:selectedMaxPrice', value: number | null): void
  (e: 'update:selectedRam', value: string | null): void
  (e: 'update:selectedStorage', value: string | null): void
  (e: 'update:selectedConnection', value: string | null): void
  (e: 'update:selectedScreenFeature', value: string | null): void
  (e: 'update:inStock', value: boolean): void
  (e: 'apply'): void
  (e: 'reset'): void
}>()

const currentCategorySlug = computed(() => {
  if (!props.selectedCategory) return null
  return props.categories.find((c) => c.id === props.selectedCategory)?.slug || null
})

const hasActiveFilter = computed(() => {
  return (
    props.selectedCategory !== null ||
    props.selectedBrandIds.length > 0 ||
    props.selectedMinPrice !== null ||
    props.selectedMaxPrice !== null ||
    props.selectedRam !== null ||
    props.selectedStorage !== null ||
    props.selectedConnection !== null ||
    props.selectedScreenFeature !== null ||
    props.inStock
  )
})

const toggleInStock = (checked: boolean) => {
  emit('update:inStock', checked)
  nextTick(() => emit('apply'))
}

const selectCategory = (id: number | null) => {
  emit('update:selectedCategory', id)
  // Khi đổi danh mục, reset các filter spec đặc thù của danh mục cũ
  emit('update:selectedRam', null)
  emit('update:selectedStorage', null)
  emit('update:selectedConnection', null)
  emit('update:selectedScreenFeature', null)
  nextTick(() => emit('apply'))
}

const toggleBrand = (id: number) => {
  const current = [...props.selectedBrandIds]
  const idx = current.indexOf(id)
  if (idx > -1) {
    current.splice(idx, 1)
  } else {
    current.push(id)
  }
  emit('update:selectedBrandIds', current)
  nextTick(() => emit('apply'))
}

const selectPricePreset = (preset: PricePreset) => {
  emit('update:selectedMinPrice', preset.min)
  emit('update:selectedMaxPrice', preset.max)
  nextTick(() => emit('apply'))
}

const toggleRam = (ram: string) => {
  emit('update:selectedRam', props.selectedRam === ram ? null : ram)
  nextTick(() => emit('apply'))
}

const toggleStorage = (storage: string) => {
  emit('update:selectedStorage', props.selectedStorage === storage ? null : storage)
  nextTick(() => emit('apply'))
}

const toggleConnection = (conn: string) => {
  emit('update:selectedConnection', props.selectedConnection === conn ? null : conn)
  nextTick(() => emit('apply'))
}

const toggleScreenFeature = (feat: string) => {
  emit('update:selectedScreenFeature', props.selectedScreenFeature === feat ? null : feat)
  nextTick(() => emit('apply'))
}

const clearSpecFilter = (type: 'ram' | 'storage' | 'connection' | 'screen') => {
  if (type === 'ram') emit('update:selectedRam', null)
  if (type === 'storage') emit('update:selectedStorage', null)
  if (type === 'connection') emit('update:selectedConnection', null)
  if (type === 'screen') emit('update:selectedScreenFeature', null)
  nextTick(() => emit('apply'))
}

const resetFilter = () => {
  emit('reset')
}
</script>
