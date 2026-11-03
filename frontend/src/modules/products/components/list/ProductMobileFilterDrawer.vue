<template>
  <Teleport to="body">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex justify-end lg:hidden"
    >
      <!-- Backdrop -->
      <div
        class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity"
        @click="$emit('close')"
      ></div>

      <!-- Drawer Content -->
      <div class="relative w-full max-w-sm bg-surface-container-lowest h-full shadow-2xl flex flex-col z-10 animate-in slide-in-from-right duration-200">
        <div class="flex items-center justify-between p-4 border-b border-outline-variant/30">
          <h3 class="font-black text-sm text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">tune</span>
            Bộ lọc sản phẩm
          </h3>
          <button
            type="button"
            @click="$emit('close')"
            class="text-outline hover:text-on-surface p-1 rounded-lg hover:bg-surface-container-high transition-colors"
          >
            <span class="material-symbols-outlined text-xl">close</span>
          </button>
        </div>

        <div class="flex-grow overflow-y-auto p-4">
          <slot></slot>
        </div>

        <div class="p-4 border-t border-outline-variant/30 bg-surface-container-low flex gap-2">
          <button
            type="button"
            @click="$emit('reset')"
            class="w-1/2 py-2.5 rounded-xl border border-outline-variant/60 font-bold text-xs text-on-surface hover:bg-surface-container-high transition-colors"
          >
            Đặt lại
          </button>
          <button
            type="button"
            @click="$emit('apply')"
            class="w-1/2 py-2.5 rounded-xl bg-primary text-on-primary font-bold text-xs hover:bg-primary-hover transition-colors shadow-xs"
          >
            Áp dụng
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
interface Props {
  show: boolean
}

defineProps<Props>()
defineEmits<{
  (e: 'close'): void
  (e: 'apply'): void
  (e: 'reset'): void
}>()
</script>

