<template>
  <div class="space-y-6">
    <!-- Header Section -->
    <AdminPageHeader
      title="Quản lý sản phẩm"
      description="Theo dõi danh mục hàng hóa, giá bán, cấu hình biến thể và kho hàng toàn hệ thống."
      :badge-text="`${pagination.total} sản phẩm`"
    >
      <template #actions>
        <RouterLink
          to="/admin/products/create"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-on-primary rounded-xl text-xs font-bold hover:bg-primary-hover transition-all shadow-md shadow-primary/20 active:scale-95"
        >
          <span class="material-symbols-outlined text-[18px]">add_circle</span>
          Thêm sản phẩm mới
        </RouterLink>
      </template>
    </AdminPageHeader>

    <!-- Filter & Search Bar -->
    <div class="bg-surface-container-lowest p-4 rounded-2xl border border-outline-variant/30 flex flex-wrap items-center justify-between gap-4">
      <div class="flex flex-wrap items-center gap-3 flex-1 min-w-[280px]">
        <!-- Search Input -->
        <div class="relative flex-1 min-w-[200px]">
          <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[18px]">
            search
          </span>
          <input
            v-model="filters.search"
            @keyup.enter="handleSearch"
            type="text"
            placeholder="Tìm theo tên sản phẩm..."
            class="w-full pl-10 pr-4 py-2 bg-surface rounded-xl border border-outline-variant/40 text-xs focus:border-primary focus:ring-1 focus:ring-primary/20"
          />
        </div>

        <!-- Category Filter -->
        <select
          v-model="filters.category_id"
          @change="handleSearch"
          class="px-3 py-2 bg-surface rounded-xl border border-outline-variant/40 text-xs text-on-surface font-medium"
        >
          <option value="all">Tất cả danh mục</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">
            {{ c.name }}
          </option>
        </select>

        <!-- Brand Filter -->
        <select
          v-model="filters.brand_id"
          @change="handleSearch"
          class="px-3 py-2 bg-surface rounded-xl border border-outline-variant/40 text-xs text-on-surface font-medium"
        >
          <option value="all">Tất cả thương hiệu</option>
          <option v-for="b in brands" :key="b.id" :value="b.id">
            {{ b.name }}
          </option>
        </select>
      </div>

      <button
        @click="resetFilters"
        class="text-xs text-on-surface-variant hover:text-primary font-bold transition-colors flex items-center gap-1"
      >
        <span class="material-symbols-outlined text-[16px]">restart_alt</span>
        Đặt lại bộ lọc
      </button>
    </div>

    <!-- Products Table -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 overflow-hidden shadow-xs">
      <div v-if="isLoading" class="p-12 text-center text-xs text-on-surface-variant flex flex-col items-center gap-2">
        <span class="material-symbols-outlined animate-spin text-primary text-3xl">sync</span>
        Đang tải danh sách sản phẩm...
      </div>

      <div v-else-if="products.length === 0" class="p-12 text-center text-xs text-on-surface-variant">
        <span class="material-symbols-outlined text-outline text-4xl mb-2">inventory_2</span>
        <p>Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead class="bg-surface-container-low text-on-surface-variant font-bold uppercase border-b border-outline-variant/30">
            <tr>
              <th class="py-3 px-4 w-16">ID</th>
              <th class="py-3 px-4">Sản phẩm</th>
              <th class="py-3 px-4">Thương hiệu</th>
              <th class="py-3 px-4">Danh mục</th>
              <th class="py-3 px-4">Giá khởi điểm</th>
              <th class="py-3 px-4 text-center">Tồn kho</th>
              <th class="py-3 px-4 text-center">Trạng thái</th>
              <th class="py-3 px-4 text-right">Thao tác</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/20 text-on-surface font-medium">
            <tr v-for="product in products" :key="product.id" class="hover:bg-surface-container-low/40 transition-colors">
              <td class="py-3 px-4 font-bold text-outline">#{{ product.id }}</td>
              <td class="py-3 px-4">
                <div class="flex items-center gap-3">
                  <div class="w-11 h-11 rounded-lg border border-outline-variant/40 bg-white p-1 shrink-0 overflow-hidden">
                    <img
                      :src="getProductThumb(product) || fallbackImage"
                      class="w-full h-full object-contain"
                      alt=""
                    />
                  </div>
                  <div class="min-w-0 max-w-[280px]">
                    <div class="font-bold text-on-surface truncate">{{ product.name }}</div>
                    <div class="text-[11px] text-on-surface-variant font-mono truncate">
                      {{ product.variants?.[0]?.sku || 'CHƯA_CÓ_SKU' }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="py-3 px-4 whitespace-nowrap">
                <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary font-bold text-[11px]">
                  {{ product.brand?.name || '---' }}
                </span>
              </td>
              <td class="py-3 px-4 whitespace-nowrap text-on-surface-variant font-medium">
                {{ product.category?.name || '---' }}
              </td>
              <td class="py-3 px-4 whitespace-nowrap">
                <div class="font-bold text-primary">
                  {{ formatPrice(Number(product.variants?.[0]?.price || 0)) }}
                </div>
                <div class="text-[11px] text-on-surface-variant">
                  {{ product.variants?.length || 1 }} phiên bản
                </div>
              </td>
              <td class="py-3 px-4 text-center font-semibold text-on-surface whitespace-nowrap">
                {{ product.variants?.[0]?.stock ?? 50 }}
              </td>
              <td class="py-3 px-4 text-center whitespace-nowrap">
                <span
                  class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold"
                  :class="product.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-surface-container text-on-surface-variant'"
                >
                  {{ product.is_active ? 'Đang bán' : 'Tạm ẩn' }}
                </span>
              </td>
              <td class="py-3 px-4 text-right whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                  <RouterLink
                    :to="'/admin/products/' + product.id + '/edit'"
                    class="p-1.5 rounded-lg text-primary hover:bg-primary/10 transition-colors"
                    title="Chỉnh sửa sản phẩm"
                  >
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                  </RouterLink>
                  <button
                    v-if="product.id"
                    @click="deleteProduct(product.id)"
                    class="p-1.5 rounded-lg text-error hover:bg-error/10 transition-colors"
                    title="Xóa sản phẩm"
                  >
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <BasePagination
        v-if="pagination.last_page > 1"
        :current-page="pagination.current_page"
        :last-page="pagination.last_page"
        :total="pagination.total"
        :per-page="pagination.per_page"
        @change-page="fetchProducts"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { adminService } from '../services/adminService'
import { useToast } from '@/core/composables/useToast'
import { useConfirmDialog } from '@/core/composables/useConfirmDialog'
import AdminPageHeader from '@/core/components/ui/AdminPageHeader.vue'
import BasePagination from '@/core/components/ui/BasePagination.vue'

const toast = useToast()
const { confirm: confirmDialog } = useConfirmDialog()

const fallbackImage =
  'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect fill="%23f0eded" width="100" height="100"/><text fill="%237b7487" font-family="sans-serif" font-size="12" dy="4" font-weight="bold" x="50%" y="50%" text-anchor="middle">TechShop</text></svg>'

interface CategoryOption {
  id: number
  name: string
}

interface BrandOption {
  id: number
  name: string
}

export interface ProductItemData {
  id?: number
  name: string
  slug?: string
  brand_id?: number
  brand?: { id: number; name: string }
  category_id?: number
  category?: { id: number; name: string }
  description?: string
  is_active?: boolean
  thumbnail?: string
  images?: { id?: number; image_url: string }[] | string[]
  variants?: {
    id?: number
    sku: string
    price: number | string
    stock: number | string
    image_url?: string
    options?: string[]
  }[]
  starting_price?: number
}

const products = ref<ProductItemData[]>([])
const categories = ref<CategoryOption[]>([])
const brands = ref<BrandOption[]>([])
const isLoading = ref<boolean>(true)

const filters = ref({
  search: '',
  category_id: 'all',
  brand_id: 'all',
})

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 15,
})

function formatPrice(val: number) {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}

function getProductThumb(prod: ProductItemData): string {
  if (prod.images && prod.images.length > 0 && prod.images[0]) {
    const first = prod.images[0]
    return typeof first === 'string' ? first : first.image_url
  }
  return prod.thumbnail || ''
}

async function fetchMetadata() {
  try {
    const [catRes, brandRes] = await Promise.all([
      adminService.getCategories(),
      adminService.getBrands(),
    ])
    categories.value = Array.isArray(catRes) ? catRes : (catRes?.data || [])
    brands.value = Array.isArray(brandRes) ? brandRes : (brandRes?.data || [])
  } catch (err: unknown) {
    console.error('Lỗi khi tải metadata:', err)
  }
}

async function fetchProducts(page = 1) {
  isLoading.value = true
  try {
    const params: Record<string, string | number> = { page, per_page: 10 }
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.category_id !== 'all') params.category_id = filters.value.category_id
    if (filters.value.brand_id !== 'all') params.brand_id = filters.value.brand_id

    const res = await adminService.getProducts(params)
    if (res) {
      const items = Array.isArray(res) ? res : (Array.isArray(res.data) ? res.data : [])
      products.value = items
      pagination.value = {
        current_page: res.current_page || 1,
        last_page: res.last_page || 1,
        total: res.total ?? items.length,
        per_page: res.per_page || 10,
      }
    }
  } catch (err: unknown) {
    console.error('Lỗi khi tải danh sách sản phẩm:', err)
  } finally {
    isLoading.value = false
  }
}

function handleSearch() {
  fetchProducts(1)
}

function resetFilters() {
  filters.value = { search: '', category_id: 'all', brand_id: 'all' }
  fetchProducts(1)
}

async function deleteProduct(id: number) {
  const isConfirmed = await confirmDialog({
    title: 'Xóa sản phẩm này?',
    message: 'Bạn có chắc chắn muốn xóa sản phẩm này không? Thao tác này không thể hoàn tác.',
    confirmText: 'Xác nhận xóa',
    cancelText: 'Hủy bỏ',
    type: 'danger',
  })
  if (!isConfirmed) return

  try {
    await adminService.deleteProduct(id)
    toast.success('Đã xóa sản phẩm thành công!')
    fetchProducts(pagination.value.current_page)
  } catch (err: unknown) {
    const errorMsg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    toast.error(errorMsg || 'Không thể xóa sản phẩm này!')
  }
}

onMounted(async () => {
  await fetchMetadata()
  await fetchProducts(1)
})
</script>
