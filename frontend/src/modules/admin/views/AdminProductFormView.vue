<template>
  <div class="space-y-6 max-w-7xl mx-auto pb-16">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-outline-variant/30">
      <div class="flex items-center gap-3">
        <button
          type="button"
          @click="goBack"
          class="p-2 rounded-xl border border-outline-variant/40 hover:bg-surface-container text-on-surface transition-colors flex items-center justify-center"
          title="Quay lại danh sách"
        >
          <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </button>
        <div>
          <h1 class="text-xl md:text-2xl font-black text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-2xl">
              {{ isEditing ? 'edit_note' : 'add_box' }}
            </span>
            <span>{{ isEditing ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm công nghệ mới' }}</span>
          </h1>
          <p class="text-xs text-on-surface-variant mt-0.5">
            {{ isEditing ? `Cập nhật thông tin, cấu hình biến thể và thông số cho ID #${productId}` : 'Thiết lập đầy đủ thông tin, biến thể và thông số kỹ thuật chuẩn e-commerce' }}
          </p>
        </div>
      </div>

      <div class="flex items-center gap-3 self-end sm:self-auto">
        <button
          type="button"
          @click="goBack"
          class="px-4 py-2.5 rounded-xl border border-outline-variant/50 text-xs font-bold text-on-surface-variant hover:bg-surface-container transition-all"
        >
          Hủy bỏ
        </button>
        <button
          type="button"
          @click="handleSave"
          :disabled="isSubmitting"
          class="px-6 py-2.5 bg-primary text-on-primary rounded-xl text-xs font-bold hover:bg-primary-container transition-all shadow-md shadow-primary/25 flex items-center gap-2 disabled:opacity-50"
        >
          <span v-if="isSubmitting" class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span>
          <span v-else class="material-symbols-outlined text-[18px]">save</span>
          <span>{{ isEditing ? 'Cập nhật sản phẩm' : 'Đăng bán sản phẩm' }}</span>
        </button>
      </div>
    </div>

    <!-- Alert message if any -->
    <div
      v-if="errorMessage"
      class="p-4 rounded-2xl bg-error-container text-on-error-container text-xs flex items-center justify-between shadow-xs"
    >
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-error text-[18px]">error</span>
        <span>{{ errorMessage }}</span>
      </div>
      <button @click="errorMessage = ''" class="text-xs font-bold hover:underline">Đóng</button>
    </div>

    <!-- Main Two-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      <!-- CỘT TRÁI (8 / 12): Thông tin cốt lõi, Ảnh, Biến thể, Thông số -->
      <div class="lg:col-span-8 space-y-6">
        <!-- 1. Thông tin cơ bản -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/30 space-y-4 shadow-xs">
          <h2 class="text-sm font-bold uppercase tracking-wider text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[18px]">info</span>
            Thông tin cơ bản
          </h2>

          <div>
            <label class="block text-xs font-bold text-on-surface mb-1">
              Tên sản phẩm *
            </label>
            <input
              v-model="form.name"
              required
              type="text"
              placeholder="VD: iPhone 16 Pro Max 256GB Chính Hãng VNA"
              class="w-full px-4 py-3 rounded-xl border border-outline-variant/50 focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm bg-surface font-semibold text-on-surface"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-on-surface mb-1">
              Mô tả chi tiết sản phẩm
            </label>
            <textarea
              v-model="form.description"
              rows="5"
              placeholder="Nhập giới thiệu thiết bị, đặc điểm nổi bật, thiết kế, thời lượng pin, chế độ bảo hành..."
              class="w-full px-4 py-3 rounded-xl border border-outline-variant/50 focus:border-primary focus:ring-2 focus:ring-primary/20 text-xs bg-surface text-on-surface leading-relaxed resize-y"
            ></textarea>
          </div>
        </div>

        <!-- 2. Thư viện hình ảnh -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/30 shadow-xs">
          <ProductImageUploader v-model:images="form.images" />
        </div>

        <!-- 3. Quản lý biến thể đa tầng -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/30 shadow-xs">
          <ProductVariantManager
            v-model:variants="form.variants"
            :available-images="form.images"
            :product-name="form.name"
          />
        </div>

        <!-- 4. Bảng thông số kỹ thuật (Technical Specifications) -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/30 space-y-4 shadow-xs">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-sm font-bold uppercase tracking-wider text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[18px]">settings_suggest</span>
                Thông số kỹ thuật chi tiết
              </h2>
              <p class="text-xs text-on-surface-variant mt-0.5">
                Hiển thị trong bảng thông số kỹ thuật ở trang chi tiết sản phẩm cho khách tra cứu.
              </p>
            </div>
            <button
              type="button"
              @click="addSpecificationRow"
              class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-primary text-primary hover:bg-primary/5 transition-all text-xs font-bold"
            >
              <span class="material-symbols-outlined text-[16px]">add</span>
              Thêm thông số
            </button>
          </div>

          <div v-if="specRows.length === 0" class="p-6 text-center border-2 border-dashed border-outline-variant/40 rounded-2xl">
            <p class="text-xs text-on-surface-variant">Chưa có thông số kỹ thuật nào.</p>
            <div class="mt-2 flex flex-wrap justify-center gap-2">
              <button
                type="button"
                @click="loadPresetSpecs('phone')"
                class="px-2.5 py-1 rounded-lg bg-surface-container text-xs font-semibold hover:bg-primary/10 hover:text-primary transition-colors"
              >
                + Mẫu Điện thoại
              </button>
              <button
                type="button"
                @click="loadPresetSpecs('laptop')"
                class="px-2.5 py-1 rounded-lg bg-surface-container text-xs font-semibold hover:bg-primary/10 hover:text-primary transition-colors"
              >
                + Mẫu Laptop
              </button>
              <button
                type="button"
                @click="loadPresetSpecs('general')"
                class="px-2.5 py-1 rounded-lg bg-surface-container text-xs font-semibold hover:bg-primary/10 hover:text-primary transition-colors"
              >
                + Mẫu Phụ kiện
              </button>
            </div>
          </div>

          <div v-else class="space-y-2">
            <div
              v-for="(row, idx) in specRows"
              :key="idx"
              class="flex items-center gap-2"
            >
              <input
                v-model="row.key"
                type="text"
                placeholder="Tên thông số (VD: Chipset, RAM, Màn hình...)"
                class="w-1/3 px-3 py-2 rounded-xl border border-outline-variant/40 text-xs bg-surface font-semibold text-on-surface"
              />
              <input
                v-model="row.value"
                type="text"
                placeholder="Giá trị (VD: Apple A18 Pro, 16GB, 120Hz...)"
                class="flex-1 px-3 py-2 rounded-xl border border-outline-variant/40 text-xs bg-surface text-on-surface"
              />
              <button
                type="button"
                @click="removeSpecificationRow(idx)"
                class="p-2 text-outline hover:text-error rounded-lg transition-colors"
                title="Xóa dòng"
              >
                <span class="material-symbols-outlined text-[18px]">delete</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- CỘT PHẢI (4 / 12): Phân loại, Trạng thái & Action Card Sticky -->
      <div class="lg:col-span-4 space-y-6 sticky top-24">
        <!-- 1. Trạng thái kinh doanh -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/30 space-y-4 shadow-xs">
          <h3 class="text-xs font-bold uppercase tracking-wider text-on-surface flex items-center gap-1.5">
            <span class="material-symbols-outlined text-primary text-[18px]">toggle_on</span>
            Trạng thái hiển thị
          </h3>

          <label class="flex items-center justify-between p-3 rounded-2xl bg-surface-container-low/50 cursor-pointer select-none group border border-outline-variant/30 hover:border-primary/40 transition-colors">
            <div>
              <strong class="text-xs text-on-surface block font-bold">
                {{ form.is_active ? 'Đang mở bán công khai' : 'Tạm ẩn khỏi cửa hàng' }}
              </strong>
              <span class="text-[11px] text-on-surface-variant block mt-0.5">
                {{ form.is_active ? 'Khách hàng có thể tìm kiếm và đặt mua ngay' : 'Chỉ Admin mới thấy trong trang quản trị' }}
              </span>
            </div>
            <input
              v-model="form.is_active"
              type="checkbox"
              class="w-5 h-5 rounded text-primary focus:ring-primary cursor-pointer"
            />
          </label>
        </div>

        <!-- 2. Ngành hàng & Thương hiệu -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/30 space-y-4 shadow-xs">
          <h3 class="text-xs font-bold uppercase tracking-wider text-on-surface flex items-center gap-1.5">
            <span class="material-symbols-outlined text-primary text-[18px]">category</span>
            Phân loại sản phẩm
          </h3>

          <div>
            <label class="block text-xs font-bold text-on-surface mb-1">
              Danh mục ngành hàng *
            </label>
            <select
              v-model="form.category_id"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/50 focus:border-primary text-xs bg-surface font-semibold text-on-surface"
            >
              <option value="" disabled>-- Chọn danh mục --</option>
              <option v-for="c in categories" :key="c.id" :value="c.id">
                {{ c.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-on-surface mb-1">
              Thương hiệu / Hãng *
            </label>
            <select
              v-model="form.brand_id"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/50 focus:border-primary text-xs bg-surface font-semibold text-on-surface"
            >
              <option value="" disabled>-- Chọn thương hiệu --</option>
              <option v-for="b in brands" :key="b.id" :value="b.id">
                {{ b.name }}
              </option>
            </select>
          </div>
        </div>

        <!-- 3. Nút hành động chính -->
        <div class="p-6 rounded-3xl bg-primary/5 border border-primary/20 space-y-3">
          <button
            type="button"
            @click="handleSave"
            :disabled="isSubmitting"
            class="w-full py-3 bg-primary text-on-primary rounded-xl text-xs font-black uppercase tracking-wider hover:bg-primary-container transition-all shadow-md shadow-primary/25 flex items-center justify-center gap-2 disabled:opacity-50"
          >
            <span v-if="isSubmitting" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
            <span v-else class="material-symbols-outlined text-[18px]">check_circle</span>
            <span>{{ isEditing ? 'Lưu thay đổi' : 'Hoàn tất & Đăng bán' }}</span>
          </button>
          <button
            type="button"
            @click="goBack"
            class="w-full py-2.5 rounded-xl border border-outline-variant/50 text-xs font-bold text-on-surface-variant hover:bg-surface-container transition-all text-center block"
          >
            Quay lại danh sách
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { adminService } from '../services/adminService'
import { useToast } from '@/core/composables/useToast'
import ProductImageUploader from '../components/ProductImageUploader.vue'
import ProductVariantManager, { type VariantItem } from '../components/ProductVariantManager.vue'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const productId = computed(() => {
  const p = route.params.id
  return p ? Number(p) : null
})
const isEditing = computed(() => !!productId.value)

const categories = ref<{ id: number; name: string }[]>([])
const brands = ref<{ id: number; name: string }[]>([])
const isSubmitting = ref(false)
const errorMessage = ref('')

const form = reactive({
  name: '',
  brand_id: '' as string | number,
  category_id: '' as string | number,
  description: '',
  is_active: true,
  images: [] as string[],
  variants: [] as VariantItem[],
})

// Thông số kỹ thuật dạng danh sách key-value
interface SpecRow {
  key: string
  value: string
}
const specRows = ref<SpecRow[]>([])

function addSpecificationRow() {
  specRows.value.push({ key: '', value: '' })
}

function removeSpecificationRow(index: number) {
  specRows.value.splice(index, 1)
}

function loadPresetSpecs(type: 'phone' | 'laptop' | 'general') {
  if (type === 'phone') {
    specRows.value = [
      { key: 'Hệ điều hành', value: 'iOS 18 / Android 14' },
      { key: 'Chipset (CPU)', value: 'Apple A18 Pro / Snapdragon 8 Gen 3' },
      { key: 'Màn hình', value: 'Super Retina XDR OLED 120Hz' },
      { key: 'Độ phân giải', value: '2868 x 1320 pixels' },
      { key: 'Camera sau', value: '48MP + 48MP + 12MP (Zoom 5x)' },
      { key: 'Dung lượng pin', value: '4,422 mAh' },
      { key: 'Kháng nước & bụi', value: 'IP68' },
    ]
  } else if (type === 'laptop') {
    specRows.value = [
      { key: 'Hệ điều hành', value: 'Windows 11 Home Bản Quyền' },
      { key: 'Vi xử lý (CPU)', value: 'Intel Core Ultra 7 / AMD Ryzen 9' },
      { key: 'Bộ nhớ RAM', value: '16GB LPDDR5X' },
      { key: 'Ổ cứng lưu trữ', value: '512GB SSD NVMe M.2' },
      { key: 'Màn hình', value: '15.6 inch 2.8K OLED 120Hz' },
      { key: 'Card đồ họa', value: 'NVIDIA GeForce RTX 4060 8GB' },
      { key: 'Trọng lượng máy', value: '1.65 kg' },
    ]
  } else {
    specRows.value = [
      { key: 'Thời gian bảo hành', value: '24 tháng chính hãng' },
      { key: 'Chuẩn kết nối', value: 'Bluetooth 5.3 / Type-C' },
      { key: 'Tương thích', value: 'iOS, Android, Windows, macOS' },
    ]
  }
}

async function loadDependencies() {
  try {
    const [cats, brs] = await Promise.all([
      adminService.getCategories(),
      adminService.getBrands(),
    ])
    categories.value = cats
    brands.value = brs
  } catch (err) {
    console.error('Lỗi khi tải danh mục hoặc thương hiệu:', err)
  }
}

async function loadProductDetail(id: number) {
  try {
    const prod = await adminService.getProduct(id)
    if (!prod) return

    form.name = prod.name || ''
    form.brand_id = prod.brand_id || ''
    form.category_id = prod.category_id || ''
    form.description = prod.description || ''
    form.is_active = !!prod.is_active

    // Load ảnh
    if (prod.images && Array.isArray(prod.images)) {
      form.images = prod.images.map((img: { image_url?: string } | string) => {
        return typeof img === 'string' ? img : img.image_url || ''
      }).filter(Boolean)
    }

    // Load biến thể
    if (prod.variants && Array.isArray(prod.variants)) {
      form.variants = prod.variants.map((v: {
        id?: number
        sku?: string
        price?: number | string
        stock?: number | string
        image_url?: string
        options?: string[]
      }) => ({
        id: v.id,
        sku: v.sku || '',
        sku_name: v.options?.[0] || v.sku || '',
        price: Number(v.price) || 0,
        stock: Number(v.stock) || 0,
        image_url: v.image_url || '',
      }))
    }

    // Load thông số kỹ thuật nếu có
    if (prod.specifications && typeof prod.specifications === 'object') {
      specRows.value = Object.entries(prod.specifications).map(([key, value]) => ({
        key,
        value: String(value),
      }))
    }
  } catch (err) {
    console.error('Lỗi khi tải thông tin sản phẩm:', err)
    errorMessage.value = 'Không thể tải thông tin sản phẩm để chỉnh sửa.'
  }
}

function goBack() {
  router.push('/admin/products')
}

async function handleSave() {
  errorMessage.value = ''

  if (!form.name.trim()) {
    errorMessage.value = 'Vui lòng nhập tên sản phẩm.'
    return
  }
  if (!form.category_id) {
    errorMessage.value = 'Vui lòng chọn danh mục cho sản phẩm.'
    return
  }
  if (!form.brand_id) {
    errorMessage.value = 'Vui lòng chọn thương hiệu cho sản phẩm.'
    return
  }
  if (form.variants.length === 0) {
    errorMessage.value = 'Sản phẩm phải có ít nhất 1 phiên bản/biến thể.'
    return
  }

  // Chuyển specRows thành object
  const specsObj: Record<string, string> = {}
  specRows.value.forEach((r) => {
    if (r.key.trim() && r.value.trim()) {
      specsObj[r.key.trim()] = r.value.trim()
    }
  })

  const payload: Record<string, unknown> = {
    name: form.name.trim(),
    category_id: form.category_id,
    brand_id: form.brand_id,
    description: form.description.trim(),
    is_active: form.is_active,
    images: form.images,
    variants: form.variants.map((v: VariantItem & { id?: number }) => ({
      id: v.id,
      sku: v.sku.trim(),
      sku_name: v.sku_name.trim(),
      price: v.price,
      stock: v.stock,
      image_url: v.image_url,
    })),
    specifications: specsObj,
  }

  isSubmitting.value = true
  try {
    if (isEditing.value && productId.value) {
      await adminService.updateProduct(productId.value, payload)
      toast.success('Cập nhật sản phẩm thành công!')
    } else {
      await adminService.createProduct(payload)
      toast.success('Thêm sản phẩm mới thành công!')
    }
    router.push('/admin/products')
  } catch (err: unknown) {
    console.error('Lỗi khi lưu sản phẩm:', err)
    const msg =
      (err as { response?: { data?: { message?: string } } })?.response?.data?.message ||
      'Có lỗi xảy ra khi lưu sản phẩm. Vui lòng kiểm tra lại thông tin.'
    errorMessage.value = msg
    toast.error(msg)
  } finally {
    isSubmitting.value = false
  }
}

onMounted(async () => {
  await loadDependencies()
  if (isEditing.value && productId.value) {
    await loadProductDetail(productId.value)
  } else {
    // Mặc định tạo sẵn 1 biến thể trống
    form.variants = [
      {
        sku_name: 'Bản Tiêu Chuẩn',
        sku: '',
        price: 0,
        stock: 10,
        image_url: '',
      },
    ]
  }
})
</script>
