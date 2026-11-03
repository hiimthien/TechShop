<template>
  <div class="flex flex-col gap-5 sticky top-20">
    <!-- Main Large Image Card -->
    <div
      class="aspect-square bg-surface-container-lowest rounded-2xl p-6 md:p-8 flex items-center justify-center border border-outline-variant/40 relative overflow-hidden group shadow-xs"
    >
      <img
        :src="currentImage"
        :alt="productName"
        class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500"
      />
      <div class="absolute top-3 left-3 flex flex-row items-center gap-1.5 z-10">
        <span class="px-2.5 py-1 bg-secondary text-on-secondary text-[10px] font-black rounded-lg uppercase tracking-wider shadow-sm">
          Ưu đãi 8%
        </span>
        <span class="px-2.5 py-1 bg-primary text-on-primary text-[10px] font-bold rounded-lg uppercase tracking-wider shadow-sm">
          Trả góp 0%
        </span>
      </div>
    </div>

    <!-- Thumbnails Carousel -->
    <div v-if="images.length > 1" class="flex gap-2.5 overflow-x-auto pb-1 scrollbar-thin">
      <button
        v-for="(img, idx) in images"
        :key="idx"
        type="button"
        @click="$emit('select-image', img)"
        class="flex-shrink-0 w-16 h-16 md:w-20 md:h-20 bg-surface-container-lowest rounded-xl border-2 p-1.5 transition-all relative"
        :class="
          currentImage === img
            ? 'border-primary shadow-sm ring-1 ring-primary/20'
            : 'border-outline-variant/40 hover:border-primary/60'
        "
      >
        <img :src="img" :alt="`Góc ảnh ${idx + 1}`" class="w-full h-full object-contain" />
      </button>
    </div>

    <!-- Cam kết dịch vụ TechShop (Static content: v-once to skip VNode patching) -->
    <div v-once class="p-4 bg-surface-container-lowest rounded-2xl border border-outline-variant/30 space-y-3 text-xs">
      <h4 class="font-bold text-on-surface uppercase tracking-wider text-[11px] flex items-center gap-1.5 text-primary">
        <span class="material-symbols-outlined text-[16px]">verified_user</span>
        Thông tin và Cam kết sản phẩm
      </h4>
      <div class="grid grid-cols-1 gap-2.5 text-on-surface-variant">
        <div class="flex items-start gap-2.5">
          <span class="material-symbols-outlined text-primary text-[18px] shrink-0">inventory_2</span>
          <span>Máy mới 100% nguyên hộp, đầy đủ phụ kiện chính hãng từ nhà sản xuất.</span>
        </div>
        <div class="flex items-start gap-2.5">
          <span class="material-symbols-outlined text-primary text-[18px] shrink-0">published_with_changes</span>
          <span>Hư gì đổi nấy trong <strong>30 ngày đầu tiên</strong> nếu phát sinh lỗi phần cứng.</span>
        </div>
        <div class="flex items-start gap-2.5">
          <span class="material-symbols-outlined text-primary text-[18px] shrink-0">security</span>
          <span>Bảo hành <strong>24 tháng chính hãng</strong> tại các trung tâm bảo hành ủy quyền toàn quốc.</span>
        </div>
        <div class="flex items-start gap-2.5">
          <span class="material-symbols-outlined text-primary text-[18px] shrink-0">receipt_long</span>
          <span>Giá bán đã bao gồm thuế VAT, hỗ trợ xuất hóa đơn điện tử công ty nhanh chóng.</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  currentImage: string
  images: string[]
  productName: string
}

defineProps<Props>()
defineEmits<{
  (e: 'select-image', img: string): void
}>()
</script>

