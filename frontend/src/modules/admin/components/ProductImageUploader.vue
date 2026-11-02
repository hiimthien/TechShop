<template>
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-primary">
          Hình ảnh sản phẩm (Upload nhiều file)
        </label>
        <p class="text-[11px] text-on-surface-variant">
          Ảnh đầu tiên sẽ là ảnh đại diện chính. Hỗ trợ JPG, PNG, WEBP tối đa 5MB/ảnh.
        </p>
      </div>
      <label
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-on-primary transition-all text-xs font-bold cursor-pointer"
        :class="{ 'opacity-60 pointer-events-none': isUploading }"
      >
        <span class="material-symbols-outlined text-[16px]">
          {{ isUploading ? 'sync' : 'add_photo_alternate' }}
        </span>
        <span>{{ isUploading ? 'Đang tải lên...' : 'Chọn nhiều ảnh' }}</span>
        <input
          type="file"
          multiple
          accept="image/*"
          class="hidden"
          :disabled="isUploading"
          @change="handleFileChange"
        />
      </label>
    </div>

    <!-- Gallery Grid -->
    <div
      v-if="images.length > 0"
      class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3 p-3 bg-surface-container-lowest rounded-xl border border-outline-variant/40"
    >
      <div
        v-for="(img, idx) in images"
        :key="idx"
        class="relative group aspect-square rounded-lg border border-outline-variant/50 overflow-hidden bg-white p-1"
      >
        <img :src="img" class="w-full h-full object-contain" alt="" />
        
        <!-- Primary badge -->
        <span
          v-if="idx === 0"
          class="absolute top-1 left-1 bg-primary text-on-primary text-[9px] font-black px-1.5 py-0.5 rounded shadow"
        >
          ẢNH CHÍNH
        </span>

        <!-- Action Overlay -->
        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5">
          <button
            v-if="idx !== 0"
            type="button"
            @click="setAsPrimary(idx)"
            class="p-1.5 bg-surface text-on-surface rounded-md hover:bg-primary hover:text-on-primary transition-colors text-[10px] font-bold"
            title="Đặt làm ảnh chính"
          >
            <span class="material-symbols-outlined text-[14px]">star</span>
          </button>
          <button
            type="button"
            @click="removeImage(idx)"
            class="p-1.5 bg-error text-on-error rounded-md hover:opacity-90 transition-colors"
            title="Xóa ảnh"
          >
            <span class="material-symbols-outlined text-[14px]">delete</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Empty placeholder -->
    <div
      v-else
      class="border-2 border-dashed border-outline-variant/40 rounded-xl p-6 text-center bg-surface-container-lowest/50"
    >
      <span class="material-symbols-outlined text-outline text-[36px] mb-1">photo_library</span>
      <p class="text-xs text-on-surface-variant font-medium">Chưa có ảnh nào được tải lên cho sản phẩm này.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { adminService } from '../services/adminService'

interface Props {
  images: string[]
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'update:images', val: string[]): void
}>()

const isUploading = ref(false)

async function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement
  if (!target.files || target.files.length === 0) return

  const fileList = Array.from(target.files)
  isUploading.value = true

  try {
    const res = await adminService.uploadMultipleImages(fileList)
    if (res && res.urls && res.urls.length > 0) {
      const updated = [...props.images, ...res.urls]
      emit('update:images', updated)
    }
  } catch (err: unknown) {
    const errorMsg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    alert(errorMsg || 'Không thể upload ảnh, vui lòng thử lại!')
  } finally {
    isUploading.value = false
    target.value = ''
  }
}

function removeImage(idx: number) {
  const updated = [...props.images]
  updated.splice(idx, 1)
  emit('update:images', updated)
}

function setAsPrimary(idx: number) {
  if (idx <= 0 || idx >= props.images.length) return
  const updated = [...props.images]
  const target = updated.splice(idx, 1)[0]
  if (target) {
    updated.unshift(target)
    emit('update:images', updated)
  }
}
</script>
