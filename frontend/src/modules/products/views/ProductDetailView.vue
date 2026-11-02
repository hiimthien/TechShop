<template>
  <div class="max-w-container-max mx-auto px-lg py-8">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-xs text-xs text-on-surface-variant mb-6">
      <RouterLink to="/" class="hover:text-primary transition-colors">Trang chủ</RouterLink>
      <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      <RouterLink to="/products" class="hover:text-primary transition-colors">Sản phẩm</RouterLink>
      <template v-if="product">
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-on-surface font-medium truncate max-w-xs">{{ product.name }}</span>
      </template>
    </nav>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="grid grid-cols-1 lg:grid-cols-12 gap-8 animate-pulse">
      <div class="lg:col-span-7 flex flex-col gap-4">
        <div class="aspect-square bg-surface-container rounded-2xl w-full"></div>
        <div class="flex gap-3">
          <div v-for="i in 4" :key="i" class="w-20 h-20 bg-surface-container rounded-lg"></div>
        </div>
      </div>
      <div class="lg:col-span-5 flex flex-col gap-4">
        <div class="h-6 bg-surface-container rounded w-1/3"></div>
        <div class="h-10 bg-surface-container rounded w-full"></div>
        <div class="h-8 bg-surface-container rounded w-1/2"></div>
        <div class="h-24 bg-surface-container rounded w-full mt-4"></div>
        <div class="h-12 bg-surface-container rounded w-full mt-4"></div>
      </div>
    </div>

    <!-- Error State -->
    <div
      v-else-if="errorMessage || !product"
      class="py-16 text-center bg-surface-container-lowest rounded-2xl border border-outline-variant/30 flex flex-col items-center justify-center gap-4"
    >
      <span class="material-symbols-outlined text-6xl text-error">error</span>
      <h2 class="text-xl font-bold text-on-surface">Không tìm thấy sản phẩm</h2>
      <p class="text-sm text-on-surface-variant max-w-md">
        {{ errorMessage || 'Sản phẩm này có thể đã bị xóa hoặc không còn hoạt động trên hệ thống.' }}
      </p>
      <RouterLink
        to="/products"
        class="px-6 py-2.5 bg-primary text-on-primary font-bold rounded-xl text-sm hover:bg-primary-container transition-all"
      >
        Quay lại danh sách sản phẩm
      </RouterLink>
    </div>

    <!-- Main Product Detail -->
    <div v-else class="space-y-12">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        <!-- Left: Image Gallery -->
        <div class="lg:col-span-7 flex flex-col gap-4">
          <!-- Main Large Image -->
          <div
            class="aspect-square bg-white rounded-2xl p-6 md:p-8 flex items-center justify-center border border-outline-variant/30 relative overflow-hidden group shadow-sm"
          >
            <img
              :src="currentMainImage"
              :alt="product.name"
              class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500"
            />
            <span
              class="absolute top-4 left-4 bg-primary text-on-primary text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm"
            >
              Chính hãng 100%
            </span>
          </div>

          <!-- Thumbnails List -->
          <div v-if="allImages.length > 1" class="flex gap-3 overflow-x-auto pb-2">
            <button
              v-for="(img, idx) in allImages"
              :key="idx"
              @click="handleSelectThumbnail(img)"
              class="flex-shrink-0 w-20 h-20 md:w-24 md:h-24 bg-white rounded-xl border-2 p-2 transition-all active:scale-95"
              :class="
                activeImage === img
                  ? 'border-primary ring-2 ring-primary/20 shadow-sm'
                  : 'border-outline-variant/40 hover:border-primary/50'
              "
            >
              <img :src="img" :alt="`Thumbnail ${idx + 1}`" class="w-full h-full object-contain" />
            </button>
          </div>
        </div>

        <!-- Right: Info & Actions -->
        <div class="lg:col-span-5 flex flex-col gap-6">
          <div>
            <div class="flex items-center gap-2 mb-2">
              <span
                v-if="product.brand"
                class="px-2.5 py-0.5 bg-primary/10 text-primary text-xs font-bold rounded-full"
              >
                {{ product.brand }}
              </span>
              <span
                v-if="product.category"
                class="px-2.5 py-0.5 bg-surface-container-high text-on-surface-variant text-xs font-medium rounded-full"
              >
                {{ product.category }}
              </span>
            </div>

            <h1 class="text-2xl md:text-3xl font-black text-on-surface leading-tight">
              {{ product.name }}
            </h1>

            <div class="flex items-center gap-2 mt-3">
              <div class="flex text-secondary">
                <span
                  v-for="s in 5"
                  :key="s"
                  class="material-symbols-outlined text-[18px]"
                  style="font-variation-settings: 'FILL' 1"
                >
                  star
                </span>
              </div>
              <span class="text-xs text-on-surface-variant font-medium">(Đánh giá 5.0)</span>
              <span class="text-outline text-xs">•</span>
              <span class="text-xs text-primary font-medium">Bảo hành 24 tháng</span>
            </div>
          </div>

          <!-- Price Display -->
          <div class="p-4 bg-surface-container-lowest rounded-xl border border-outline-variant/30 flex items-baseline gap-3">
            <span class="text-2xl md:text-3xl font-black text-primary">
              {{ formatPrice(currentPrice) }}
            </span>
          </div>

          <!-- Variants Selection -->
          <div v-if="product.variants && product.variants.length > 0" class="flex flex-col gap-3">
            <div class="flex justify-between items-center text-sm font-semibold">
              <span class="text-on-surface">Chọn phiên bản:</span>
              <span v-if="selectedVariant" class="text-xs text-outline font-normal">
                SKU: {{ selectedVariant.sku }}
              </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
              <button
                v-for="variant in product.variants"
                :key="variant.id"
                type="button"
                @click="selectVariant(variant)"
                class="p-2.5 rounded-xl border text-left transition-all text-xs flex items-center gap-2.5"
                :class="
                  selectedVariant?.id === variant.id
                    ? 'border-2 border-primary bg-primary/5 text-primary font-bold shadow-sm'
                    : 'border-outline-variant/60 hover:border-primary/50 text-on-surface'
                "
              >
                <img
                  v-if="variant.image_url"
                  :src="variant.image_url"
                  class="w-10 h-10 object-contain rounded-lg border border-outline-variant/40 p-0.5 shrink-0 bg-white"
                  alt=""
                />
                <div class="flex-1 min-w-0">
                  <span class="font-medium text-sm block truncate">
                    {{ variant.options?.join(', ') || variant.sku }}
                  </span>
                  <span class="text-xs font-semibold block text-primary">
                    {{ formatPrice(variant.price) }}
                  </span>
                </div>
              </button>
            </div>
          </div>

          <!-- Quantity Selector -->
          <div class="flex items-center gap-4">
            <span class="text-sm font-semibold text-on-surface">Số lượng:</span>
            <div class="flex items-center border border-outline-variant rounded-xl overflow-hidden bg-surface-container-lowest">
              <button
                type="button"
                :disabled="quantity <= 1"
                @click="quantity--"
                class="w-10 h-10 flex items-center justify-center text-on-surface hover:bg-surface-container transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
              >
                <span class="material-symbols-outlined text-[18px]">remove</span>
              </button>
              <input
                type="number"
                v-model.number="quantity"
                min="1"
                class="w-12 text-center text-sm font-bold border-none focus:outline-none bg-transparent"
              />
              <button
                type="button"
                @click="quantity++"
                class="w-10 h-10 flex items-center justify-center text-on-surface hover:bg-surface-container transition-colors"
              >
                <span class="material-symbols-outlined text-[18px]">add</span>
              </button>
            </div>
            <span v-if="selectedVariant?.stock" class="text-xs text-on-surface-variant">
              (Còn {{ selectedVariant.stock }} sản phẩm)
            </span>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col gap-3 pt-2">
            <button
              @click="handleAddToCart"
              type="button"
              class="w-full py-3.5 bg-primary text-on-primary rounded-xl font-bold text-base hover:bg-primary-container transition-all active:scale-[0.98] shadow-md shadow-primary/20 flex items-center justify-center gap-2"
            >
              <span class="material-symbols-outlined">add_shopping_cart</span>
              THÊM VÀO GIỎ HÀNG
            </button>
            <div class="grid grid-cols-2 gap-3">
              <button
                @click="handleBuyNow"
                type="button"
                class="py-3 border-2 border-primary text-primary rounded-xl font-bold text-sm hover:bg-primary/5 transition-all active:scale-[0.98] flex items-center justify-center gap-1.5"
              >
                <span class="material-symbols-outlined text-[18px]">bolt</span>
                MUA NGAY
              </button>
              <button
                type="button"
                class="py-3 border border-outline-variant text-on-surface rounded-xl font-bold text-sm hover:bg-surface-container transition-all active:scale-[0.98] flex items-center justify-center gap-1.5"
              >
                <span class="material-symbols-outlined text-[18px]">credit_card</span>
                TRẢ GÓP 0%
              </button>
            </div>
          </div>

          <!-- Added Alert Feedback -->
          <div
            v-if="addedToCartSuccess"
            class="p-3 bg-secondary/10 border border-secondary/30 rounded-xl text-secondary text-xs font-bold flex items-center gap-2"
          >
            <span class="material-symbols-outlined text-sm">check_circle</span>
            Đã thêm {{ quantity }} sản phẩm vào giỏ hàng thành công!
          </div>

          <!-- Commitments Policy -->
          <div class="grid grid-cols-3 gap-3 p-4 bg-surface-container-lowest rounded-xl border border-outline-variant/30 text-center">
            <div class="flex flex-col items-center gap-1">
              <span class="material-symbols-outlined text-primary text-2xl">local_shipping</span>
              <span class="text-[11px] font-bold text-on-surface">Miễn phí ship</span>
              <span class="text-[10px] text-on-surface-variant">Đơn từ 500k</span>
            </div>
            <div class="flex flex-col items-center gap-1">
              <span class="material-symbols-outlined text-primary text-2xl">published_with_changes</span>
              <span class="text-[11px] font-bold text-on-surface">Đổi mới 30 ngày</span>
              <span class="text-[10px] text-on-surface-variant">Lỗi là đổi mới</span>
            </div>
            <div class="flex flex-col items-center gap-1">
              <span class="material-symbols-outlined text-primary text-2xl">verified</span>
              <span class="text-[11px] font-bold text-on-surface">100% Chính hãng</span>
              <span class="text-[10px] text-on-surface-variant">Bảo hành 24T</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Description & Specs Section -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pt-8 border-t border-outline-variant/30">
        <!-- Description -->
        <div class="lg:col-span-7 flex flex-col gap-4">
          <h2 class="text-xl font-bold text-on-surface flex items-center gap-2">
            <span class="w-1.5 h-6 bg-primary rounded-full"></span>
            Mô tả sản phẩm
          </h2>
          <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 text-sm leading-relaxed text-on-surface-variant whitespace-pre-line">
            {{ product.description || 'Chưa có thông tin mô tả chi tiết cho sản phẩm này.' }}
          </div>
        </div>

        <!-- Specifications -->
        <div class="lg:col-span-5 flex flex-col gap-4">
          <h2 class="text-xl font-bold text-on-surface flex items-center gap-2">
            <span class="w-1.5 h-6 bg-secondary rounded-full"></span>
            Thông số kỹ thuật
          </h2>
          <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 overflow-hidden text-sm">
            <table class="w-full text-left border-collapse">
              <tbody>
                <tr class="border-b border-outline-variant/20">
                  <td class="p-3 text-on-surface-variant w-1/3 bg-surface-container-low font-medium">Thương hiệu</td>
                  <td class="p-3 text-on-surface font-semibold">{{ product.brand || 'Đang cập nhật' }}</td>
                </tr>
                <tr class="border-b border-outline-variant/20">
                  <td class="p-3 text-on-surface-variant bg-surface-container-low font-medium">Danh mục</td>
                  <td class="p-3 text-on-surface font-semibold">{{ product.category || 'Đang cập nhật' }}</td>
                </tr>
                <tr class="border-b border-outline-variant/20">
                  <td class="p-3 text-on-surface-variant bg-surface-container-low font-medium">Mã SKU</td>
                  <td class="p-3 text-on-surface font-semibold">{{ selectedVariant?.sku || 'Đang cập nhật' }}</td>
                </tr>
                <tr class="border-b border-outline-variant/20">
                  <td class="p-3 text-on-surface-variant bg-surface-container-low font-medium">Tình trạng</td>
                  <td class="p-3 text-secondary font-bold">Còn hàng ({{ selectedVariant?.stock || 10 }} cái)</td>
                </tr>
                <tr>
                  <td class="p-3 text-on-surface-variant bg-surface-container-low font-medium">Bảo hành</td>
                  <td class="p-3 text-on-surface font-semibold">24 tháng chính hãng</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { productService } from '../services/productService'
import { useCartStore } from '@/modules/cart/stores/cartStore'
import type { Product, ProductVariant } from '../types'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()

const product = ref<Product | null>(null)
const isLoading = ref<boolean>(true)
const errorMessage = ref<string>('')

const activeImage = ref<string>('')
const selectedVariant = ref<ProductVariant | null>(null)
const quantity = ref<number>(1)
const addedToCartSuccess = ref<boolean>(false)

function selectVariant(variant: ProductVariant) {
  selectedVariant.value = variant
  if (variant.image_url) {
    activeImage.value = variant.image_url
  }
}

function handleSelectThumbnail(img: string) {
  activeImage.value = img
  const matchingVariant = product.value?.variants?.find((v) => v.image_url === img)
  if (matchingVariant) {
    selectedVariant.value = matchingVariant
  }
}

const fallbackImage =
  'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400"><rect fill="%23f0eded" width="400" height="400"/><text fill="%237b7487" font-family="sans-serif" font-size="20" dy="10.5" font-weight="bold" x="50%" y="50%" text-anchor="middle">TechShop</text></svg>'

const allImages = computed<string[]>(() => {
  if (!product.value) return []
  const list: string[] = []
  if (product.value.thumbnail) {
    list.push(product.value.thumbnail)
  }
  if (product.value.images && product.value.images.length > 0) {
    product.value.images.forEach((img) => {
      if (!list.includes(img)) list.push(img)
    })
  }
  if (product.value.variants && product.value.variants.length > 0) {
    product.value.variants.forEach((v) => {
      if (v.image_url && !list.includes(v.image_url)) {
        list.push(v.image_url)
      }
    })
  }
  return list.length > 0 ? list : [fallbackImage]
})

const currentMainImage = computed<string>(() => {
  return activeImage.value || allImages.value[0] || fallbackImage
})

const currentPrice = computed<number | string | null | undefined>(() => {
  if (selectedVariant.value && selectedVariant.value.price) {
    return selectedVariant.value.price
  }
  return product.value?.starting_price
})

const formatPrice = (price: number | string | null | undefined): string => {
  if (price === null || price === undefined || price === '') return 'Liên hệ'
  const numeric = typeof price === 'string' ? parseFloat(price) : price
  if (isNaN(numeric)) return 'Liên hệ'
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(numeric)
}

const handleAddToCart = () => {
  if (!product.value) return

  const variant = selectedVariant.value || product.value.variants?.[0]
  if (!variant) return

  const rawPrice = typeof variant.price === 'string' ? parseFloat(variant.price) : variant.price

  cartStore.addItem({
    variant_id: variant.id,
    quantity: quantity.value,
    product_name: product.value.name,
    product_slug: product.value.slug,
    variant_sku: variant.sku,
    variant_option: variant.options?.join(', ') || undefined,
    price: isNaN(rawPrice) ? 0 : rawPrice,
    thumbnail: currentMainImage.value,
    stock: variant.stock,
  })

  addedToCartSuccess.value = true
  setTimeout(() => {
    addedToCartSuccess.value = false
  }, 3500)
}

const handleBuyNow = () => {
  handleAddToCart()
  router.push('/cart')
}

const fetchProductDetail = async () => {
  const slug = route.params.slug as string
  if (!slug) {
    errorMessage.value = 'Đường dẫn sản phẩm không hợp lệ.'
    isLoading.value = false
    return
  }

  isLoading.value = true
  errorMessage.value = ''

  try {
    const data = await productService.getProduct(slug)
    product.value = data

    if (data.images && data.images.length > 0 && data.images[0]) {
      activeImage.value = data.images[0]
    } else if (data.thumbnail) {
      activeImage.value = data.thumbnail
    }

    if (data.variants && data.variants.length > 0 && data.variants[0]) {
      selectedVariant.value = data.variants[0]
    }
  } catch (error: unknown) {
    console.error('Lỗi khi tải chi tiết sản phẩm:', error)
    const errData = (error as { response?: { data?: { message?: string } } })?.response?.data
    errorMessage.value =
      errData?.message || 'Không thể tải thông tin sản phẩm. Vui lòng thử lại sau.'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchProductDetail()
})
</script>
