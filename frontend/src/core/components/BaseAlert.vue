<template>
  <div
    v-if="show"
    :class="[
      'p-md rounded-lg flex items-start gap-sm border text-body-sm transition-all',
      type === 'error' ? 'bg-error/10 border-error/20 text-error' : '',
      type === 'success' ? 'bg-primary/10 border-primary/20 text-primary' : '',
      type === 'warning' ? 'bg-orange-500/10 border-orange-500/20 text-orange-600' : '',
      type === 'info' ? 'bg-blue-500/10 border-blue-500/20 text-blue-600' : ''
    ]"
  >
    <span class="material-symbols-outlined text-[20px] shrink-0 mt-[2px]">{{ iconName }}</span>
    <div class="grow">
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(defineProps<{
  type?: 'error' | 'success' | 'warning' | 'info'
  show?: boolean
}>(), {
  type: 'info',
  show: true
})

const iconName = computed(() => {
  switch (props.type) {
    case 'error': return 'error'
    case 'success': return 'check_circle'
    case 'warning': return 'warning'
    case 'info':
    default: return 'info'
  }
})
</script>
