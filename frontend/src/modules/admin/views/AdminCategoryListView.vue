<template>
  <div class="space-y-6 max-w-full">
    <!-- Header Section -->
    <header class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-2">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-on-surface tracking-tight">Quản lý danh mục</h1>
        <p class="text-sm text-on-surface-variant mt-1">Phân loại ngành hàng, cấu trúc danh mục và kiểm soát sản phẩm.</p>
      </div>
      <div>
        <button
          @click="openCreateModal"
          class="bg-primary hover:bg-primary-container text-on-primary px-5 py-2.5 rounded-xl flex items-center gap-2 transition-all shadow-md active:scale-95 text-sm font-bold"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          Thêm danh mục mới
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
          placeholder="Tìm kiếm danh mục..."
          type="text"
        />
      </div>
      <div class="text-sm font-medium text-on-surface-variant hidden sm:block">
        Tổng cộng: <strong class="text-on-surface">{{ filteredCategories.length }}</strong> danh mục
      </div>
    </section>

    <!-- Table Section -->
    <section class="bg-surface rounded-2xl shadow-sm border border-outline-variant/30 overflow-hidden">
      <div v-if="loading" class="p-16 text-center text-on-surface-variant">
        <div class="inline-block animate-spin w-8 h-8 border-4 border-primary border-t-transparent rounded-full mb-3"></div>
        <div class="text-sm font-medium">Đang tải danh mục...</div>
      </div>

      <div v-else-if="!filteredCategories.length" class="p-16 text-center text-on-surface-variant">
        <span class="material-symbols-outlined text-5xl text-outline mb-2">category</span>
        <div class="text-lg font-bold text-on-surface">Chưa có danh mục nào</div>
        <p class="text-sm text-on-surface-variant mt-1">Bấm "Thêm danh mục mới" để tạo ngành hàng mới</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant/50 text-xs font-bold uppercase tracking-wider text-on-surface-variant">
              <th class="py-3.5 px-4 w-16 text-center">ID</th>
              <th class="py-3.5 px-4">Tên danh mục</th>
              <th class="py-3.5 px-4">Đường dẫn (Slug)</th>
              <th class="py-3.5 px-4">Mô tả</th>
              <th class="py-3.5 px-4 w-36 text-center">Số sản phẩm</th>
              <th class="py-3.5 px-4 w-32 text-right pr-6">Hành động</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30">
            <tr
              v-for="cat in filteredCategories"
              :key="cat.id"
              class="hover:bg-surface-container-low transition-colors"
            >
              <td class="py-3.5 px-4 text-center font-mono text-on-surface-variant font-bold">
                #{{ cat.id }}
              </td>
              <td class="py-3.5 px-4 font-bold text-on-surface">
                {{ cat.name }}
              </td>
              <td class="py-3.5 px-4 text-primary font-mono text-xs">
                /{{ cat.slug }}
              </td>
              <td class="py-3.5 px-4 text-on-surface-variant text-xs max-w-xs truncate">
                {{ cat.description || 'Chưa có mô tả' }}
              </td>
              <td class="py-3.5 px-4 text-center">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-fixed text-on-primary-fixed">
                  {{ cat.products_count ?? 0 }} sản phẩm
                </span>
              </td>
              <td class="py-3.5 px-4 text-right pr-6 whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    @click="openEditModal(cat)"
                    class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-primary-fixed transition"
                    title="Chỉnh sửa"
                  >
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                  </button>
                  <button
                    @click="deleteCategory(cat)"
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
            {{ isEditing ? 'Chỉnh Sửa Danh Mục' : 'Thêm Danh Mục Mới' }}
          </h2>
          <button
            @click="showModal = false"
            class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container transition"
          >
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>

        <form @submit.prevent="submitCategory" class="p-5 space-y-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Tên danh mục *</label>
            <input
              v-model="form.name"
              required
              type="text"
              placeholder="VD: Bàn phím cơ, Chuột Gaming, Laptop..."
              class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-medium"
            />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Mô tả ngắn</label>
            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Mô tả ngành hàng, phân khúc sản phẩm..."
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
              :disabled="submitting"
              class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-container text-on-primary text-sm font-bold transition shadow-md disabled:opacity-50"
            >
              {{ submitting ? 'Đang lưu...' : (isEditing ? 'Lưu Thay Đổi' : 'Thêm Danh Mục') }}
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

interface CategoryAdminItem {
  id: number
  name: string
  slug: string
  description?: string | null
  products_count?: number
}

const loading = ref(true)
const categories = ref<CategoryAdminItem[]>([])
const searchQuery = ref('')

const showModal = ref(false)
const isEditing = ref(false)
const currentEditingId = ref<number | null>(null)
const submitting = ref(false)

const form = ref({
  name: '',
  description: '',
})

const filteredCategories = computed(() => {
  if (!searchQuery.value.trim()) return categories.value
  const q = searchQuery.value.toLowerCase().trim()
  return categories.value.filter((c) => c.name.toLowerCase().includes(q) || c.slug.toLowerCase().includes(q))
})

async function fetchCategories() {
  try {
    loading.value = true
    const res = await adminService.getCategoriesAdmin()
    categories.value = (res || []) as CategoryAdminItem[]
  } catch (err: unknown) {
    console.error('Lỗi khi tải danh mục:', err)
  } finally {
    loading.value = false
  }
}

function openCreateModal() {
  isEditing.value = false
  currentEditingId.value = null
  form.value = { name: '', description: '' }
  showModal.value = true
}

function openEditModal(cat: CategoryAdminItem) {
  isEditing.value = true
  currentEditingId.value = cat.id
  form.value = {
    name: cat.name,
    description: cat.description || '',
  }
  showModal.value = true
}

async function submitCategory() {
  try {
    submitting.value = true
    if (isEditing.value && currentEditingId.value) {
      await adminService.updateCategory(currentEditingId.value, form.value)
    } else {
      await adminService.createCategory(form.value)
    }
    showModal.value = false
    await fetchCategories()
  } catch (err: unknown) {
    const errorMsg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    alert(errorMsg || 'Có lỗi xảy ra khi lưu danh mục!')
  } finally {
    submitting.value = false
  }
}

async function deleteCategory(cat: CategoryAdminItem) {
  if (!confirm(`Bạn có chắc muốn xóa danh mục "${cat.name}"?`)) return
  try {
    const res = await adminService.deleteCategory(cat.id)
    if (!res.status) {
      alert(res.message || 'Không thể xóa danh mục!')
      return
    }
    await fetchCategories()
  } catch (err: unknown) {
    const errorMsg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    alert(errorMsg || 'Không thể xóa danh mục!')
  }
}

onMounted(() => {
  fetchCategories()
})
</script>

