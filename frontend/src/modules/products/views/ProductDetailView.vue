<template>
  <div class="min-h-screen bg-surface py-6 px-4 md:px-8">
    <!-- 1. Trạng thái Đang tải (Loading Skeleton) -->
    <div v-if="isLoading" class="max-w-7xl mx-auto space-y-6 animate-pulse">
      <div class="h-4 bg-surface-container rounded-lg w-1/4"></div>
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-5 aspect-square bg-surface-container rounded-3xl"></div>
        <div class="lg:col-span-7 space-y-4">
          <div class="h-8 bg-surface-container rounded-xl w-3/4"></div>
          <div class="h-16 bg-surface-container rounded-2xl w-full"></div>
          <div class="h-32 bg-surface-container rounded-2xl w-full"></div>
        </div>
      </div>
    </div>

    <!-- 2. Trạng thái Lỗi (Error State) -->
    <div v-else-if="errorMessage || !product" class="max-w-7xl mx-auto py-16 text-center space-y-4">
      <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mx-auto text-outline">
        <span class="material-symbols-outlined text-[40px]">error_outline</span>
      </div>
      <h2 class="text-xl font-bold text-on-surface">Không tìm thấy sản phẩm</h2>
      <p class="text-sm text-on-surface-variant max-w-md mx-auto">
        {{ errorMessage || 'Sản phẩm bạn đang tìm kiếm không tồn tại hoặc đã ngừng kinh doanh.' }}
      </p>
      <RouterLink
        to="/products"
        class="inline-block mt-4 px-6 py-2.5 bg-primary text-on-primary text-xs font-bold rounded-xl hover:bg-primary-hover transition-colors"
      >
        Quay lại danh sách sản phẩm
      </RouterLink>
    </div>

    <!-- 3. Nội dung Chi tiết Sản phẩm -->
    <div v-else class="max-w-7xl mx-auto space-y-8">
      <!-- Breadcrumb Navigation -->
      <nav class="flex items-center gap-2 text-xs font-medium text-on-surface-variant overflow-x-auto whitespace-nowrap">
        <RouterLink to="/" class="hover:text-primary transition-colors">Trang chủ</RouterLink>
        <span class="text-outline">/</span>
        <RouterLink
          v-if="product.category"
          :to="{ path: '/products', query: { category_id: product.category_id } }"
          class="hover:text-primary transition-colors"
        >
          {{ product.category }}
        </RouterLink>
        <span v-if="product.category" class="text-outline">/</span>
        <span class="text-on-surface font-semibold truncate">{{ product.name }}</span>
      </nav>

      <!-- Product Header: Title & Badges -->
      <div class="border-b border-outline-variant/30 pb-3 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3 flex-wrap">
          <h1 class="text-xl md:text-2xl font-black text-on-surface leading-tight">
            {{ product.name }}
          </h1>
          <span class="px-2.5 py-0.5 bg-primary/10 text-primary border border-primary/20 text-[11px] font-bold rounded-md uppercase">
            Chính Hãng VN/A
          </span>
        </div>

        <div class="flex items-center gap-4 text-xs font-semibold text-on-surface-variant">
          <div class="flex items-center gap-1 text-secondary">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1">star</span>
            <span class="font-bold text-on-surface">5.0</span>
            <span class="text-outline font-normal">(128 đánh giá)</span>
          </div>
          <button
            type="button"
            @click="isFavorite = !isFavorite"
            class="flex items-center gap-1 transition-colors hover:text-secondary"
            :class="isFavorite ? 'text-secondary' : 'text-on-surface-variant'"
          >
            <span
              class="material-symbols-outlined text-[18px]"
              :style="isFavorite ? 'font-variation-settings: \'FILL\' 1' : ''"
            >
              favorite
            </span>
            <span>{{ isFavorite ? 'Đã thích' : 'Yêu thích' }}</span>
          </button>
          <span class="text-outline">•</span>
          <span class="flex items-center gap-1 text-primary">
            <span class="material-symbols-outlined text-[16px]">verified</span>
            Bảo hành 24 tháng
          </span>
        </div>
      </div>

      <!-- Main Section: Image Gallery & Buy Options -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- LEFT: Gallery & Commitments (Col 5) -->
        <div class="lg:col-span-5">
          <ProductGallery
            :current-image="currentMainImage"
            :images="allImages"
            :product-name="product.name"
            @select-image="handleSelectThumbnail"
          />
        </div>

        <!-- RIGHT: Pricing, Storage, Color & CTA (Col 7) -->
        <div class="lg:col-span-7 flex flex-col gap-5">
          <!-- Hộp hiển thị giá & trả góp -->
          <ProductPricingBox
            :price="currentPrice"
            :original-price="currentOriginalPrice"
          />

          <!-- Bộ chọn biến thể Đa chiều (Bộ nhớ + Màu sắc) -->
          <ProductVariantSelector
            :storage-options="storageOptions"
            v-model:selected-storage="selectedStorage"
            :variants="colorVariants"
            :selected-variant-id="selectedVariant?.id"
            :active-color-name="activeColorName"
            @select-variant="selectVariant"
          />

          <!-- Khối Khuyến mãi đặc quyền TechShop -->
          <div class="rounded-2xl border-2 border-primary/40 overflow-hidden bg-surface-container-lowest shadow-xs">
            <div class="bg-primary text-on-primary px-4 py-2 flex items-center gap-2">
              <span class="material-symbols-outlined text-[18px]">card_giftcard</span>
              <span class="text-xs font-black uppercase tracking-wider">Khuyến mãi đặc quyền TechShop</span>
            </div>
            <div class="p-3.5 md:p-4 space-y-2.5 text-xs text-on-surface">
              <div
                v-for="promo in promotionsList"
                :key="promo.id"
                class="flex items-start gap-2.5"
              >
                <span class="w-4 h-4 rounded-full bg-primary text-on-primary font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">
                  {{ promo.id }}
                </span>
                <span class="leading-relaxed">
                  <strong>{{ promo.title }}:</strong> {{ promo.content }}
                </span>
              </div>
            </div>
          </div>

          <!-- Main Action Buttons (Mua ngay, Trả góp, Thêm vào giỏ) -->
          <div class="space-y-3 pt-2">
            <button
              @click="handleBuyNow"
              type="button"
              class="w-full py-3.5 bg-primary hover:bg-primary-hover text-on-primary rounded-xl font-black text-sm uppercase tracking-wider transition-all active:scale-[0.99] shadow-lg shadow-primary/25 flex flex-col items-center justify-center"
            >
              <span>MUA NGAY</span>
              <span class="text-[11px] font-normal opacity-90">Giao tận nơi hoặc nhận tại siêu thị</span>
            </button>

            <div class="grid grid-cols-2 gap-3">
              <button
                type="button"
                @click="handleBuyNow"
                class="py-2.5 px-3 bg-secondary hover:bg-secondary-hover text-on-secondary rounded-xl font-bold text-xs uppercase tracking-wider transition-all flex flex-col items-center justify-center"
              >
                <span>TRẢ GÓP 0%</span>
                <span class="text-[10px] font-normal opacity-90">Duyệt nhanh qua điện thoại</span>
              </button>
              <button
                @click="handleAddToCart"
                type="button"
                class="py-2.5 px-3 border-2 border-primary text-primary hover:bg-primary/5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5"
              >
                <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                <span>THÊM VÀO GIỎ</span>
              </button>
            </div>

            <!-- Feedback thông báo thêm vào giỏ thành công -->
            <Transition
              enter-active-class="transition duration-300 ease-out"
              enter-from-class="opacity-0 translate-y-2"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition duration-200 ease-in"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
            >
              <div
                v-if="addedToCartSuccess"
                class="p-3 bg-emerald-50 border border-emerald-300 rounded-xl text-emerald-800 text-xs font-bold flex items-center gap-2"
              >
                <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                Đã thêm sản phẩm vào giỏ hàng thành công!
              </div>
            </Transition>
          </div>
        </div>
      </div>

      <!-- Bottom Grid: Technical Specifications & Detailed Description -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pt-8 border-t border-outline-variant/30 items-start">
        <!-- LEFT: Bài viết mô tả chi tiết (Col 7) -->
        <div class="lg:col-span-7 flex flex-col gap-4">
          <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 space-y-4">
            <h2 class="text-lg font-black text-on-surface uppercase tracking-wider flex items-center gap-2">
              <span class="w-1.5 h-5 bg-primary rounded-full"></span>
              Đặc điểm nổi bật & Đánh giá chi tiết
            </h2>
            <div class="prose max-w-none text-xs md:text-sm leading-relaxed text-on-surface-variant whitespace-pre-line space-y-3">
              {{ product.description || 'Chưa có thông tin mô tả chi tiết cho sản phẩm này.' }}
            </div>
          </div>
        </div>

        <!-- RIGHT: Bảng thông số kỹ thuật chi tiết (Col 5) -->
        <div class="lg:col-span-5 flex flex-col gap-4 sticky top-20">
          <ProductSpecsTable :specs="specificationsList" />
        </div>
      </div>

      <!-- Khối Đánh giá & Bình luận khách hàng -->
      <ProductReviewsSection
        v-if="product"
        :product-id="product.id"
        :product-name="product.name"
      />

      <!-- Khối "CÓ THỂ BẠN CŨNG THÍCH" (Sản phẩm tương tự) -->
      <div v-if="relatedProductsList.length > 0" class="space-y-4 pt-6">
        <div class="flex items-center justify-between border-b border-outline-variant/30 pb-3">
          <h2 class="text-lg md:text-xl font-black text-on-surface uppercase tracking-wider flex items-center gap-2">
            <span class="w-2 h-6 bg-secondary rounded-full"></span>
            Có thể bạn cũng thích
          </h2>
          <RouterLink
            :to="{ path: '/products', query: { category_id: product.category_id } }"
            class="text-xs font-bold text-primary hover:underline flex items-center gap-1"
          >
            Xem tất cả
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
          </RouterLink>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
          <RouterLink
            v-for="rel in relatedProductsList"
            :key="rel.id"
            :to="`/products/${rel.slug}`"
            class="p-4 bg-surface-container-lowest rounded-2xl border border-outline-variant/30 hover:border-primary hover:shadow-md transition-all flex flex-col justify-between group"
          >
            <div class="relative aspect-square mb-3 flex items-center justify-center overflow-hidden rounded-xl bg-white p-2">
              <img
                :src="rel.thumbnail || fallbackImage"
                :alt="rel.name"
                class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
              />
              <span class="absolute top-1 left-1 px-2 py-0.5 bg-secondary text-on-secondary font-bold text-[9px] rounded-md">
                -8%
              </span>
            </div>

            <div class="space-y-2">
              <span v-if="rel.brand" class="text-[10px] font-bold text-on-surface-variant uppercase">
                {{ rel.brand }}
              </span>
              <h3 class="text-xs font-bold text-on-surface line-clamp-2 group-hover:text-primary transition-colors">
                {{ rel.name }}
              </h3>
              <div class="flex items-baseline gap-2">
                <span class="text-sm font-black text-primary">
                  {{ formatPrice(rel.starting_price) }}
                </span>
              </div>
            </div>
          </RouterLink>
        </div>
      </div>
    </div>

    <!-- Sticky Buy Bar ghim chân trang khi cuộn -->
    <ProductStickyBuyBar
      :show="showStickyBar"
      :name="product?.name || ''"
      :image="currentMainImage"
      :price="currentPrice"
      :original-price="currentOriginalPrice"
      :selected-variant-label="activeColorName"
      @add-to-cart="handleAddToCart"
      @buy-now="handleBuyNow"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, shallowRef, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { provideProductContext } from '../context/productContext'
import { productService } from '../services/productService'
import type {
  Product,
  ProductVariant,
  PromotionItem,
} from '../types'
import { useCartStore } from '@/modules/cart/stores/cartStore'
import { useFormatters } from '@/core/composables/useFormatters'
import { useToast } from '@/core/composables/useToast'

// Sub-components
import ProductGallery from '../components/ProductGallery.vue'
import ProductPricingBox from '../components/ProductPricingBox.vue'
import ProductVariantSelector from '../components/ProductVariantSelector.vue'
import ProductSpecsTable from '../components/ProductSpecsTable.vue'
import ProductStickyBuyBar from '../components/ProductStickyBuyBar.vue'
import ProductReviewsSection from '../components/ProductReviewsSection.vue'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()
const toast = useToast()
const { formatPrice } = useFormatters()

const product = shallowRef<Product | null>(null)
const isLoading = ref<boolean>(true)
const errorMessage = ref<string>('')

const activeImage = ref<string>('')
const selectedVariant = ref<ProductVariant | null>(null)
const selectedStorage = ref<string>('')
const isFavorite = ref<boolean>(false)
const addedToCartSuccess = ref<boolean>(false)
const showStickyBar = ref<boolean>(false)

const fallbackImage =
  'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400"><rect fill="%23f0eded" width="400" height="400"/><text fill="%237b7487" font-family="sans-serif" font-size="20" dy="10.5" font-weight="bold" x="50%" y="50%" text-anchor="middle">TechShop</text></svg>'

// Danh sách ảnh
const allImages = computed<string[]>(() => {
  if (!product.value) return []
  const list: string[] = []
  if (product.value.thumbnail) list.push(product.value.thumbnail)
  if (product.value.images) {
    product.value.images.forEach((img: string) => {
      if (!list.includes(img)) list.push(img)
    })
  }
  if (product.value.variants) {
    product.value.variants.forEach((v: ProductVariant) => {
      if (v.image_url && !list.includes(v.image_url)) list.push(v.image_url)
    })
  }
  return list.length > 0 ? list : [fallbackImage]
})

const currentMainImage = computed<string>(() => {
  return activeImage.value || allImages.value[0] || fallbackImage
})

// Tách các phiên bản Dung lượng (128GB, 256GB, 512GB, 1TB...)
const storageOptions = computed<string[]>(() => {
  if (!product.value?.variants) return []
  const storages = new Set<string>()
  product.value.variants.forEach((v: ProductVariant) => {
    const raw = v.options?.join(' ') || v.sku
    const match = raw.match(/\b(64GB|128GB|256GB|512GB|1TB|2TB|16GB|32GB|64GB)\b/i)
    if (match && match[1]) {
      storages.add(match[1].toUpperCase())
    }
  })
  return Array.from(storages)
})

// Các biến thể theo màu sắc
const colorVariants = computed<ProductVariant[]>(() => {
  if (!product.value?.variants) return []
  if (!selectedStorage.value) return product.value.variants

  const filtered = product.value.variants.filter((v: ProductVariant) => {
    const raw = (v.options?.join(' ') || v.sku).toUpperCase()
    return raw.includes(selectedStorage.value)
  })

  return filtered.length > 0 ? filtered : product.value.variants
})

const activeColorName = computed<string>(() => {
  if (!selectedVariant.value) return 'Mặc định'
  const raw = selectedVariant.value.options?.[0] || selectedVariant.value.sku || ''
  if (raw.includes('-')) {
    const parts = raw.split('-')
    const first = parts[0]?.trim()
    if (first) return first
  }
  return (
    raw
      .replace(/\b\d+\s*(GB|TB)(\s*RAM)?\b/gi, '')
      .replace(/\/\s*RAM\b/gi, '')
      .replace(/[-_]/g, ' ')
      .trim() || 'Mặc định'
  )
})

const currentPrice = computed<number>(() => {
  const p = selectedVariant.value?.price ?? product.value?.variants?.[0]?.price ?? product.value?.starting_price ?? 0
  const num = typeof p === 'string' ? parseFloat(p) : p
  return isNaN(num) ? 0 : num
})

const currentOriginalPrice = computed<number | undefined>(() => {
  return currentPrice.value ? Math.round(currentPrice.value * 1.08) : undefined
})

const promotionsList = computed<PromotionItem[]>(() => {
  return product.value?.promotions || []
})

const specificationsList = computed<Record<string, string>>(() => {
  return product.value?.specifications || {}
})

const relatedProductsList = computed<Product[]>(() => {
  return product.value?.related_products || []
})

function selectVariant(variant: ProductVariant) {
  selectedVariant.value = variant
  if (variant.image_url) {
    activeImage.value = variant.image_url
  }
}

function handleSelectThumbnail(img: string) {
  activeImage.value = img
  const matchingVariant = product.value?.variants?.find((v: ProductVariant) => v.image_url === img)
  if (matchingVariant) {
    selectedVariant.value = matchingVariant
  }
}

function handleAddToCart() {
  if (!product.value) return

  const variant = selectedVariant.value || product.value.variants?.[0]
  if (!variant) return

  const rawPrice = currentPrice.value

  cartStore.addItem({
    variant_id: variant.id,
    quantity: 1,
    product_name: product.value.name,
    product_slug: product.value.slug,
    variant_sku: variant.sku,
    variant_option: `${activeColorName.value} ${selectedStorage.value ? `(${selectedStorage.value})` : ''}`.trim(),
    price: isNaN(rawPrice) ? 0 : rawPrice,
    thumbnail: currentMainImage.value,
    stock: variant.stock,
  })

  addedToCartSuccess.value = true
  toast.success(`Đã thêm ${product.value.name} vào giỏ hàng!`, 'Giỏ hàng TechShop')
  setTimeout(() => {
    addedToCartSuccess.value = false
  }, 3500)
}

function handleBuyNow() {
  handleAddToCart()
  router.push('/cart')
}

// Provide full context down the component tree for children to inject
provideProductContext({
  product,
  currentPrice,
  currentOriginalPrice,
  currentMainImage,
  selectedVariant,
  selectedStorage,
  activeColorName,
  handleAddToCart,
  handleBuyNow,
})

let scrollTicking = false
function handleScroll() {
  if (!scrollTicking) {
    window.requestAnimationFrame(() => {
      showStickyBar.value = window.scrollY > 450
      scrollTicking = false
    })
    scrollTicking = true
  }
}

async function fetchProductDetail() {
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

    if (data?.name) {
      document.title = `${data.name} | TechShop`
    }

    if (data.images && data.images.length > 0 && data.images[0]) {
      activeImage.value = data.images[0]
    } else if (data.thumbnail) {
      activeImage.value = data.thumbnail
    }

    if (data.variants && data.variants.length > 0 && data.variants[0]) {
      selectedVariant.value = data.variants[0]
      const match = (data.variants[0].options?.join(' ') || data.variants[0].sku).match(/\b(64GB|128GB|256GB|512GB|1TB|2TB|16GB|32GB)\b/i)
      if (match && match[1]) {
        selectedStorage.value = match[1].toUpperCase()
      }
    }
  } catch (err: unknown) {
    console.error('Lỗi khi tải chi tiết sản phẩm:', err)
    errorMessage.value = 'Không thể tải thông tin sản phẩm. Vui lòng thử lại sau.'
  } finally {
    isLoading.value = false
  }
}

watch(
  () => route.params.slug,
  () => {
    fetchProductDetail()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
)

onMounted(() => {
  fetchProductDetail()
  window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>
