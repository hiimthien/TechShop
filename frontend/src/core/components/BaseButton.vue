<template>
  <button
    :type="type"
    :disabled="loading"
    :class="[
      'w-full font-label-md text-label-md h-12 rounded-lg active:scale-[0.98] transition-all flex items-center justify-center gap-xs',
      variant === 'primary' ? 'bg-primary text-on-primary shadow-lg hover:bg-primary/90' : '',
      variant === 'outline' ? 'bg-transparent border border-outline-variant text-on-surface hover:bg-surface-container' : ''
    ]"
  >
    <span v-if="loading">Đang xử lý...</span>
    <slot v-else />
  </button>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  type?: 'submit' | 'button' | 'reset'
  loading?: boolean
  variant?: 'primary' | 'outline'
}>(), {
  type: 'button',
  loading: false,
  variant: 'primary'
})
</script>
