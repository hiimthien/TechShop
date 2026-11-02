<template>
  <BaseModal
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    :title="isEditing ? 'Chỉnh sửa thông tin sản phẩm' : 'Thêm sản phẩm mới vào hệ thống'"
    max-width="max-w-4xl"
  >
    <form id="productForm" @submit.prevent="handleSubmit" class="space-y-5">
      <!-- 1. General Info -->
      <div class="space-y-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">
            Tên sản phẩm *
          </label>
          <input
            v-model="form.name"
            required
            type="text"
            placeholder="VD: iPhone 16 Pro Max 256GB Chính Hãng VNA"
            class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm bg-surface font-medium"
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">
              Thương hiệu *
            </label>
            <select
              v-model="form.brand_id"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm bg-surface font-medium"
            >
              <option value="" disabled>-- Chọn thương hiệu --</option>
              <option v-for="b in brands" :key="b.id" :value="b.id">
                {{ b.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">
              Ngành hàng / Danh mục *
            </label>
            <select
              v-model="form.category_id"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm bg-surface font-medium"
            >
              <option value="" disabled>-- Chọn danh mục --</option>
              <option v-for="c in categories" :key="c.id" :value="c.id">
                {{ c.name }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- 2. Multiple Images Uploader Component -->
      <ProductImageUploader v-model:images="form.images" />

      <!-- 3. Variants Manager Component -->
      <ProductVariantManager
        v-model:variants="form.variants"
        :available-images="form.images"
      />

      <!-- 4. Description & Status -->
      <div class="space-y-4 pt-3 border-t border-outline-variant/40">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">
            Mô tả chi tiết sản phẩm
          </label>
          <textarea
            v-model="form.description"
            rows="3"
            placeholder="Nhập thông tin giới thiệu, thông số nổi bật..."
            class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm bg-surface"
          ></textarea>
        </div>

        <div class="flex items-center gap-3">
          <input
            id="modal_is_active"
            v-model="form.is_active"
            type="checkbox"
            class="w-4 h-4 rounded text-primary focus:ring-primary border-outline-variant"
          />
          <label for="modal_is_active" class="text-sm font-semibold text-on-surface cursor-pointer">
            Hiển thị sản phẩm trên cửa hàng (Đang mở bán)
          </label>
        </div>
      </div>
    </form>

    <template #footer>
      <button
        type="button"
        @click="$emit('update:modelValue', false)"
        class="px-4 py-2.5 rounded-xl border border-outline-variant/60 text-xs font-bold text-on-surface-variant hover:bg-surface-container transition-colors"
      >
        Hủy bỏ
      </button>
      <button
        type="submit"
        form="productForm"
        :disabled="isSubmitting"
        class="px-5 py-2.5 rounded-xl bg-primary text-on-primary text-xs font-bold hover:bg-primary-container transition-all shadow-md shadow-primary/20 disabled:opacity-50 inline-flex items-center gap-2"
      >
        <span v-if="isSubmitting" class="material-symbols-outlined text-[16px] animate-spin">sync</span>
        <span>{{ isSubmitting ? 'Đang lưu...' : isEditing ? 'Cập nhật sản phẩm' : 'Lưu sản phẩm mới' }}</span>
      </button>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import BaseModal from '@/core/components/BaseModal.vue'
import ProductImageUploader from './ProductImageUploader.vue'
import ProductVariantManager, { type VariantItem } from './ProductVariantManager.vue'
import { adminService } from '../services/adminService'

interface BrandItem {
  id: number
  name: string
}

interface CategoryItem {
  id: number
  name: string
}

interface ProductFormData {
  name: string
  brand_id: number | ''
  category_id: number | ''
  images: string[]
  variants: VariantItem[]
  description: string
  is_active: boolean
}

interface ProductVariantRaw {
  sku?: string
  price?: number | string
  stock?: number
  image_url?: string | null
  options?: (string | { value: string })[]
}

export interface ProductItemData {
  id?: number
  name?: string
  brand_id?: number | ''
  category_id?: number | ''
  brand?: { id: number; name: string }
  category?: { id: number; name: string }
  thumbnail?: string | null
  images?: (string | { image_url: string })[]
  variants?: ProductVariantRaw[]
  description?: string | null
  is_active?: boolean | number
}

interface Props {
  modelValue: boolean
  isEditing: boolean
  initialData?: ProductItemData | null
  categories: CategoryItem[]
  brands: BrandItem[]
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'update:modelValue', val: boolean): void
  (e: 'saved'): void
}>()

const isSubmitting = ref(false)

const defaultForm: ProductFormData = {
  name: '',
  brand_id: '',
  category_id: '',
  images: [],
  variants: [
    {
      sku_name: 'Bản Tiêu Chuẩn',
      sku: '',
      price: 10000000,
      stock: 20,
      image_url: '',
    },
  ],
  description: '',
  is_active: true,
}

const form = ref<ProductFormData>({ ...defaultForm })

watch(
  () => props.modelValue,
  (newVal) => {
    if (newVal) {
      if (props.isEditing && props.initialData) {
        const prod = props.initialData
        const imgList: string[] = []
        if (prod.images && prod.images.length > 0) {
          prod.images.forEach((im) => {
            const url = typeof im === 'string' ? im : im.image_url
            if (url && !imgList.includes(url)) imgList.push(url)
          })
        }
        if (prod.thumbnail && !imgList.includes(prod.thumbnail)) {
          imgList.unshift(prod.thumbnail)
        }

        const varList: VariantItem[] =
          prod.variants && prod.variants.length > 0
            ? prod.variants.map((v) => ({
                sku_name:
                  (typeof v.options?.[0] === 'string' ? v.options[0] : v.options?.[0]?.value) ||
                  v.sku ||
                  'Phiên bản',
                sku: v.sku || '',
                price: Number(v.price) || 0,
                stock: v.stock ?? 10,
                image_url: v.image_url || imgList[0] || '',
              }))
            : [
                {
                  sku_name: 'Bản Tiêu Chuẩn',
                  sku: 'TECH-DEFAULT',
                  price: 10000000,
                  stock: 20,
                  image_url: imgList[0] || '',
                },
              ]

        form.value = {
          name: prod.name || '',
          brand_id: (prod.brand_id as number) || '',
          category_id: (prod.category_id as number) || '',
          images: imgList,
          variants: varList,
          description: prod.description || '',
          is_active: !!prod.is_active,
        }
      } else {
        form.value = {
          name: '',
          brand_id: props.brands[0]?.id || '',
          category_id: props.categories[0]?.id || '',
          images: [],
          variants: [
            {
              sku_name: 'Bản Tiêu Chuẩn',
              sku: '',
              price: 10000000,
              stock: 20,
              image_url: '',
            },
          ],
          description: '',
          is_active: true,
        }
      }
    }
  },
  { immediate: true }
)

async function handleSubmit() {
  isSubmitting.value = true
  try {
    const payload = {
      name: form.value.name,
      brand_id: form.value.brand_id,
      category_id: form.value.category_id,
      description: form.value.description,
      is_active: form.value.is_active,
      images: form.value.images,
      image_url: form.value.images[0] || '',
      price: form.value.variants[0]?.price || 0,
      stock: form.value.variants[0]?.stock || 0,
      variants: form.value.variants.map((v) => ({
        sku_name: v.sku_name || undefined,
        sku: v.sku || undefined,
        price: v.price,
        stock: v.stock,
        image_url: v.image_url,
      })),
    }

    if (props.isEditing && props.initialData?.id) {
      await adminService.updateProduct(props.initialData.id, payload)
    } else {
      await adminService.createProduct(payload)
    }

    emit('update:modelValue', false)
    emit('saved')
  } catch (err: unknown) {
    const errorMsg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    alert(errorMsg || 'Không thể lưu sản phẩm!')
  } finally {
    isSubmitting.value = false
  }
}
</script>

