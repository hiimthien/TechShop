<template>
  <div class="space-y-3 pt-3 border-t border-outline-variant/40">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-primary flex items-center gap-1.5">
          <span class="material-symbols-outlined text-[18px]">tune</span>
          Biến thể sản phẩm (Màu sắc / RAM / Dung lượng)
        </h3>
        <p class="text-[11px] text-on-surface-variant">
          Thiết lập từng phiên bản, giá bán, tồn kho và gán hình ảnh riêng biệt cho từng màu sắc.
        </p>
      </div>
      <button
        type="button"
        @click="addVariantRow"
        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-primary text-primary hover:bg-primary/5 transition-all text-xs font-bold"
      >
        <span class="material-symbols-outlined text-[16px]">add</span>
        Thêm phiên bản
      </button>
    </div>

    <!-- Variants Table -->
    <div class="border border-outline-variant/40 rounded-xl overflow-hidden bg-surface-container-lowest">
      <table class="w-full text-left text-xs border-collapse">
        <thead class="bg-surface-container-low text-on-surface-variant font-bold uppercase border-b border-outline-variant/40">
          <tr>
            <th class="py-2.5 px-3">Tên phiên bản / Màu sắc *</th>
            <th class="py-2.5 px-3">Mã SKU</th>
            <th class="py-2.5 px-3 w-32">Giá bán (₫) *</th>
            <th class="py-2.5 px-3 w-20">Tồn kho</th>
            <th class="py-2.5 px-3 w-36">Ảnh gắn màu</th>
            <th class="py-2.5 px-2 text-center w-12">Xóa</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/30">
          <tr v-for="(v, vIdx) in variants" :key="vIdx" class="hover:bg-surface-container-low/40">
            <td class="py-2 px-3">
              <input
                v-model="v.sku_name"
                required
                type="text"
                placeholder="VD: Titan Sa Mạc - 256GB"
                class="w-full px-2.5 py-1.5 rounded-lg border border-outline-variant/50 focus:border-primary focus:ring-1 focus:ring-primary/20 text-xs bg-surface"
              />
            </td>
            <td class="py-2 px-3">
              <input
                v-model="v.sku"
                type="text"
                placeholder="Tự sinh nếu trống"
                class="w-full px-2.5 py-1.5 rounded-lg border border-outline-variant/50 focus:border-primary focus:ring-1 focus:ring-primary/20 text-xs bg-surface uppercase font-mono"
              />
            </td>
            <td class="py-2 px-3">
              <input
                v-model.number="v.price"
                required
                type="number"
                min="0"
                step="10000"
                class="w-full px-2.5 py-1.5 rounded-lg border border-outline-variant/50 focus:border-primary focus:ring-1 focus:ring-primary/20 text-xs font-semibold text-primary bg-surface"
              />
            </td>
            <td class="py-2 px-3">
              <input
                v-model.number="v.stock"
                required
                type="number"
                min="0"
                class="w-full px-2.5 py-1.5 rounded-lg border border-outline-variant/50 focus:border-primary focus:ring-1 focus:ring-primary/20 text-xs bg-surface text-center"
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
                  class="w-full px-2 py-1.5 rounded-lg border border-outline-variant/50 text-[11px] bg-surface"
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
</template>

<script setup lang="ts">
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
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'update:variants', val: VariantItem[]): void
}>()

function addVariantRow() {
  const defImg = props.availableImages[0] || ''
  const updated = [...props.variants]
  updated.push({
    sku_name: '',
    sku: '',
    price: updated[0]?.price || 1000000,
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
</script>

