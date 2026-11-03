<template>
  <BaseModal
    :model-value="modelValue"
    @update:model-value="(val) => $emit('update:modelValue', val)"
    title="Tạo yêu cầu bảo hành thiết bị"
    subtitle="Điền thông tin sự cố để trung tâm bảo hành tiếp nhận và xử lý nhanh chóng."
    icon="build"
    max-width="lg"
  >
    <form @submit.prevent="handleSubmit" class="space-y-4 text-xs">
      <div>
        <label class="block font-bold text-on-surface mb-1">Tên thiết bị / Sản phẩm *</label>
        <input
          v-model="form.product_name"
          type="text"
          required
          placeholder="Ví dụ: Màn hình Dell S2421HN, iPhone 15 Pro..."
          class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/40 bg-surface-container-low focus:outline-none focus:border-primary text-xs"
        />
      </div>

      <div>
        <label class="block font-bold text-on-surface mb-1">Số Serial / IMEI (Nếu có)</label>
        <input
          v-model="form.serial_number"
          type="text"
          placeholder="Nhập số serial in sau máy hoặc trên hóa đơn"
          class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/40 bg-surface-container-low focus:outline-none focus:border-primary text-xs font-mono"
        />
      </div>

      <div>
        <label class="block font-bold text-on-surface mb-1">Mô tả chi tiết lỗi phát sinh *</label>
        <textarea
          v-model="form.issue_description"
          required
          rows="3"
          placeholder="Mô tả cụ thể triệu chứng: không lên nguồn, sọc màn hình, liệt phím..."
          class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/40 bg-surface-container-low focus:outline-none focus:border-primary text-xs resize-none"
        ></textarea>
      </div>

      <div>
        <label class="block font-bold text-on-surface mb-1">Địa chỉ tiếp nhận máy / gửi trả</label>
        <input
          v-model="form.address"
          type="text"
          placeholder="Địa chỉ nhà hoặc nơi nhận máy của bạn"
          class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/40 bg-surface-container-low focus:outline-none focus:border-primary text-xs"
        />
      </div>

      <div v-if="errorMessage" class="p-3 rounded-xl bg-error/10 text-error text-xs font-semibold">
        {{ errorMessage }}
      </div>

      <div class="flex justify-end gap-2 pt-3 border-t border-outline-variant/20">
        <button
          type="button"
          @click="$emit('update:modelValue', false)"
          class="px-4 py-2 rounded-xl border border-outline-variant/40 text-on-surface-variant text-xs font-bold hover:bg-surface-container transition-colors"
        >
          Hủy bỏ
        </button>
        <button
          type="submit"
          :disabled="submitting"
          class="px-5 py-2 bg-primary text-white rounded-xl font-bold text-xs hover:bg-primary-hover transition-all shadow-sm flex items-center gap-1.5 disabled:opacity-50"
        >
          <span v-if="submitting" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
          <span>Gửi phiếu bảo hành</span>
        </button>
      </div>
    </form>
  </BaseModal>
</template>

<script setup lang="ts">
import BaseModal from '@/core/components/ui/BaseModal.vue'

export interface WarrantyFormData {
  product_name: string
  serial_number: string
  issue_description: string
  address: string
}

interface Props {
  modelValue: boolean
  form: WarrantyFormData
  submitting?: boolean
  errorMessage?: string
}

defineProps<Props>()
const emit = defineEmits<{
  (e: 'update:modelValue', val: boolean): void
  (e: 'submit'): void
}>()

function handleSubmit() {
  emit('submit')
}
</script>

