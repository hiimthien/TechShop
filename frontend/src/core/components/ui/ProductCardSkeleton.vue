<template>
  <div class="bg-surface-container-lowest rounded-xl overflow-hidden border border-outline-variant/30 flex flex-col justify-between animate-pulse">
    <!-- Ảnh sản phẩm giả lập -->
    <div class="aspect-square bg-surface-container-low relative p-4 flex items-center justify-center">
      <div class="w-16 h-16 rounded-2xl bg-surface-container-high/60 flex items-center justify-center">
        <span class="material-symbols-outlined text-outline/30 text-3xl">image</span>
      </div>
      <div class="absolute top-3 left-3 w-16 h-4 bg-surface-container-high rounded-full"></div>
    </div>

    <!-- Thông tin chi tiết -->
    <div class="p-4 flex flex-col gap-2.5 flex-grow justify-between">
      <div class="space-y-2">
        <div class="flex items-center justify-between">
          <div class="h-3 bg-surface-container rounded w-10"></div>
          <div class="h-3 bg-surface-container rounded w-16"></div>
        </div>

        <div class="h-3.5 bg-surface-container rounded w-full"></div>
        <div class="h-3.5 bg-surface-container rounded w-2/3"></div>
      </div>

      <div class="pt-2 border-t border-outline-variant/20 flex items-center justify-between">
        <div class="h-5 bg-primary/20 rounded w-28"></div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Stateless reusable skeleton placeholder
</script>

