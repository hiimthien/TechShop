<template>
  <div class="max-w-container-max mx-auto px-lg py-12 md:py-16">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-xs text-xs text-on-surface-variant mb-8">
      <RouterLink to="/" class="hover:text-primary transition-colors">Trang chủ</RouterLink>
      <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      <span class="text-on-surface font-medium">Trung tâm bảo hành TechShop</span>
    </nav>

    <div class="max-w-4xl mx-auto space-y-8">
      <div class="text-center md:text-left">
        <span class="inline-block px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full uppercase tracking-wider mb-2">
          Hậu Mãi & Dịch Vụ Khách Hàng
        </span>
        <h1 class="text-3xl md:text-4xl font-black text-on-surface leading-tight">
          Bảo Hành & Hỗ Trợ Kỹ Thuật
        </h1>
        <p class="text-xs md:text-sm text-on-surface-variant mt-2">
          Chính sách minh bạch, tra cứu tiến độ sửa chữa trực tuyến và gửi yêu cầu bảo hành nhanh chóng.
        </p>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-2 border-b border-outline-variant/30 pb-3">
        <button
          @click="activeTab = 'policy'"
          class="px-4 py-2 rounded-xl text-xs md:text-sm font-bold transition-all flex items-center gap-2"
          :class="activeTab === 'policy' ? 'bg-primary text-on-primary shadow-md' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high'"
        >
          <span class="material-symbols-outlined text-[18px]">verified</span>
          Chính Sách Bảo Hành
        </button>
        <button
          @click="activeTab = 'lookup'"
          class="px-4 py-2 rounded-xl text-xs md:text-sm font-bold transition-all flex items-center gap-2"
          :class="activeTab === 'lookup' ? 'bg-primary text-on-primary shadow-md' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high'"
        >
          <span class="material-symbols-outlined text-[18px]">search</span>
          Tra Cứu Tiến Độ
        </button>
        <button
          @click="activeTab = 'submit'"
          class="px-4 py-2 rounded-xl text-xs md:text-sm font-bold transition-all flex items-center gap-2"
          :class="activeTab === 'submit' ? 'bg-primary text-on-primary shadow-md' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high'"
        >
          <span class="material-symbols-outlined text-[18px]">handyman</span>
          Gửi Yêu Cầu Bảo Hành
        </button>
      </div>

      <!-- TAB 1: Chính sách & Quy định -->
      <div v-if="activeTab === 'policy'" class="space-y-6">
        <!-- Section 1 -->
        <section class="bg-surface-container-lowest p-6 md:p-8 rounded-2xl border border-outline-variant/30 space-y-4 shadow-xs">
          <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">verified_user</span>
            1. Chính sách Đổi mới trong 30 ngày
          </h2>
          <p class="text-xs md:text-sm text-on-surface-variant leading-relaxed">
            Trong vòng <strong>30 ngày</strong> đầu tiên kể từ thời điểm nhận hàng, nếu sản phẩm gặp bất kỳ lỗi kỹ thuật nào do nhà sản xuất (không phải lỗi người dùng), TechShop cam kết thực hiện:
          </p>
          <ul class="list-disc list-inside text-xs md:text-sm text-on-surface-variant space-y-2 pl-2">
            <li>Đổi ngay sản phẩm mới cùng model, cùng màu sắc và cấu hình.</li>
            <li>Trường hợp sản phẩm đã hết hàng, khách hàng có thể chọn đổi sang sản phẩm khác tương đương hoặc nhận hoàn tiền 100%.</li>
            <li>Điều kiện: Sản phẩm còn nguyên hộp, đầy đủ phụ kiện và hóa đơn mua hàng.</li>
          </ul>
        </section>

        <!-- Section 2 -->
        <section class="bg-surface-container-lowest p-6 md:p-8 rounded-2xl border border-outline-variant/30 space-y-4 shadow-xs">
          <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">schedule</span>
            2. Thời hạn bảo hành tiêu chuẩn
          </h2>
          <div class="overflow-x-auto text-xs md:text-sm">
            <table class="w-full border-collapse">
              <thead>
                <tr class="border-b border-outline-variant/30 bg-surface-container-low text-left">
                  <th class="p-3 font-bold text-on-surface">Ngành hàng</th>
                  <th class="p-3 font-bold text-on-surface">Thời gian bảo hành</th>
                  <th class="p-3 font-bold text-on-surface">Hình thức</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant/20 text-on-surface-variant">
                <tr>
                  <td class="p-3 font-medium text-on-surface">Laptop & Màn hình</td>
                  <td class="p-3 font-bold text-primary">24 tháng chính hãng</td>
                  <td class="p-3">Bảo hành tại TTBH ủy quyền</td>
                </tr>
                <tr>
                  <td class="p-3 font-medium text-on-surface">Điện thoại / Tablet</td>
                  <td class="p-3 font-bold text-primary">12 - 24 tháng</td>
                  <td class="p-3">Bảo hành chính hãng</td>
                </tr>
                <tr>
                  <td class="p-3 font-medium text-on-surface">Chuột & Bàn phím cơ</td>
                  <td class="p-3 font-bold text-primary">24 tháng</td>
                  <td class="p-3">Đổi mới linh kiện hoặc đổi máy</td>
                </tr>
                <tr>
                  <td class="p-3 font-medium text-on-surface">Tai nghe & Loa</td>
                  <td class="p-3 font-bold text-primary">12 tháng</td>
                  <td class="p-3">Bảo hành chính hãng</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Section 3 -->
        <section class="bg-surface-container-lowest p-6 md:p-8 rounded-2xl border border-outline-variant/30 space-y-4 shadow-xs">
          <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-error">cancel</span>
            3. Các trường hợp từ chối bảo hành
          </h2>
          <ul class="list-disc list-inside text-xs md:text-sm text-on-surface-variant space-y-2 pl-2">
            <li>Sản phẩm bị rơi vỡ, nứt mẻ, biến dạng cơ học hoặc có dấu hiệu can thiệp, sửa chữa không qua ủy quyền.</li>
            <li>Sản phẩm bị ngấm nước, oxy hóa mạch, chập điện do nguồn điện không ổn định.</li>
            <li>Tem bảo hành hoặc số Serial/IMEI bị rách, tẩy xóa hoặc không còn nguyên vẹn.</li>
          </ul>
        </section>
      </div>

      <!-- TAB 2: Tra cứu tiến độ bảo hành -->
      <div v-else-if="activeTab === 'lookup'" class="space-y-6">
        <div class="bg-surface-container-lowest p-6 md:p-8 rounded-2xl border border-outline-variant/30 shadow-xs space-y-4">
          <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">search</span>
            Tra cứu trạng thái tiếp nhận & sửa chữa
          </h2>
          <p class="text-xs text-on-surface-variant">
            Nhập <strong>Số Serial / IMEI</strong> thiết bị, <strong>Mã phiếu</strong> (VD: BH-XXXXXX), hoặc <strong>Số điện thoại</strong> mua hàng:
          </p>

          <form @submit.prevent="handleLookup" class="flex flex-col sm:flex-row gap-3">
            <input
              v-model="lookupQuery"
              type="text"
              required
              placeholder="VD: SN-IP16-8921, BH-100234 hoặc 0912345678"
              class="flex-1 px-4 py-3 rounded-xl border border-outline-variant/40 bg-surface text-on-surface text-sm focus:outline-none focus:border-primary font-mono uppercase"
            />
            <button
              type="submit"
              :disabled="lookupLoading"
              class="px-6 py-3 rounded-xl bg-primary text-on-primary text-sm font-bold shadow-md hover:bg-primary-container transition disabled:opacity-50"
            >
              {{ lookupLoading ? 'Đang tìm...' : 'Tra Cứu Ngay' }}
            </button>
          </form>

          <!-- Lookup Results -->
          <div v-if="hasSearched" class="pt-4 border-t border-outline-variant/20 space-y-6">
            <!-- 1. Kết quả tra cứu thiết bị đã mua & thời hạn bảo hành -->
            <div v-if="deviceResults.length > 0" class="space-y-3">
              <h3 class="text-xs font-bold uppercase tracking-wider text-primary flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">devices</span>
                Thông tin thiết bị & Thời hạn bảo hành
              </h3>
              <div
                v-for="(dev, dIdx) in deviceResults"
                :key="dIdx"
                class="p-5 rounded-2xl border border-primary/20 bg-primary/5 space-y-3"
              >
                <div class="flex flex-wrap items-center justify-between gap-2">
                  <div>
                    <h4 class="font-bold text-on-surface text-sm">{{ dev.product_name }}</h4>
                    <p class="text-xs text-on-surface-variant mt-0.5">Số Serial / IMEI: <strong class="font-mono text-primary">{{ dev.serial_number }}</strong></p>
                  </div>
                  <span
                    class="px-3 py-1 rounded-full text-xs font-bold"
                    :class="dev.is_expired ? 'bg-error-container text-on-error-container' : 'bg-primary text-on-primary'"
                  >
                    {{ dev.status_label }}
                  </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs bg-surface p-3.5 rounded-xl border border-outline-variant/30">
                  <div>
                    <span class="text-on-surface-variant block">Mã đơn hàng:</span>
                    <strong class="text-on-surface font-mono">{{ dev.order_code }}</strong>
                  </div>
                  <div>
                    <span class="text-on-surface-variant block">Ngày mua / Kích hoạt:</span>
                    <strong class="text-on-surface">{{ dev.purchase_date || 'N/A' }}</strong>
                  </div>
                  <div>
                    <span class="text-on-surface-variant block">Hạn bảo hành đến:</span>
                    <strong :class="dev.is_expired ? 'text-error' : 'text-primary'">{{ dev.warranty_expires || 'N/A' }}</strong>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Danh sách phiếu bảo hành RMA -->
            <div v-if="lookupResults.length > 0" class="space-y-3">
              <h3 class="text-xs font-bold uppercase tracking-wider text-primary flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">build_circle</span>
                Tiến độ xử lý phiếu bảo hành RMA
              </h3>
              <div class="space-y-4">
                <div
                  v-for="claim in lookupResults"
                  :key="claim.id"
                  class="p-5 rounded-2xl border border-outline-variant/40 bg-surface space-y-3"
                >
                  <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                      <span class="font-mono font-bold text-primary text-base">{{ claim.claim_code }}</span>
                      <span class="text-xs text-on-surface-variant">({{ claim.product_name }})</span>
                    </div>
                    <span
                      class="px-3 py-1 rounded-full text-xs font-bold"
                      :class="getBadgeClass(claim.status)"
                    >
                      {{ getStatusText(claim.status) }}
                    </span>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-on-surface-variant">
                    <div><strong>Khách hàng:</strong> {{ claim.customer_name }} ({{ claim.customer_phone }})</div>
                    <div><strong>Số Serial:</strong> {{ claim.serial_number || 'N/A' }}</div>
                  </div>

                  <div class="text-xs bg-surface-container p-3 rounded-xl text-on-surface">
                    <strong>Hiện tượng lỗi:</strong> {{ claim.issue_description }}
                  </div>

                  <div v-if="claim.admin_note" class="text-xs bg-primary/10 border border-primary/20 p-3 rounded-xl text-primary font-medium">
                    <strong>Phản hồi từ Kỹ thuật viên:</strong> {{ claim.admin_note }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Trống kết quả -->
            <div v-if="!lookupResults.length && !deviceResults.length" class="text-center py-8 text-on-surface-variant text-sm">
              Không tìm thấy thông tin thiết bị hoặc phiếu bảo hành nào khớp với từ khóa "{{ lookupQuery }}".
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 3: Gửi yêu cầu bảo hành online -->
      <div v-else-if="activeTab === 'submit'" class="space-y-6">
        <div class="bg-surface-container-lowest p-6 md:p-8 rounded-2xl border border-outline-variant/30 shadow-xs space-y-6">
          <div>
            <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
              <span class="material-symbols-outlined text-primary">handyman</span>
              Đăng ký phiếu bảo hành online
            </h2>
            <p class="text-xs text-on-surface-variant mt-1">
              Điền thông tin sự cố sản phẩm. Bộ phận CSKH TechShop sẽ liên hệ xác nhận và hướng dẫn gửi thiết bị về trung tâm bảo hành gần nhất.
            </p>
          </div>

          <div v-if="submitSuccessMsg" class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 text-sm font-medium">
            {{ submitSuccessMsg }}
          </div>

          <form @submit.prevent="handleSubmitClaim" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Họ và tên quý khách *</label>
                <input
                  v-model="claimForm.customer_name"
                  type="text"
                  required
                  placeholder="VD: Nguyễn Văn A"
                  class="w-full px-4 py-2.5 rounded-xl border border-outline-variant/40 bg-surface text-on-surface text-sm focus:outline-none focus:border-primary"
                />
              </div>
              <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Số điện thoại liên hệ *</label>
                <input
                  v-model="claimForm.customer_phone"
                  type="tel"
                  required
                  placeholder="VD: 0987654321"
                  class="w-full px-4 py-2.5 rounded-xl border border-outline-variant/40 bg-surface text-on-surface text-sm focus:outline-none focus:border-primary"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Tên sản phẩm thiết bị *</label>
                <input
                  v-model="claimForm.product_name"
                  type="text"
                  required
                  placeholder="VD: Laptop Asus ROG Strix G16..."
                  class="w-full px-4 py-2.5 rounded-xl border border-outline-variant/40 bg-surface text-on-surface text-sm focus:outline-none focus:border-primary"
                />
              </div>
              <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Số Serial / IMEI (nếu có)</label>
                <input
                  v-model="claimForm.serial_number"
                  type="text"
                  placeholder="VD: SN-19823482"
                  class="w-full px-4 py-2.5 rounded-xl border border-outline-variant/40 bg-surface text-on-surface text-sm focus:outline-none focus:border-primary"
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-on-surface mb-1">Mô tả chi tiết tình trạng lỗi *</label>
              <textarea
                v-model="claimForm.issue_description"
                required
                rows="4"
                placeholder="Mô tả cụ thể hiện tượng lỗi: Không lên nguồn, màn hình sọc, liệt phím, rè âm thanh..."
                class="w-full px-4 py-2.5 rounded-xl border border-outline-variant/40 bg-surface text-on-surface text-sm focus:outline-none focus:border-primary"
              ></textarea>
            </div>

            <button
              type="submit"
              :disabled="submittingClaim"
              class="w-full py-3.5 rounded-xl bg-primary text-on-primary font-bold text-sm shadow-md hover:bg-primary-container transition disabled:opacity-50"
            >
              {{ submittingClaim ? 'Đang gửi yêu cầu...' : 'Gửi Yêu Cầu Bảo Hành' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import api from '@/core/api/axios'
import { useToast } from '@/core/composables/useToast'

const toast = useToast()
const activeTab = ref<'policy' | 'lookup' | 'submit'>('policy')

interface WarrantyLookupItem {
  id: number
  claim_code?: string
  product_name: string
  serial_number?: string
  customer_name?: string
  customer_phone?: string
  status: string
  issue_description?: string
  admin_note?: string
  created_at: string
}

interface WarrantyDeviceItem {
  serial_number: string
  product_name: string
  order_code: string
  purchase_date: string
  warranty_expires: string
  is_expired: boolean
  status_label: string
  warranty_months: number
}

// Lookup state
const lookupQuery = ref('')
const lookupLoading = ref(false)
const hasSearched = ref(false)
const lookupResults = ref<WarrantyLookupItem[]>([])
const deviceResults = ref<WarrantyDeviceItem[]>([])

async function handleLookup() {
  if (!lookupQuery.value.trim()) return

  try {
    lookupLoading.value = true
    hasSearched.value = true
    const res = await api.get('/warranty-claims/lookup', {
      params: { query: lookupQuery.value.trim() },
    })
    const payload = res.data.data
    if (payload && typeof payload === 'object' && !Array.isArray(payload)) {
      lookupResults.value = payload.claims || []
      deviceResults.value = payload.devices || []
    } else {
      lookupResults.value = (payload || []) as WarrantyLookupItem[]
      deviceResults.value = []
    }
  } catch (err) {
    console.error('Lỗi khi tra cứu bảo hành:', err)
    lookupResults.value = []
    deviceResults.value = []
  } finally {
    lookupLoading.value = false
  }
}

// Submit Claim state
const claimForm = ref({
  customer_name: '',
  customer_phone: '',
  customer_email: '',
  product_name: '',
  serial_number: '',
  issue_description: '',
})
const submittingClaim = ref(false)
const submitSuccessMsg = ref('')

async function handleSubmitClaim() {
  try {
    submittingClaim.value = true
    submitSuccessMsg.value = ''
    const res = await api.post('/warranty-claims', claimForm.value)
    const successMsg = res.data.message || 'Gửi yêu cầu bảo hành thành công!'
    submitSuccessMsg.value = successMsg
    toast.success(successMsg)
    claimForm.value = {
      customer_name: '',
      customer_phone: '',
      customer_email: '',
      product_name: '',
      serial_number: '',
      issue_description: '',
    }
  } catch (err: unknown) {
    const errorMsg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Có lỗi xảy ra khi gửi yêu cầu.'
    toast.error(errorMsg)
  } finally {
    submittingClaim.value = false
  }
}

function getBadgeClass(status: string): string {
  switch (status) {
    case 'pending':
      return 'bg-amber-100 text-amber-800'
    case 'received':
      return 'bg-blue-100 text-blue-800'
    case 'inspecting':
      return 'bg-purple-100 text-purple-800'
    case 'completed':
      return 'bg-emerald-100 text-emerald-800'
    case 'rejected':
      return 'bg-rose-100 text-rose-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

function getStatusText(status: string): string {
  switch (status) {
    case 'pending':
      return 'Chờ tiếp nhận'
    case 'received':
      return 'Đã nhận máy tại TTBH'
    case 'inspecting':
      return 'Đang kiểm tra kỹ thuật'
    case 'completed':
      return 'Đã hoàn tất bảo hành'
    case 'rejected':
      return 'Từ chối bảo hành'
    default:
      return status
  }
}
</script>
