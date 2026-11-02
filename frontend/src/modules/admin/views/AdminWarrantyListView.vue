<template>
  <div class="space-y-lg max-w-container-max mx-auto">
    <!-- Header Section -->
    <header class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-sm mb-md">
      <div>
        <h1 class="text-headline-lg font-headline-lg font-bold text-on-surface tracking-tight">Quản lý phiếu bảo hành (RMA)</h1>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Tiếp nhận yêu cầu bảo hành, ghi chú kỹ thuật, kiểm tra linh kiện và trả máy cho khách.</p>
      </div>
      <div>
        <button
          @click="fetchWarranties(1)"
          :disabled="loading"
          class="bg-surface-container hover:bg-surface-container-high text-on-surface px-lg py-md rounded-lg flex items-center gap-xs transition-all shadow-sm text-label-md font-label-md border border-outline-variant/30"
        >
          <span class="material-symbols-outlined text-[18px]" :class="{ 'animate-spin': loading }">refresh</span>
          Làm mới
        </button>
      </div>
    </header>

    <!-- Filters & Search Section -->
    <section class="bg-surface p-lg rounded-xl shadow-sm border border-outline-variant/30">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-md">
        <!-- Status Tabs -->
        <div class="flex items-center gap-xs overflow-x-auto pb-1 sm:pb-0 bg-surface-container-low p-base rounded-lg border border-outline-variant/40">
          <button
            v-for="tab in statusTabs"
            :key="tab.key"
            @click="selectStatus(tab.key)"
            class="px-md py-xs rounded-md text-label-md font-bold whitespace-nowrap transition-all"
            :class="currentStatus === tab.key ? 'bg-surface text-primary shadow-sm' : 'text-on-surface-variant hover:bg-surface-container'"
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- Search input -->
        <div class="relative w-full sm:w-80">
          <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
          <input
            v-model="searchQuery"
            @keyup.enter="fetchWarranties(1)"
            type="text"
            placeholder="Tìm mã BH, tên khách, SĐT, máy..."
            class="w-full pl-xl pr-md py-xs bg-surface-container-low border border-outline-variant rounded-full focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-body-sm"
          />
        </div>
      </div>
    </section>

    <!-- Warranties Table -->
    <section class="bg-surface rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
      <div v-if="loading" class="p-16 text-center text-on-surface-variant">
        <div class="inline-block animate-spin w-8 h-8 border-4 border-primary border-t-transparent rounded-full mb-3"></div>
        <div class="text-body-sm font-medium">Đang tải danh sách bảo hành...</div>
      </div>

      <div v-else-if="!warranties.length" class="p-16 text-center text-on-surface-variant">
        <span class="material-symbols-outlined text-5xl text-outline mb-2">verified_user</span>
        <div class="text-headline-sm font-bold text-on-surface">Không có phiếu bảo hành nào</div>
        <p class="text-body-sm text-on-surface-variant mt-1">Hệ thống chưa ghi nhận yêu cầu hoặc không khớp với bộ lọc</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant/50 text-xs font-bold uppercase tracking-wider text-on-surface-variant">
              <th class="py-3.5 px-4 whitespace-nowrap">Mã phiếu BH</th>
              <th class="py-3.5 px-4 whitespace-nowrap">Khách hàng</th>
              <th class="py-3.5 px-4">Thiết bị & Serial</th>
              <th class="py-3.5 px-4">Mô tả sự cố</th>
              <th class="py-3.5 px-4 whitespace-nowrap text-center">Trạng thái</th>
              <th class="py-3.5 px-4 whitespace-nowrap">Ngày gửi</th>
              <th class="py-3.5 px-4 whitespace-nowrap text-right pr-6">Hành động</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30">
            <tr
              v-for="claim in warranties"
              :key="claim.id"
              class="hover:bg-surface-container-low transition-colors"
            >
              <td class="py-3.5 px-4 whitespace-nowrap">
                <span class="inline-block px-2.5 py-1 rounded-lg bg-error-container text-on-error-container font-mono font-bold text-xs whitespace-nowrap">
                  {{ claim.claim_code }}
                </span>
              </td>
              <td class="py-3.5 px-4 whitespace-nowrap">
                <div class="font-bold text-on-surface text-sm">{{ claim.customer_name }}</div>
                <div class="text-xs text-on-surface-variant font-mono">{{ claim.customer_phone }}</div>
              </td>
              <td class="py-3.5 px-4 max-w-[220px]">
                <div class="font-medium text-on-surface text-sm truncate" :title="claim.product_name">{{ claim.product_name }}</div>
                <div class="text-xs text-on-surface-variant font-mono truncate">SN: {{ claim.serial_number || 'N/A' }}</div>
              </td>
              <td class="py-3.5 px-4 max-w-[200px]">
                <div class="text-xs text-on-surface-variant truncate" :title="claim.issue_description">
                  {{ claim.issue_description }}
                </div>
              </td>
              <td class="py-3.5 px-4 whitespace-nowrap text-center">
                <span
                  class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold"
                  :class="getWarrantyStatusBadgeClass(claim.status)"
                >
                  {{ getWarrantyStatusText(claim.status) }}
                </span>
              </td>
              <td class="py-3.5 px-4 text-xs text-on-surface-variant whitespace-nowrap">
                {{ formatDate(claim.created_at) }}
              </td>
              <td class="py-3.5 px-4 text-right whitespace-nowrap pr-6">
                <button
                  @click="openProcessModal(claim)"
                  class="bg-surface-container hover:bg-primary hover:text-on-primary text-on-surface px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-xs border border-outline-variant/40"
                >
                  Xử lý phiếu
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Process Warranty Modal -->
    <div
      v-if="selectedClaim"
      class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
    >
      <div class="bg-surface rounded-2xl w-full max-w-2xl shadow-2xl border border-outline-variant overflow-hidden my-8">
        <div class="p-lg border-b border-outline-variant flex items-center justify-between bg-surface-container-lowest">
          <div>
            <div class="flex items-center gap-sm">
              <h2 class="text-headline-sm font-headline-sm font-bold text-on-surface">Xử Lý Bảo Hành {{ selectedClaim.claim_code }}</h2>
              <span
                class="px-2.5 py-0.5 rounded-full text-xs font-bold"
                :class="getWarrantyStatusBadgeClass(selectedClaim.status)"
              >
                {{ getWarrantyStatusText(selectedClaim.status) }}
              </span>
            </div>
            <p class="text-label-sm text-on-surface-variant mt-0.5">Tiếp nhận ngày: {{ formatDate(selectedClaim.created_at) }}</p>
          </div>
          <button
            @click="selectedClaim = null"
            class="p-xs rounded-lg text-on-surface-variant hover:bg-surface-container transition"
          >
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>

        <div class="p-lg space-y-md">
          <!-- Information grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
            <div class="p-md rounded-xl bg-surface-container-lowest border border-outline-variant/40">
              <div class="text-label-sm font-bold uppercase tracking-wider text-on-surface-variant mb-1">Khách Hàng</div>
              <div class="text-body-sm font-bold text-on-surface">{{ selectedClaim.customer_name }}</div>
              <div class="text-body-sm text-on-surface-variant mt-1">SĐT: {{ selectedClaim.customer_phone }}</div>
              <div class="text-body-sm text-on-surface-variant mt-0.5">Email: {{ selectedClaim.customer_email || 'Chưa cung cấp' }}</div>
            </div>

            <div class="p-md rounded-xl bg-surface-container-lowest border border-outline-variant/40">
              <div class="text-label-sm font-bold uppercase tracking-wider text-on-surface-variant mb-1">Thiết Bị Cần Bảo Hành</div>
              <div class="text-body-sm font-bold text-on-surface">{{ selectedClaim.product_name }}</div>
              <div class="text-body-sm text-on-surface-variant mt-1">Số Serial: {{ selectedClaim.serial_number || 'N/A' }}</div>
              <div class="text-body-sm text-on-surface-variant mt-0.5">Ngày mua: {{ formatDate(selectedClaim.purchase_date || '') }}</div>
            </div>
          </div>

          <!-- Customer reported issue -->
          <div class="p-md rounded-xl bg-surface-container-lowest border border-outline-variant/40">
            <div class="text-label-sm font-bold uppercase tracking-wider text-on-surface-variant mb-1">Hiện Tượng Lỗi Khách Báo</div>
            <p class="text-body-sm text-on-surface whitespace-pre-wrap">{{ selectedClaim.issue_description }}</p>
          </div>

          <!-- Action & Admin Note Form -->
          <div class="space-y-md pt-xs">
            <div>
              <label class="block text-label-sm font-bold uppercase tracking-wider text-primary mb-1">
                Cập Nhật Trạng Thái Xử Lý
              </label>
              <select
                v-model="claimStatus"
                class="w-full px-md py-md bg-surface-container-low border border-outline-variant rounded-lg text-body-sm font-bold text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer"
              >
                <option value="pending">Chờ xác nhận (Pending)</option>
                <option value="received">Đã nhận máy tại TTBH (Received)</option>
                <option value="inspecting">Đang kiểm tra kỹ thuật / Thay thế linh kiện (Inspecting)</option>
                <option value="completed">Đã hoàn tất / Sẵn sàng trả máy (Completed)</option>
                <option value="rejected">Từ chối bảo hành (Rejected)</option>
              </select>
            </div>

            <div>
              <label class="block text-label-sm font-bold uppercase tracking-wider text-on-surface-variant mb-1">
                Ghi Chú Kỹ Thuật Viên / Lý Do Từ Chối (Khách hàng sẽ nhìn thấy khi tra cứu)
              </label>
              <textarea
                v-model="adminNote"
                rows="3"
                placeholder="Nhập ghi chú tình trạng kiểm tra phần cứng hoặc lý do từ chối..."
                class="w-full px-md py-md bg-surface-container-low border border-outline-variant rounded-lg text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
              ></textarea>
            </div>
          </div>
        </div>

        <div class="p-lg border-t border-outline-variant bg-surface-container-lowest flex justify-end gap-sm">
          <button
            @click="selectedClaim = null"
            class="px-lg py-md rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface text-label-md font-bold transition"
          >
            Đóng
          </button>
          <button
            @click="updateClaim"
            :disabled="updating"
            class="px-xl py-md rounded-lg bg-primary hover:bg-primary-container text-on-primary text-label-md font-bold transition shadow-sm disabled:opacity-50"
          >
            {{ updating ? 'Đang lưu...' : 'Lưu kết quả' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { adminService, type WarrantyClaim } from '@/modules/admin/services/adminService'

const loading = ref(true)
const warranties = ref<WarrantyClaim[]>([])
const currentStatus = ref('all')
const searchQuery = ref('')

const statusTabs = [
  { key: 'all', label: 'Tất cả' },
  { key: 'pending', label: 'Chờ tiếp nhận' },
  { key: 'received', label: 'Đã nhận máy' },
  { key: 'inspecting', label: 'Đang kiểm tra' },
  { key: 'completed', label: 'Đã hoàn tất' },
  { key: 'rejected', label: 'Từ chối' },
]

const selectedClaim = ref<WarrantyClaim | null>(null)
const claimStatus = ref('pending')
const adminNote = ref('')
const updating = ref(false)

async function fetchWarranties(page = 1) {
  try {
    loading.value = true
    const res = (await adminService.getWarranties({
      page,
      status: currentStatus.value,
      search: searchQuery.value,
    })) as { data: WarrantyClaim[] }
    warranties.value = res.data
  } catch (err: unknown) {
    console.error('Lỗi khi tải phiếu bảo hành:', err)
  } finally {
    loading.value = false
  }
}

function selectStatus(key: string) {
  currentStatus.value = key
  fetchWarranties(1)
}

function openProcessModal(claim: WarrantyClaim) {
  selectedClaim.value = claim
  claimStatus.value = claim.status
  adminNote.value = claim.admin_note || ''
}

async function updateClaim() {
  if (!selectedClaim.value) return
  try {
    updating.value = true
    await adminService.updateWarrantyStatus(selectedClaim.value.id, claimStatus.value, adminNote.value)
    selectedClaim.value.status = claimStatus.value as WarrantyClaim['status']
    selectedClaim.value.admin_note = adminNote.value
    const inList = warranties.value.find((w) => w.id === selectedClaim.value?.id)
    if (inList) {
      inList.status = claimStatus.value as WarrantyClaim['status']
      inList.admin_note = adminNote.value
    }
    selectedClaim.value = null
  } catch {
    alert('Không thể cập nhật phiếu bảo hành!')
  } finally {
    updating.value = false
  }
}

function formatDate(dateStr: string): string {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  })
}

function getWarrantyStatusBadgeClass(status: string): string {
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
      return 'bg-surface-container text-on-surface-variant'
  }
}

function getWarrantyStatusText(status: string): string {
  switch (status) {
    case 'pending':
      return 'Chờ tiếp nhận'
    case 'received':
      return 'Đã nhận máy'
    case 'inspecting':
      return 'Đang kiểm tra'
    case 'completed':
      return 'Đã hoàn tất'
    case 'rejected':
      return 'Từ chối'
    default:
      return status
  }
}

onMounted(() => {
  fetchWarranties(1)
})
</script>
