<template>
  <div class="space-y-4 pt-2">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-outline-variant/30">
      <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-primary flex items-center gap-1.5">
          <span class="material-symbols-outlined text-[18px]">tune</span>
          Biến thể sản phẩm (Màu sắc / RAM / Dung lượng)
        </h3>
        <p class="text-[11px] text-on-surface-variant mt-0.5">
          Thiết lập linh hoạt từng tùy chọn phân loại, tự động sinh tổ hợp đa chiều hoặc nhập tùy chỉnh thủ công.
        </p>
      </div>

      <!-- Mode Switcher -->
      <div class="inline-flex p-1 bg-surface-container rounded-xl border border-outline-variant/40">
        <button
          type="button"
          @click="activeMode = 'generator'"
          class="px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all"
          :class="
            activeMode === 'generator'
              ? 'bg-primary text-on-primary shadow-xs'
              : 'text-on-surface-variant hover:text-on-surface'
          "
        >
          <span class="material-symbols-outlined text-[15px]">auto_awesome</span>
          Bộ sinh tổ hợp tự động
        </button>
        <button
          type="button"
          @click="activeMode = 'table'"
          class="px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all relative"
          :class="
            activeMode === 'table'
              ? 'bg-primary text-on-primary shadow-xs'
              : 'text-on-surface-variant hover:text-on-surface'
          "
        >
          <span class="material-symbols-outlined text-[15px]">table_rows</span>
          Bảng biến thể
          <span
            v-if="variants.length > 0"
            class="ml-1 px-1.5 py-0.2 rounded-full text-[10px]"
            :class="activeMode === 'table' ? 'bg-white/20 text-white' : 'bg-primary/10 text-primary'"
          >
            {{ variants.length }}
          </span>
        </button>
      </div>
    </div>

    <!-- MODE 1: COMBINATION GENERATOR (Tạo nhanh theo tổ hợp đa chiều) -->
    <div
      v-if="activeMode === 'generator'"
      class="bg-surface-container-low p-4 sm:p-5 rounded-2xl border border-outline-variant/40 space-y-4"
    >
      <div class="flex items-center justify-between pb-2 border-b border-outline-variant/20">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-primary text-[20px]">hub</span>
          <span class="text-xs font-bold uppercase tracking-wider text-on-surface">
            Chọn các thuộc tính cấu hình sản phẩm
          </span>
        </div>
        <span class="text-[11px] text-on-surface-variant font-medium">
          Dự kiến sinh ra: <strong class="text-primary">{{ estimatedCombinationsCount }}</strong> biến thể
        </span>
      </div>

      <!-- 1. Thuộc tính Màu sắc -->
      <div class="space-y-2">
        <div class="flex items-center justify-between">
          <label class="text-xs font-bold text-on-surface flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-secondary"></span>
            1. Màu sắc sản phẩm
          </label>
          <span class="text-[11px] text-on-surface-variant">Chọn gợi ý hoặc gõ thêm và nhấn Enter</span>
        </div>

        <!-- Tags đã chọn -->
        <div class="flex flex-wrap gap-1.5 items-center p-2 rounded-xl bg-surface border border-outline-variant/50 min-h-[42px]">
          <span
            v-for="(color, cIdx) in selectedColors"
            :key="cIdx"
            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-primary/10 text-primary text-xs font-semibold border border-primary/20"
          >
            {{ color }}
            <button
              type="button"
              @click="removeColor(cIdx)"
              class="w-3.5 h-3.5 rounded hover:bg-primary/20 inline-flex items-center justify-center text-[11px]"
            >
              ×
            </button>
          </span>
          <input
            v-model="newColorInput"
            @keydown.enter.prevent="addColor"
            type="text"
            placeholder="+ Thêm màu khác..."
            class="flex-1 min-w-[120px] bg-transparent text-xs outline-none px-1 py-0.5 text-on-surface placeholder:text-outline"
          />
        </div>

        <!-- Quick color presets -->
        <div class="flex flex-wrap gap-1.5 items-center pt-0.5">
          <span class="text-[10px] text-on-surface-variant font-semibold">Gợi ý nhanh:</span>
          <button
            v-for="preset in colorPresets"
            :key="preset"
            type="button"
            @click="toggleColorPreset(preset)"
            class="px-2 py-0.5 rounded-md text-[11px] transition-colors border"
            :class="
              selectedColors.includes(preset)
                ? 'bg-primary text-on-primary border-primary font-bold'
                : 'bg-surface hover:bg-surface-container text-on-surface-variant border-outline-variant/40'
            "
          >
            {{ preset }}
          </button>
        </div>
      </div>

      <!-- 2. Thuộc tính RAM -->
      <div class="space-y-2">
        <div class="flex items-center justify-between">
          <label class="text-xs font-bold text-on-surface flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-primary"></span>
            2. Bộ nhớ RAM (Tùy chọn cho Laptop / PC / Tablet)
          </label>
          <span class="text-[11px] text-on-surface-variant">Bỏ trống nếu sản phẩm không có tùy chọn RAM</span>
        </div>

        <!-- RAM Quick Tags -->
        <div class="flex flex-wrap gap-2 items-center">
          <button
            v-for="ram in ramPresets"
            :key="ram"
            type="button"
            @click="toggleRam(ram)"
            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border"
            :class="
              selectedRams.includes(ram)
                ? 'bg-primary text-on-primary border-primary shadow-xs'
                : 'bg-surface hover:bg-surface-container text-on-surface border-outline-variant/60'
            "
          >
            {{ ram }}
          </button>
          <div class="inline-flex items-center gap-1 bg-surface border border-outline-variant/50 rounded-xl px-2 py-1">
            <input
              v-model="newRamInput"
              @keydown.enter.prevent="addCustomRam"
              type="text"
              placeholder="+ Khác (VD: 48GB)"
              class="w-28 text-xs bg-transparent outline-none placeholder:text-outline text-on-surface"
            />
            <button
              type="button"
              @click="addCustomRam"
              class="text-primary hover:text-primary-hover text-xs font-bold px-1"
            >
              +
            </button>
          </div>
        </div>
      </div>

      <!-- 3. Thuộc tính Dung lượng / Ổ cứng (ROM / SSD) -->
      <div class="space-y-2">
        <div class="flex items-center justify-between">
          <label class="text-xs font-bold text-on-surface flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-tertiary"></span>
            3. Ổ cứng / Bộ nhớ trong (Storage / ROM / SSD)
          </label>
          <span class="text-[11px] text-on-surface-variant">Bỏ trống nếu sản phẩm không phân chia ổ cứng</span>
        </div>

        <!-- Storage Quick Tags -->
        <div class="flex flex-wrap gap-2 items-center">
          <button
            v-for="st in storagePresets"
            :key="st"
            type="button"
            @click="toggleStorage(st)"
            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border"
            :class="
              selectedStorages.includes(st)
                ? 'bg-tertiary text-on-tertiary border-tertiary shadow-xs'
                : 'bg-surface hover:bg-surface-container text-on-surface border-outline-variant/60'
            "
          >
            {{ st }}
          </button>
          <div class="inline-flex items-center gap-1 bg-surface border border-outline-variant/50 rounded-xl px-2 py-1">
            <input
              v-model="newStorageInput"
              @keydown.enter.prevent="addCustomStorage"
              type="text"
              placeholder="+ Khác (VD: 4TB)"
              class="w-28 text-xs bg-transparent outline-none placeholder:text-outline text-on-surface"
            />
            <button
              type="button"
              @click="addCustomStorage"
              class="text-tertiary hover:opacity-80 text-xs font-bold px-1"
            >
              +
            </button>
          </div>
        </div>
      </div>

      <!-- Base Price & Stock Setting -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-outline-variant/30 bg-surface p-3.5 rounded-xl">
        <div>
          <label class="text-xs font-bold text-on-surface block mb-1">
            Giá bán cơ bản mặc định (₫)
          </label>
          <input
            v-model.number="basePrice"
            type="number"
            min="0"
            step="10000"
            placeholder="VD: 25000000"
            class="w-full px-3 py-1.5 rounded-lg border border-outline-variant/60 focus:border-primary text-xs font-semibold text-primary"
          />
          <p class="text-[10px] text-on-surface-variant mt-0.5">
            Sẽ gán làm giá khởi điểm cho các biến thể, có thể tinh chỉnh sau.
          </p>
        </div>
        <div>
          <label class="text-xs font-bold text-on-surface block mb-1">
            Số lượng tồn kho mặc định
          </label>
          <input
            v-model.number="baseStock"
            type="number"
            min="0"
            placeholder="VD: 20"
            class="w-full px-3 py-1.5 rounded-lg border border-outline-variant/60 focus:border-primary text-xs text-on-surface"
          />
          <p class="text-[10px] text-on-surface-variant mt-0.5">
            Số lượng hàng có sẵn cho mỗi phiên bản sinh ra.
          </p>
        </div>
      </div>

      <!-- Action Button -->
      <div class="flex items-center justify-between pt-1">
        <button
          type="button"
          @click="resetGenerator"
          class="text-xs text-on-surface-variant hover:text-error transition-colors flex items-center gap-1 font-semibold"
        >
          <span class="material-symbols-outlined text-[16px]">restart_alt</span>
          Làm mới bộ chọn
        </button>

        <button
          type="button"
          @click="generateCombinations"
          :disabled="estimatedCombinationsCount === 0"
          class="px-5 py-2.5 rounded-xl bg-primary text-on-primary hover:bg-primary-hover disabled:opacity-40 disabled:cursor-not-allowed text-xs font-black uppercase tracking-wider flex items-center gap-2 shadow-sm transition-all"
        >
          <span class="material-symbols-outlined text-[18px]">bolt</span>
          Tạo {{ estimatedCombinationsCount }} biến thể tự động
        </button>
      </div>
    </div>

    <!-- MODE 2: TABLE VIEW / BULK ACTIONS -->
    <div class="space-y-3">
      <!-- Bulk Toolbar -->
      <div class="flex flex-wrap items-center justify-between gap-2.5 bg-surface-container p-2.5 rounded-xl border border-outline-variant/40">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="text-xs font-bold text-on-surface flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px] text-primary">build</span>
            Thao tác nhanh hàng loạt:
          </span>
          <div class="inline-flex items-center gap-1">
            <input
              v-model.number="bulkPrice"
              type="number"
              placeholder="Giá chung..."
              class="w-28 px-2 py-1 rounded-lg border border-outline-variant/60 bg-surface text-xs"
            />
            <button
              type="button"
              @click="applyBulkPrice"
              class="px-2 py-1 rounded-lg bg-surface hover:bg-primary/10 text-primary border border-primary/30 text-[11px] font-bold"
            >
              Gán giá
            </button>
          </div>
          <div class="inline-flex items-center gap-1">
            <input
              v-model.number="bulkStock"
              type="number"
              placeholder="Tồn kho..."
              class="w-20 px-2 py-1 rounded-lg border border-outline-variant/60 bg-surface text-xs text-center"
            />
            <button
              type="button"
              @click="applyBulkStock"
              class="px-2 py-1 rounded-lg bg-surface hover:bg-primary/10 text-primary border border-primary/30 text-[11px] font-bold"
            >
              Gán kho
            </button>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="addVariantRow"
            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg border border-primary text-primary hover:bg-primary/5 transition-all text-xs font-bold"
          >
            <span class="material-symbols-outlined text-[16px]">add</span>
            + Thêm 1 dòng lẻ
          </button>
          <button
            type="button"
            v-if="variants.length > 0"
            @click="clearAllVariants"
            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-error/40 text-error hover:bg-error/5 transition-all text-xs font-bold"
          >
            <span class="material-symbols-outlined text-[15px]">delete_sweep</span>
            Xóa hết
          </button>
        </div>
      </div>

      <!-- Variants Table -->
      <div class="border border-outline-variant/40 rounded-xl overflow-hidden bg-surface-container-lowest">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-surface-container-low text-on-surface-variant font-bold uppercase border-b border-outline-variant/40">
              <tr>
                <th class="py-2.5 px-3 w-10 text-center">#</th>
                <th class="py-2.5 px-3">Tên phiên bản (Màu / RAM / Dung lượng) *</th>
                <th class="py-2.5 px-3 w-36">Mã SKU</th>
                <th class="py-2.5 px-3 w-32">Giá bán (₫) *</th>
                <th class="py-2.5 px-3 w-20 text-center">Tồn kho</th>
                <th class="py-2.5 px-3 w-40">Ảnh gắn màu</th>
                <th class="py-2.5 px-2 text-center w-12">Xóa</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/30">
              <tr v-for="(v, vIdx) in variants" :key="vIdx" class="hover:bg-surface-container-low/40">
                <td class="py-2 px-3 text-center text-on-surface-variant font-mono text-[11px]">
                  {{ vIdx + 1 }}
                </td>
                <td class="py-2 px-3">
                  <input
                    v-model="v.sku_name"
                    required
                    type="text"
                    placeholder="VD: Titan Tự Nhiên - 16GB - 512GB"
                    class="w-full px-2.5 py-1.5 rounded-lg border border-outline-variant/50 focus:border-primary focus:ring-1 focus:ring-primary/20 text-xs bg-surface font-medium"
                  />
                </td>
                <td class="py-2 px-3">
                  <input
                    v-model="v.sku"
                    type="text"
                    placeholder="Tự sinh nếu trống"
                    class="w-full px-2.5 py-1.5 rounded-lg border border-outline-variant/50 focus:border-primary focus:ring-1 focus:ring-primary/20 text-xs bg-surface uppercase font-mono text-[11px]"
                  />
                </td>
                <td class="py-2 px-3">
                  <input
                    v-model.number="v.price"
                    required
                    type="number"
                    min="0"
                    step="10000"
                    class="w-full px-2.5 py-1.5 rounded-lg border border-outline-variant/50 focus:border-primary focus:ring-1 focus:ring-primary/20 text-xs font-bold text-primary bg-surface"
                  />
                </td>
                <td class="py-2 px-3">
                  <input
                    v-model.number="v.stock"
                    required
                    type="number"
                    min="0"
                    class="w-full px-2.5 py-1.5 rounded-lg border border-outline-variant/50 focus:border-primary focus:ring-1 focus:ring-primary/20 text-xs bg-surface text-center font-semibold"
                  />
                </td>
                <td class="py-2 px-3">
                  <div class="flex items-center gap-1.5">
                    <img
                      v-if="v.image_url"
                      :src="v.image_url"
                      class="w-7 h-7 object-contain rounded border border-outline-variant/50 bg-white p-0.5 shrink-0"
                      alt=""
                    />
                    <select
                      v-model="v.image_url"
                      class="w-full px-2 py-1.5 rounded-lg border border-outline-variant/50 text-[11px] bg-surface truncate"
                    >
                      <option value="">(Chưa chọn ảnh)</option>
                      <option v-for="(img, imgIdx) in availableImages" :key="imgIdx" :value="img">
                        Ảnh #{{ imgIdx + 1 }}
                      </option>
                    </select>
                  </div>
                </td>
                <td class="py-2 px-2 text-center">
                  <button
                    type="button"
                    :disabled="variants.length <= 1"
                    @click="removeVariantRow(vIdx)"
                    class="w-7 h-7 rounded inline-flex items-center justify-center text-error hover:bg-error/10 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                    title="Xóa phiên bản này"
                  >
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

export interface VariantItem {
  sku_name: string
  sku: string
  price: number
  stock: number
  image_url: string
}

interface Props {
  variants: VariantItem[]
  availableImages: string[]
  productName?: string
}

const props = withDefaults(defineProps<Props>(), {
  productName: '',
})

const emit = defineEmits<{
  (e: 'update:variants', val: VariantItem[]): void
}>()

const activeMode = ref<'generator' | 'table'>('generator')

// 1. Preset definitions for tech products
const colorPresets = [
  'Titan Tự Nhiên',
  'Titan Sa Mạc',
  'Titan Đen',
  'Titan Trắng',
  'Xám Không Gian',
  'Bạc',
  'Đen',
  'Trắng',
  'Xanh Dương',
]

const ramPresets = ['8GB', '16GB', '24GB', '32GB', '64GB']
const storagePresets = ['128GB', '256GB', '512GB', '1TB', '2TB']

// 2. Selected attribute values
const selectedColors = ref<string[]>(['Titan Tự Nhiên', 'Xám Không Gian'])
const selectedRams = ref<string[]>([])
const selectedStorages = ref<string[]>(['256GB', '512GB'])

const newColorInput = ref('')
const newRamInput = ref('')
const newStorageInput = ref('')

const basePath = computed(() => {
  const name = props.productName?.trim() || 'TECH'
  return name
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toUpperCase()
    .replace(/[^A-Z0-9]/g, '-')
    .replace(/-+/g, '-')
    .slice(0, 8)
})

const basePrice = ref<number>(25000000)
const baseStock = ref<number>(20)

// Quick Bulk values
const bulkPrice = ref<number | null>(null)
const bulkStock = ref<number | null>(null)

// 3. Estimate combinations count
const estimatedCombinationsCount = computed(() => {
  const cCount = selectedColors.value.length || 1
  const rCount = selectedRams.value.length || 1
  const sCount = selectedStorages.value.length || 1

  // If nothing is selected at all, return 0
  if (
    selectedColors.value.length === 0 &&
    selectedRams.value.length === 0 &&
    selectedStorages.value.length === 0
  ) {
    return 0
  }
  return cCount * rCount * sCount
})

// Handlers for Colors
function addColor() {
  const val = newColorInput.value.trim()
  if (val && !selectedColors.value.includes(val)) {
    selectedColors.value.push(val)
  }
  newColorInput.value = ''
}

function removeColor(idx: number) {
  selectedColors.value.splice(idx, 1)
}

function toggleColorPreset(color: string) {
  const idx = selectedColors.value.indexOf(color)
  if (idx >= 0) {
    selectedColors.value.splice(idx, 1)
  } else {
    selectedColors.value.push(color)
  }
}

// Handlers for RAM
function toggleRam(ram: string) {
  const idx = selectedRams.value.indexOf(ram)
  if (idx >= 0) {
    selectedRams.value.splice(idx, 1)
  } else {
    selectedRams.value.push(ram)
  }
}

function addCustomRam() {
  const val = newRamInput.value.trim().toUpperCase()
  if (val && !selectedRams.value.includes(val)) {
    selectedRams.value.push(val)
  }
  newRamInput.value = ''
}

// Handlers for Storage
function toggleStorage(st: string) {
  const idx = selectedStorages.value.indexOf(st)
  if (idx >= 0) {
    selectedStorages.value.splice(idx, 1)
  } else {
    selectedStorages.value.push(st)
  }
}

function addCustomStorage() {
  const val = newStorageInput.value.trim().toUpperCase()
  if (val && !selectedStorages.value.includes(val)) {
    selectedStorages.value.push(val)
  }
  newStorageInput.value = ''
}

function resetGenerator() {
  selectedColors.value = []
  selectedRams.value = []
  selectedStorages.value = []
}

// Generate Cartesian Combinations
function generateCombinations() {
  const colors = selectedColors.value.length > 0 ? selectedColors.value : ['Tiêu chuẩn']
  const rams = selectedRams.value.length > 0 ? selectedRams.value : ['']
  const storages = selectedStorages.value.length > 0 ? selectedStorages.value : ['']

  const result: VariantItem[] = []
  const defImg = props.availableImages[0] || ''
  const prefix = basePath.value

  for (const c of colors) {
    for (const r of rams) {
      for (const s of storages) {
        const parts = [c, r, s].filter(Boolean)
        const skuName = parts.join(' - ')

        // Create a clean short SKU
        const shortC = c
          .normalize('NFD')
          .replace(/[\u0300-\u036f]/g, '')
          .replace(/[^a-zA-Z0-9]/g, '')
          .toUpperCase()
          .slice(0, 3)

        const skuParts = [prefix, shortC, r.replace(/\s+/g, ''), s.replace(/\s+/g, '')].filter(
          Boolean
        )
        const sku = skuParts.join('-')

        // Heuristic: automatically give higher storage a tiered price increment if desired
        let calculatedPrice = basePrice.value
        if (s === '512GB') calculatedPrice += 3000000
        else if (s === '1TB') calculatedPrice += 7000000
        else if (s === '2TB') calculatedPrice += 14000000

        if (r === '32GB') calculatedPrice += 4000000
        else if (r === '64GB') calculatedPrice += 8000000

        result.push({
          sku_name: skuName,
          sku: sku,
          price: calculatedPrice,
          stock: baseStock.value,
          image_url: defImg,
        })
      }
    }
  }

  emit('update:variants', result)
  activeMode.value = 'table'
}

// Table / Manual handlers
function addVariantRow() {
  const defImg = props.availableImages[0] || ''
  const updated = [...props.variants]
  updated.push({
    sku_name: '',
    sku: '',
    price: updated[0]?.price || basePrice.value,
    stock: 20,
    image_url: defImg,
  })
  emit('update:variants', updated)
}

function removeVariantRow(idx: number) {
  if (props.variants.length > 1) {
    const updated = [...props.variants]
    updated.splice(idx, 1)
    emit('update:variants', updated)
  }
}

function clearAllVariants() {
  emit('update:variants', [
    {
      sku_name: 'Bản Tiêu Chuẩn',
      sku: '',
      price: basePrice.value,
      stock: 20,
      image_url: props.availableImages[0] || '',
    },
  ])
}

function applyBulkPrice() {
  if (bulkPrice.value !== null && bulkPrice.value >= 0) {
    const updated = props.variants.map((v) => ({
      ...v,
      price: bulkPrice.value as number,
    }))
    emit('update:variants', updated)
    bulkPrice.value = null
  }
}

function applyBulkStock() {
  if (bulkStock.value !== null && bulkStock.value >= 0) {
    const updated = props.variants.map((v) => ({
      ...v,
      stock: bulkStock.value as number,
    }))
    emit('update:variants', updated)
    bulkStock.value = null
  }
}
</script>
