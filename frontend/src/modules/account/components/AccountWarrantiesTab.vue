<template>
  <div class="p-6 md:p-8 bg-surface-container-lowest rounded-3xl border border-outline-variant/30 space-y-6 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-outline-variant/20 gap-3">
      <div>
        <h3 class="text-lg font-black text-on-surface flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">build</span>
          Quản lý phiếu bảo hành của bạn
        </h3>
        <p class="text-xs text-on-surface-variant mt-1">
          Theo dõi tiến độ thẩm định, bảo hành và sửa chữa thiết bị
        </p>
      </div>
      <button
        type="button"
        @click="$emit('create')"
        class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-hover transition-all flex items-center gap-1.5 self-start sm:self-auto shadow-sm"
      >
        <span class="material-symbols-outlined text-[18px]">add_circle</span>
        <span>Gửi yêu cầu bảo hành</span>
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="py-12 text-center text-xs text-outline flex flex-col items-center gap-2">
      <span class="material-symbols-outlined animate-spin text-2xl text-primary">progress_activity</span>
      <span>Đang tải thông tin bảo hành...</span>
    </div>

    <!-- Empty -->
    <div v-else-if="warranties.length === 0">
      <EmptyState
        icon="verified_user"
        title="Bạn chưa có phiếu bảo hành nào"
        description="Nếu thiết bị bạn mua tại TechShop gặp sự cố kỹ thuật, hãy tạo phiếu bảo hành để chúng tôi hỗ trợ ngay."
        action-text="Gửi yêu cầu bảo hành đầu tiên"
        action-icon="build"
        @action="$emit('create')"
      />
    </div>

    <!-- Danh sách phiếu bảo hành -->
    <div v-else class="space-y-4">
      <div
        v-for="claim in warranties"
        :key="claim.id"
        class="p-5 rounded-2xl border border-outline-variant/30 bg-surface-container-lowest hover:border-primary/40 transition-all space-y-3"
      >
        <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-outline-variant/15">
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">receipt_long</span>
            <strong class="text-sm font-black text-on-surface">#{{ claim.claim_code }}</strong>
            <span class="text-[11px] text-outline">• Ngày gửi: {{ formatDate(claim.created_at) }}</span>
          </div>

          <!-- Status Badge -->
          <StatusBadge :status="claim.status" :label="getWarrantyStatusLabel(claim.status)" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
          <div>
            <span class="text-outline block mb-0.5">Thiết bị bảo hành:</span>
            <strong class="text-on-surface text-sm block">{{ claim.product_name }}</strong>
            <span v-if="claim.serial_number" class="text-[11px] text-on-surface-variant block mt-0.5 font-mono">
              Serial / IMEI: <span class="font-bold text-primary">{{ claim.serial_number }}</span>
            </span>
          </div>

          <div>
            <span class="text-outline block mb-0.5">Tình trạng / Lỗi phát sinh:</span>
            <p class="text-on-surface-variant bg-surface-container-low/40 p-2.5 rounded-xl text-xs leading-relaxed">
              {{ claim.issue_description }}
            </p>
          </div>
        </div>

        <!-- Phản hồi kỹ thuật nếu có -->
        <div
          v-if="claim.admin_note"
          class="p-3 rounded-xl bg-primary/5 border border-primary/20 text-xs flex items-start gap-2"
        >
          <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">support_agent</span>
          <div>
            <strong class="text-primary block font-bold">Ghi chú từ Kỹ thuật viên TechShop:</strong>
            <p class="text-on-surface-variant mt-0.5 leading-relaxed">{{ claim.admin_note }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { WarrantyClaimItem } from '../services/accountService'
import { useFormatters } from '@/core/composables/useFormatters'
import StatusBadge from '@/core/components/ui/StatusBadge.vue'
import EmptyState from '@/core/components/ui/EmptyState.vue'

interface Props {
  warranties: WarrantyClaimItem[]
  loading?: boolean
}

withDefaults(defineProps<Props>(), {
  loading: false,
})

defineEmits<{
  (e: 'create'): void
}>()

const { formatDate } = useFormatters()

function getWarrantyStatusLabel(status: string) {
  switch (status) {
    case 'pending': return 'Đang tiếp nhận'
    case 'processing': return 'Đang kiểm tra'
    case 'completed': return 'Đã bảo hành xong'
    case 'rejected': return 'Từ chối bảo hành'
    default: return status
  }
}
</script>

