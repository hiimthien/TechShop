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
        <!-- 1. Toolbar: Search, Counter & Sort -->
        <ProductSortBar
          :count="products.length"
          :total="pagination.total"
          v-model:search-query="searchQuery"
          v-model:sort-order="sortOrder"
          :has-active-filters="hasActiveFilterTags"
          @search="handleSearch"
          @clear-search="clearSearch"
          @update:sort-order="handleSortChange"
          @open-mobile-filter="showMobileFilter = true"
        />

        <!-- 2. Active Filter Tags Bar -->
        <ProductActiveFilters
          :category-name="selectedCategoryName"
          :active-brands="activeBrandList"
          :ram="selectedRam"
          :storage="selectedStorage"
          :connection="selectedConnection"
          :screen-feature="selectedScreenFeature"
          :in-stock="inStock"
          @remove-category="selectedCategory = null; handleApplyFilter()"
          @remove-brand="removeBrandTag"
          @remove-ram="selectedRam = null; handleApplyFilter()"
          @remove-storage="selectedStorage = null; handleApplyFilter()"
          @remove-connection="selectedConnection = null; handleApplyFilter()"
          @remove-screen-feature="selectedScreenFeature = null; handleApplyFilter()"
          @remove-in-stock="inStock = false; handleApplyFilter()"
          @reset-all="handleResetFilter"
        />

        <!-- Error Alert -->
        <div
          v-if="errorMessage"
          class="p-4 mb-6 rounded-xl bg-error-container text-on-error-container text-xs flex items-center justify-between"
        >
          <span>{{ errorMessage }}</span>
          <button @click="fetchProducts" class="underline font-bold ml-4 cursor-pointer">Thử lại</button>
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
            class="mt-3 px-4 py-2 bg-primary text-on-primary rounded-xl text-xs font-bold hover:bg-primary-hover transition-colors cursor-pointer"
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
        <div class="mt-8">
          <BasePagination
            v-if="!isLoading && pagination.last_page > 1"
            :current-page="pagination.current_page"
            :last-page="pagination.last_page"
            :total="pagination.total"
            :per-page="pagination.per_page"
            @change-page="handlePageChange"
          />
        </div>
      </section>
    </div>

    <!-- Mobile Filter Drawer -->
    <ProductMobileFilterDrawer
      :show="showMobileFilter"
      @close="showMobileFilter = false"
      @apply="handleApplyFilter"
      @reset="handleResetFilter"
    >
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
    </ProductMobileFilterDrawer>
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
import BasePagination from '@/core/components/ui/BasePagination.vue'
import ProductSortBar from '../components/list/ProductSortBar.vue'
import ProductActiveFilters from '../components/list/ProductActiveFilters.vue'
import ProductMobileFilterDrawer from '../components/list/ProductMobileFilterDrawer.vue'

const route = useRoute()
const router = useRouter()

const showMobileFilter = ref(false)
const products = ref<Product[]>([])
const categories = ref<Category[]>([])
const brands = ref<Brand[]>([])

const selectedCategory = ref<number | null>(null)
const selectedBrandIds = ref<number[]>([])

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

const activeBrandList = computed(() => {
  return selectedBrandIds.value.map((id) => ({
    id,
    name: brands.value.find((b) => b.id === id)?.name || `Hãng #${id}`,
  }))
})

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
  } catch (error) {
    console.error('Lỗi khi tải danh sách sản phẩm:', error)
    errorMessage.value = 'Không thể kết nối đến máy chủ. Vui lòng kiểm tra lại đường truyền.'
  } finally {
    isLoading.value = false
  }
}

const updateRouterQuery = () => {
  const query: Record<string, string | number> = {}

  if (selectedCategory.value) query.category_id = selectedCategory.value
  if (selectedBrandIds.value.length > 0) query.brand_ids = selectedBrandIds.value.join(',')
  if (selectedMinPrice.value !== null && selectedMinPrice.value !== undefined) query.min_price = selectedMinPrice.value
  if (selectedMaxPrice.value !== null && selectedMaxPrice.value !== undefined) query.max_price = selectedMaxPrice.value
  if (selectedRam.value) query.ram = selectedRam.value
  if (selectedStorage.value) query.storage = selectedStorage.value
  if (selectedConnection.value) query.connection = selectedConnection.value
  if (selectedScreenFeature.value) query.screen_feature = selectedScreenFeature.value
  if (inStock.value) query.in_stock = 'true'
  if (searchQuery.value.trim()) query.search = searchQuery.value.trim()
  if (sortOrder.value && sortOrder.value !== 'latest') query.sort = sortOrder.value
  if (currentPage.value > 1) query.page = currentPage.value

  router.push({ query })
}

const handleApplyFilter = () => {
  currentPage.value = 1
  updateRouterQuery()
  fetchProducts()
  showMobileFilter.value = false
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

  router.push({ query: {} })
  fetchProducts()
  showMobileFilter.value = false
}

const handleSearch = () => {
  currentPage.value = 1
  updateRouterQuery()
  fetchProducts()
}

const clearSearch = () => {
  searchQuery.value = ''
  handleSearch()
}

const handleSortChange = (val?: string) => {
  if (val) sortOrder.value = val
  currentPage.value = 1
  updateRouterQuery()
  fetchProducts()
}

const handlePageChange = (page: number) => {
  currentPage.value = page
  updateRouterQuery()
  fetchProducts()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

watch(
  () => route.query,
  () => {
    syncCategoryFromRoute()
    syncBrandsFromRoute()
    selectedMinPrice.value = route.query.min_price ? Number(route.query.min_price) : null
    selectedMaxPrice.value = route.query.max_price ? Number(route.query.max_price) : null
    selectedRam.value = (route.query.ram as string) || null
    selectedStorage.value = (route.query.storage as string) || null
    selectedConnection.value = (route.query.connection as string) || null
    selectedScreenFeature.value = (route.query.screen_feature as string) || null
    inStock.value = route.query.in_stock === 'true'
    searchQuery.value = (route.query.search as string) || ''
    sortOrder.value = (route.query.sort as string) || 'latest'
    currentPage.value = route.query.page ? Number(route.query.page) : 1
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
