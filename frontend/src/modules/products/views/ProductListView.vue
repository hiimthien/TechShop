<template>
  <div class="max-w-container-max mx-auto px-lg py-8 md:py-12">
    <!-- Breadcrumb & Title -->
    <div class="mb-8">
      <h1 class="text-2xl md:text-3xl font-black text-on-surface">
        Danh sách sản phẩm
      </h1>
      <p class="text-xs md:text-sm text-on-surface-variant mt-1">
        Khám phá các thiết bị công nghệ chính hãng mới nhất
      </p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 items-start">
      <!-- Desktop Sidebar Filter (Ẩn trên màn hình mobile < lg) -->
      <div class="hidden lg:block shrink-0">
        <ProductFilterSidebar
          :categories="categories"
          :brands="brands"
          :selected-category="selectedCategory"
          :selected-brand-ids="selectedBrandIds"
          :selected-min-price="selectedMinPrice"
          :selected-max-price="selectedMaxPrice"
          :selected-ram="selectedRam"
          :selected-storage="selectedStorage"
          :selected-connection="selectedConnection"
          :selected-screen-feature="selectedScreenFeature"
          :in-stock="inStock"
          @update:selected-category="selectedCategory = $event"
          @update:selected-brand-ids="selectedBrandIds = $event"
          @update:selected-min-price="selectedMinPrice = $event"
          @update:selected-max-price="selectedMaxPrice = $event"
          @update:selected-ram="selectedRam = $event"
          @update:selected-storage="selectedStorage = $event"
          @update:selected-connection="selectedConnection = $event"
          @update:selected-screen-feature="selectedScreenFeature = $event"
          @update:in-stock="inStock = $event"
          @apply="handleApplyFilter"
          @reset="handleResetFilter"
        />
      </div>

      <!-- Main Product Section -->
      <section class="flex-grow w-full">
        <!-- Toolbar: Search & Info & Sort -->
        <div
          class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 bg-surface-container-lowest p-4 rounded-2xl border border-outline-variant/30 shadow-sm"
        >
          <div class="flex items-center gap-1.5 text-xs text-on-surface-variant shrink-0">
            <span>Hiển thị</span>
            <strong class="text-primary font-bold px-0.5">{{ products.length }}</strong>
            <span>/</span>
            <strong class="text-on-surface font-bold px-0.5">{{ pagination.total }}</strong>
            <span>sản phẩm</span>
          </div>

          <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <!-- Search Box -->
            <div
              class="flex items-center flex-grow sm:flex-grow-0 sm:w-64 bg-surface-container-low rounded-xl px-3 py-1.5 border border-outline-variant/40 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all"
            >
              <span class="material-symbols-outlined text-outline text-[18px] shrink-0 leading-none flex items-center justify-center select-none">
                search
              </span>
              <input
                v-model="searchQuery"
                @keyup.enter="handleSearch"
                type="text"
                placeholder="Tìm theo tên..."
                class="w-full bg-transparent !border-0 !border-none !ring-0 !shadow-none !outline-none text-xs px-2 py-0 placeholder:text-outline text-on-surface leading-normal"
              />
              <button
                v-if="searchQuery"
                @click="clearSearch"
                class="text-outline hover:text-on-surface flex items-center justify-center shrink-0 cursor-pointer ml-1"
                type="button"
                aria-label="Xóa từ khóa tìm kiếm"
              >
                <span class="material-symbols-outlined text-[16px]">close</span>
              </button>
            </div>

            <!-- Sort Dropdown -->
            <div class="flex items-center gap-1.5">
              <span class="text-xs text-outline hidden md:inline">Sắp xếp:</span>
              <select
                v-model="sortOrder"
                @change="handleSortChange"
                class="bg-surface-container-low border border-outline-variant/40 rounded-xl px-3 py-1.5 text-xs font-medium text-on-surface focus:outline-none focus:border-primary cursor-pointer"
              >
                <option value="latest">Mới nhất</option>
                <option value="price_asc">Giá: Thấp đến cao</option>
                <option value="price_desc">Giá: Cao đến thấp</option>
                <option value="name_asc">Tên: A - Z</option>
              </select>
            </div>

            <!-- Nút mở Drawer bộ lọc trên Mobile (< lg) -->
            <button
              type="button"
              @click="showMobileFilter = true"
              class="lg:hidden inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-outline-variant/60 bg-surface-container-low text-xs font-bold text-on-surface hover:border-primary transition-colors shrink-0"
            >
              <span class="material-symbols-outlined text-[17px] text-primary">tune</span>
              <span>Bộ lọc</span>
              <span
                v-if="hasActiveFilterTags"
                class="w-2 h-2 rounded-full bg-primary"
              ></span>
            </button>
          </div>
        </div>

        <!-- Active Filter Tags -->
        <div v-if="hasActiveFilterTags" class="flex flex-wrap items-center gap-2 mb-6">
          <span class="text-xs text-outline font-medium">Đang lọc:</span>
          <span
            v-if="selectedCategoryName"
            class="inline-flex items-center gap-1 px-2.5 py-1 bg-primary/10 text-primary text-xs font-bold rounded-lg"
          >
            {{ selectedCategoryName }}
            <button @click="selectedCategory = null; handleApplyFilter()" class="hover:text-error">×</button>
          </span>
          <span
            v-for="bId in selectedBrandIds"
            :key="bId"
            class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-container-high text-on-surface text-xs font-semibold rounded-lg"
          >
            {{ getBrandName(bId) }}
            <button @click="removeBrandTag(bId)" class="hover:text-error">×</button>
          </span>
          <span
            v-if="selectedRam"
            class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-container-high text-on-surface text-xs font-semibold rounded-lg"
          >
            RAM: {{ selectedRam }}
            <button @click="selectedRam = null; handleApplyFilter()" class="hover:text-error">×</button>
          </span>
          <span
            v-if="selectedStorage"
            class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-container-high text-on-surface text-xs font-semibold rounded-lg"
          >
            SSD: {{ selectedStorage }}
            <button @click="selectedStorage = null; handleApplyFilter()" class="hover:text-error">×</button>
          </span>
          <span
            v-if="selectedConnection"
            class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-container-high text-on-surface text-xs font-semibold rounded-lg"
          >
            Kết nối: {{ selectedConnection }}
            <button @click="selectedConnection = null; handleApplyFilter()" class="hover:text-error">×</button>
          </span>
          <span
            v-if="selectedScreenFeature"
            class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-container-high text-on-surface text-xs font-semibold rounded-lg"
          >
            Màn hình: {{ selectedScreenFeature }}
            <button @click="selectedScreenFeature = null; handleApplyFilter()" class="hover:text-error">×</button>
          </span>
          <span
            v-if="inStock"
            class="inline-flex items-center gap-1 px-2.5 py-1 bg-secondary/10 text-secondary text-xs font-semibold rounded-lg"
          >
            Còn hàng
            <button @click="inStock = false; handleApplyFilter()" class="hover:text-error">×</button>
          </span>
          <button
            @click="handleResetFilter"
            class="text-xs text-primary hover:underline font-bold ml-2"
          >
            Xóa tất cả
          </button>
        </div>

        <!-- Error Alert -->
        <div
          v-if="errorMessage"
          class="p-4 mb-6 rounded-xl bg-error-container text-on-error-container text-xs flex items-center justify-between"
        >
          <span>{{ errorMessage }}</span>
          <button @click="fetchProducts" class="underline font-bold ml-4">Thử lại</button>
        </div>

        <!-- Loading Skeletons -->
        <div
          v-if="isLoading"
          class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5"
        >
          <ProductCardSkeleton v-for="n in 8" :key="n" />
        </div>

        <!-- Empty State -->
        <div
          v-else-if="products.length === 0"
          class="py-16 text-center bg-surface-container-lowest rounded-2xl border border-outline-variant/30 flex flex-col items-center justify-center gap-2"
        >
          <span class="material-symbols-outlined text-5xl text-outline mb-1">inventory_2</span>
          <h3 class="text-base font-bold text-on-surface">Không tìm thấy sản phẩm phù hợp</h3>
          <p class="text-xs text-on-surface-variant max-w-sm">
            Thử thay đổi mức giá, thương hiệu hoặc từ khóa tìm kiếm.
          </p>
          <button
            @click="handleResetFilter"
            class="mt-3 px-4 py-2 bg-primary text-on-primary rounded-xl text-xs font-bold hover:bg-primary-container transition-colors"
          >
            Xóa tất cả bộ lọc
          </button>
        </div>

        <!-- Product Grid -->
        <div
          v-else
          class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5"
        >
          <ProductCard
            v-for="product in products"
            :key="product.id"
            :product="product"
          />
        </div>

        <!-- Pagination -->
        <BasePagination
          v-if="!isLoading && pagination.last_page > 1"
          :current-page="pagination.current_page"
          :total-pages="pagination.last_page"
          @change-page="handlePageChange"
        />
      </section>
    </div>

    <!-- Mobile Filter Drawer (Slide-in Bottom/Right Sheet on Mobile < lg) -->
    <Teleport to="body">
      <div
        v-if="showMobileFilter"
        class="fixed inset-0 z-50 flex justify-end lg:hidden"
      >
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity"
          @click="showMobileFilter = false"
        ></div>

        <!-- Drawer Content -->
        <div class="relative w-full max-w-sm bg-surface-container-lowest h-full shadow-2xl flex flex-col z-10 animate-in slide-in-from-right duration-200">
          <div class="flex items-center justify-between p-4 border-b border-outline-variant/30">
            <h3 class="font-black text-sm text-on-surface flex items-center gap-2">
              <span class="material-symbols-outlined text-primary text-xl">tune</span>
              Bộ lọc sản phẩm
            </h3>
            <button
              type="button"
              @click="showMobileFilter = false"
              class="p-1.5 text-outline hover:text-on-surface rounded-lg"
            >
              <span class="material-symbols-outlined text-xl">close</span>
            </button>
          </div>

          <div class="flex-1 overflow-y-auto p-4">
            <ProductFilterSidebar
              :categories="categories"
              :brands="brands"
              :selected-category="selectedCategory"
              :selected-brand-ids="selectedBrandIds"
              :selected-min-price="selectedMinPrice"
              :selected-max-price="selectedMaxPrice"
              :selected-ram="selectedRam"
              :selected-storage="selectedStorage"
              :selected-connection="selectedConnection"
              :selected-screen-feature="selectedScreenFeature"
              :in-stock="inStock"
              @update:selected-category="selectedCategory = $event"
              @update:selected-brand-ids="selectedBrandIds = $event"
              @update:selected-min-price="selectedMinPrice = $event"
              @update:selected-max-price="selectedMaxPrice = $event"
              @update:selected-ram="selectedRam = $event"
              @update:selected-storage="selectedStorage = $event"
              @update:selected-connection="selectedConnection = $event"
              @update:selected-screen-feature="selectedScreenFeature = $event"
              @update:in-stock="inStock = $event"
              @apply="handleApplyFilter(); showMobileFilter = false;"
              @reset="handleResetFilter"
            />
          </div>

          <div class="p-4 border-t border-outline-variant/30 bg-surface-container-low flex gap-2">
            <button
              type="button"
              @click="handleResetFilter"
              class="flex-1 py-2.5 rounded-xl border border-outline-variant text-xs font-bold text-on-surface hover:bg-surface-container"
            >
              Đặt lại
            </button>
            <button
              type="button"
              @click="handleApplyFilter(); showMobileFilter = false;"
              class="flex-1 py-2.5 rounded-xl bg-primary text-on-primary text-xs font-black uppercase tracking-wider shadow-sm hover:bg-primary-hover"
            >
              Xem kết quả
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { productService } from '../services/productService'
import type { Product, Category, Brand, PaginationMeta } from '../types'
import ProductCard from '../components/ProductCard.vue'
import ProductCardSkeleton from '@/core/components/ui/ProductCardSkeleton.vue'
import ProductFilterSidebar from '../components/ProductFilterSidebar.vue'
import BasePagination from '@/core/components/BasePagination.vue'

const route = useRoute()
const router = useRouter()

const showMobileFilter = ref(false)
const products = ref<Product[]>([])
const categories = ref<Category[]>([])
const brands = ref<Brand[]>([])

const selectedCategory = ref<number | null>(null)
const selectedBrandIds = ref<number[]>([])

// Phân giải Category an toàn từ Slug hoặc ID
function syncCategoryFromRoute() {
  const catSlug = (route.query.category || route.query.category_slug) as string
  if (catSlug && categories.value.length > 0) {
    const match = categories.value.find((c) => c.slug.toLowerCase() === catSlug.toLowerCase())
    if (match) {
      selectedCategory.value = match.id
      return
    }
  }
  if (route.query.category_id) {
    selectedCategory.value = Number(route.query.category_id)
  }
}

// Phân giải Brands an toàn từ Slugs hoặc IDs
function syncBrandsFromRoute() {
  const rawBrandSlug = (route.query.brands || route.query.brand || route.query.brand_slugs || route.query.brand_slug) as string
  if (rawBrandSlug && brands.value.length > 0) {
    const slugList = rawBrandSlug.split(',').map((s) => s.trim().toLowerCase())
    const matched = brands.value.filter((b) => slugList.includes(b.slug.toLowerCase())).map((b) => b.id)
    if (matched.length > 0) {
      selectedBrandIds.value = matched
      return
    }
  }
  if (route.query.brand_ids) {
    selectedBrandIds.value = String(route.query.brand_ids)
      .split(',')
      .map(Number)
      .filter((n) => !isNaN(n))
  } else if (route.query.brand_id) {
    const b = Number(route.query.brand_id)
    selectedBrandIds.value = isNaN(b) ? [] : [b]
  }
}
const selectedMinPrice = ref<number | null>(
  route.query.min_price ? Number(route.query.min_price) : null
)
const selectedMaxPrice = ref<number | null>(
  route.query.max_price ? Number(route.query.max_price) : null
)
const selectedRam = ref<string | null>((route.query.ram as string) || null)
const selectedStorage = ref<string | null>((route.query.storage as string) || null)
const selectedConnection = ref<string | null>((route.query.connection as string) || null)
const selectedScreenFeature = ref<string | null>((route.query.screen_feature as string) || null)
const inStock = ref<boolean>(route.query.in_stock === 'true')

const searchQuery = ref<string>((route.query.search as string) || '')
const sortOrder = ref<string>((route.query.sort as string) || 'latest')
const currentPage = ref<number>(
  route.query.page ? Number(route.query.page) : 1
)

const pagination = ref<PaginationMeta>({
  total: 0,
  per_page: 12,
  current_page: 1,
  last_page: 1,
})

const isLoading = ref<boolean>(false)
const errorMessage = ref<string>('')

const selectedCategoryName = computed(() => {
  if (!selectedCategory.value) return null
  return categories.value.find((c) => c.id === selectedCategory.value)?.name || null
})

const getBrandName = (id: number) => {
  return brands.value.find((b) => b.id === id)?.name || `Hãng #${id}`
}

const hasActiveFilterTags = computed(() => {
  return (
    selectedCategory.value !== null ||
    selectedBrandIds.value.length > 0 ||
    selectedRam.value !== null ||
    selectedStorage.value !== null ||
    selectedConnection.value !== null ||
    selectedScreenFeature.value !== null ||
    inStock.value
  )
})

const removeBrandTag = (id: number) => {
  selectedBrandIds.value = selectedBrandIds.value.filter((b) => b !== id)
  handleApplyFilter()
}

const fetchInitialData = async () => {
  try {
    const [cats, brs] = await Promise.all([
      productService.getCategories(),
      productService.getBrands(),
    ])
    categories.value = cats
    brands.value = brs
  } catch (error) {
    console.error('Lỗi khi tải danh mục hoặc thương hiệu:', error)
  }
}

const fetchProducts = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const res = await productService.getProducts({
      category_id: selectedCategory.value,
      brand_ids: selectedBrandIds.value.length > 0 ? selectedBrandIds.value : undefined,
      min_price: selectedMinPrice.value,
      max_price: selectedMaxPrice.value,
      ram: selectedRam.value || undefined,
      storage: selectedStorage.value || undefined,
      connection: selectedConnection.value || undefined,
      screen_feature: selectedScreenFeature.value || undefined,
      in_stock: inStock.value ? true : undefined,
      sort: sortOrder.value,
      search: searchQuery.value.trim() || undefined,
      page: currentPage.value,
      per_page: pagination.value.per_page,
    })

    products.value = res.data
    pagination.value = res.pagination
  } catch (error: unknown) {
    console.error('Lỗi khi tải danh sách sản phẩm:', error)
    errorMessage.value = 'Không thể tải danh sách sản phẩm. Vui lòng thử lại sau.'
  } finally {
    isLoading.value = false
  }
}

const syncUrlParams = () => {
  const query: Record<string, string | number> = {}

  // Bảo mật: Dùng Category Slug thay vì lộ ID
  if (selectedCategory.value) {
    const cat = categories.value.find((c) => c.id === selectedCategory.value)
    if (cat?.slug) {
      query.category = cat.slug
    } else {
      query.category_id = selectedCategory.value
    }
  }

  // Bảo mật: Dùng Brand Slugs thay vì lộ ID
  if (selectedBrandIds.value.length > 0) {
    const slugs = selectedBrandIds.value
      .map((id) => brands.value.find((b) => b.id === id)?.slug)
      .filter(Boolean)
    if (slugs.length > 0) {
      query.brands = slugs.join(',')
    } else {
      query.brand_ids = selectedBrandIds.value.join(',')
    }
  }

  if (selectedMinPrice.value) query.min_price = selectedMinPrice.value
  if (selectedMaxPrice.value) query.max_price = selectedMaxPrice.value
  if (selectedRam.value) query.ram = selectedRam.value
  if (selectedStorage.value) query.storage = selectedStorage.value
  if (selectedConnection.value) query.connection = selectedConnection.value
  if (selectedScreenFeature.value) query.screen_feature = selectedScreenFeature.value
  if (inStock.value) query.in_stock = 'true'
  if (sortOrder.value !== 'latest') query.sort = sortOrder.value
  if (searchQuery.value.trim()) query.search = searchQuery.value.trim()
  if (currentPage.value > 1) query.page = currentPage.value

  router.replace({ query })
}

const handleApplyFilter = () => {
  currentPage.value = 1
  syncUrlParams()
  fetchProducts()
}

const handleResetFilter = () => {
  selectedCategory.value = null
  selectedBrandIds.value = []
  selectedMinPrice.value = null
  selectedMaxPrice.value = null
  selectedRam.value = null
  selectedStorage.value = null
  selectedConnection.value = null
  selectedScreenFeature.value = null
  inStock.value = false
  searchQuery.value = ''
  sortOrder.value = 'latest'
  currentPage.value = 1
  syncUrlParams()
  fetchProducts()
}

const handleSortChange = () => {
  currentPage.value = 1
  syncUrlParams()
  fetchProducts()
}

const handleSearch = () => {
  currentPage.value = 1
  syncUrlParams()
  fetchProducts()
}

const clearSearch = () => {
  searchQuery.value = ''
  handleSearch()
}

const handlePageChange = (page: number) => {
  currentPage.value = page
  syncUrlParams()
  fetchProducts()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Cập nhật Document Title theo bộ lọc và tìm kiếm
watch(
  [selectedCategoryName, searchQuery],
  ([catName, search]) => {
    if (search) {
      document.title = `Tìm kiếm "${search}" | TechShop`
    } else if (catName) {
      document.title = `${catName} Chính Hãng | TechShop`
    } else {
      document.title = `Sản Phẩm Công Nghệ | TechShop`
    }
  },
  { immediate: true }
)

// Lắng nghe thay đổi URL từ bên ngoài (như MegaMenu)
watch(
  () => route.query,
  () => {
    syncCategoryFromRoute()
    syncBrandsFromRoute()
    searchQuery.value = (route.query.search as string) || ''
    fetchProducts()
  }
)

onMounted(async () => {
  await fetchInitialData()
  syncCategoryFromRoute()
  syncBrandsFromRoute()
  await fetchProducts()
})
</script>
