<template>
  <div class="space-y-6 max-w-full">
    <!-- Header Section -->
    <header class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-2">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-on-surface tracking-tight">Quản lý thương hiệu</h1>
        <p class="text-sm text-on-surface-variant mt-1">Danh sách đối tác, hãng sản xuất công nghệ và thương hiệu đối tác.</p>
      </div>
      <div>
        <button
          @click="openCreateModal"
          class="bg-primary hover:bg-primary-container text-on-primary px-5 py-2.5 rounded-xl flex items-center gap-2 transition-all shadow-md active:scale-95 text-sm font-bold"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          Thêm thương hiệu mới
        </button>
      </div>
    </header>

    <!-- Search Section -->
    <section class="bg-surface p-4 sm:p-5 rounded-2xl shadow-sm border border-outline-variant/30 flex items-center justify-between">
      <div class="relative w-full sm:w-80">
        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
        <input
          v-model="searchQuery"
          class="w-full pl-10 pr-3 py-2 bg-surface-container-low border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
          placeholder="Tìm kiếm thương hiệu..."
          type="text"
        />
      </div>
      <div class="text-sm font-medium text-on-surface-variant hidden sm:block">
        Tổng cộng: <strong class="text-on-surface">{{ filteredBrands.length }}</strong> thương hiệu
      </div>
    </section>

    <!-- Table Section -->
    <section class="bg-surface rounded-2xl shadow-sm border border-outline-variant/30 overflow-hidden">
      <div v-if="loading" class="p-16 text-center text-on-surface-variant">
        <div class="inline-block animate-spin w-8 h-8 border-4 border-primary border-t-transparent rounded-full mb-3"></div>
        <div class="text-sm font-medium">Đang tải thương hiệu...</div>
      </div>

      <div v-else-if="!filteredBrands.length" class="p-16 text-center text-on-surface-variant">
        <span class="material-symbols-outlined text-5xl text-outline mb-2">branding_watermark</span>
        <div class="text-lg font-bold text-on-surface">Chưa có thương hiệu nào</div>
        <p class="text-sm text-on-surface-variant mt-1">Bấm "Thêm thương hiệu mới" để đăng ký đối tác mới</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant/50 text-xs font-bold uppercase tracking-wider text-on-surface-variant">
              <th class="py-3.5 px-4 w-20 text-center">Logo</th>
              <th class="py-3.5 px-4">Tên thương hiệu</th>
              <th class="py-3.5 px-4">Slug</th>
              <th class="py-3.5 px-4">Mô tả</th>
              <th class="py-3.5 px-4 w-36 text-center">Số sản phẩm</th>
              <th class="py-3.5 px-4 w-32 text-right pr-6">Hành động</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30">
            <tr
              v-for="brand in filteredBrands"
              :key="brand.id"
              class="hover:bg-surface-container-low transition-colors"
            >
              <td class="py-3.5 px-4 text-center">
                <div class="w-12 h-12 rounded-xl bg-surface-container-lowest p-1.5 border border-outline-variant/40 flex items-center justify-center mx-auto">
                  <img
                    v-if="brand.logo_url"
                    :src="brand.logo_url"
                    class="max-w-full max-h-full object-contain"
                    alt=""
                  />
                  <span v-else class="font-bold text-primary text-xs">{{ brand.name.substring(0, 2).toUpperCase() }}</span>
                </div>
              </td>
              <td class="py-3.5 px-4 font-bold text-on-surface">
                {{ brand.name }}
              </td>
              <td class="py-3.5 px-4 text-primary font-mono text-xs">
                /{{ brand.slug }}
              </td>
              <td class="py-3.5 px-4 text-on-surface-variant text-xs max-w-xs truncate">
                {{ brand.description || 'Chưa có mô tả' }}
              </td>
              <td class="py-3.5 px-4 text-center">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-fixed text-on-primary-fixed">
                  {{ brand.products_count ?? 0 }} sản phẩm
                </span>
              </td>
              <td class="py-3.5 px-4 text-right pr-6 whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    @click="openEditModal(brand)"
                    class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-primary-fixed transition"
                    title="Chỉnh sửa"
                  >
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                  </button>
                  <button
                    @click="deleteBrand(brand)"
                    class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error-container/20 transition"
                    title="Xóa"
                  >
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Create / Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
    >
      <div class="bg-surface rounded-2xl w-full max-w-lg shadow-2xl border border-outline-variant overflow-hidden my-8">
        <div class="p-5 border-b border-outline-variant flex items-center justify-between bg-surface-container-lowest">
          <h2 class="text-lg font-bold text-on-surface">
            {{ isEditing ? 'Chỉnh Sửa Thương Hiệu' : 'Thêm Thương Hiệu Mới' }}
          </h2>
          <button
            @click="showModal = false"
            class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container transition"
          >
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>

        <form @submit.prevent="submitBrand" class="p-5 space-y-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Tên thương hiệu *</label>
            <input
              v-model="form.name"
              required
              type="text"
              placeholder="VD: ASUS, Apple, Logitech, Sony..."
              class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-medium"
            />
          </div>

          <!-- Logo Upload Section -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Logo Thương Hiệu</label>
            <div class="flex items-center gap-3">
              <div class="w-16 h-16 rounded-xl bg-surface-container-lowest border border-outline-variant/60 flex items-center justify-center overflow-hidden shrink-0">
                <img v-if="form.logo_url" :src="form.logo_url" class="max-w-full max-h-full object-contain p-1" alt="Logo preview" />
                <span v-else class="material-symbols-outlined text-outline">image</span>
              </div>
              <div class="flex-1 space-y-2">
                <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high border border-outline-variant text-xs font-bold text-on-surface cursor-pointer transition">
                  <span class="material-symbols-outlined text-[18px]">upload_file</span>
                  {{ uploadingLogo ? 'Đang tải lên...' : 'Chọn file ảnh từ máy' }}
                  <input type="file" accept="image/*" class="hidden" @change="handleLogoUpload" />
                </label>
                <input
                  v-model="form.logo_url"
                  type="text"
                  placeholder="Hoặc dán URL logo..."
                  class="w-full px-3 py-1.5 bg-surface-container-low border border-outline-variant rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-primary"
                />
              </div>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Mô tả thương hiệu</label>
            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Thông tin xuất xứ, bảo hành chính hãng..."
              class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
            ></textarea>
          </div>

          <div class="pt-4 border-t border-outline-variant flex justify-end gap-3">
            <button
              type="button"
              @click="showModal = false"
              class="px-5 py-2.5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface text-sm font-semibold transition"
            >
              Hủy
            </button>
            <button
              type="submit"
              :disabled="submitting || uploadingLogo"
              class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-container text-on-primary text-sm font-bold transition shadow-md disabled:opacity-50"
            >
              {{ submitting ? 'Đang lưu...' : (isEditing ? 'Lưu Thay Đổi' : 'Thêm Thương Hiệu') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { adminService } from '@/modules/admin/services/adminService'

interface BrandAdminItem {
  id: number
  name: string
  slug: string
  logo_url?: string | null
  description?: string | null
  products_count?: number
}

const loading = ref(true)
const brands = ref<BrandAdminItem[]>([])
const searchQuery = ref('')

const showModal = ref(false)
const isEditing = ref(false)
const currentEditingId = ref<number | null>(null)
const submitting = ref(false)
const uploadingLogo = ref(false)

const form = ref({
  name: '',
  logo_url: '',
  description: '',
})

const filteredBrands = computed(() => {
  if (!searchQuery.value.trim()) return brands.value
  const q = searchQuery.value.toLowerCase().trim()
  return brands.value.filter((b) => b.name.toLowerCase().includes(q) || b.slug.toLowerCase().includes(q))
})

async function fetchBrands() {
  try {
    loading.value = true
    const res = await adminService.getBrandsAdmin()
    brands.value = (res || []) as BrandAdminItem[]
  } catch (err: unknown) {
    console.error('Lỗi khi tải thương hiệu:', err)
  } finally {
    loading.value = false
  }
}

async function handleLogoUpload(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  try {
    uploadingLogo.value = true
    const res = await adminService.uploadImage(file)
    if (res.url) {
      form.value.logo_url = res.url
    }
  } catch (err: unknown) {
    const errorMsg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    alert(errorMsg || 'Không thể upload ảnh logo!')
  } finally {
    uploadingLogo.value = false
  }
}

function openCreateModal() {
  isEditing.value = false
  currentEditingId.value = null
  form.value = { name: '', logo_url: '', description: '' }
  showModal.value = true
}

function openEditModal(brand: BrandAdminItem) {
  isEditing.value = true
  currentEditingId.value = brand.id
  form.value = {
    name: brand.name,
    logo_url: brand.logo_url || '',
    description: brand.description || '',
  }
  showModal.value = true
}

async function submitBrand() {
  try {
    submitting.value = true
    if (isEditing.value && currentEditingId.value) {
      await adminService.updateBrand(currentEditingId.value, form.value)
    } else {
      await adminService.createBrand(form.value)
    }
    showModal.value = false
    await fetchBrands()
  } catch (err: unknown) {
    const errorMsg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    alert(errorMsg || 'Có lỗi xảy ra khi lưu thương hiệu!')
  } finally {
    submitting.value = false
  }
}

async function deleteBrand(brand: BrandAdminItem) {
  if (!confirm(`Bạn có chắc muốn xóa thương hiệu "${brand.name}"?`)) return
  try {
    const res = await adminService.deleteBrand(brand.id)
    if (!res.status) {
      alert(res.message || 'Không thể xóa thương hiệu!')
      return
    }
    await fetchBrands()
  } catch (err: unknown) {
    const errorMsg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    alert(errorMsg || 'Không thể xóa thương hiệu!')
  }
}

onMounted(() => {
  fetchBrands()
})
</script>

