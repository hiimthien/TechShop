<template>
  <div class="py-12 px-4 text-center flex flex-col items-center justify-center max-w-sm mx-auto">
    <div
      v-if="icon"
      class="w-16 h-16 rounded-3xl bg-surface-container flex items-center justify-center text-on-surface-variant/70 mb-4 border border-outline-variant/30 shadow-xs"
    >
      <span class="material-symbols-outlined text-[32px]">{{ icon }}</span>
    </div>

    <h4 class="text-sm font-bold text-on-surface mb-1">
      {{ title }}
    </h4>

    <p v-if="description" class="text-xs text-on-surface-variant leading-relaxed mb-5">
      {{ description }}
    </p>

    <slot name="action">
      <button
        v-if="actionText"
        type="button"
        @click="$emit('action')"
        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-on-primary hover:bg-primary-hover text-xs font-bold transition-all shadow-xs"
      >
        <span v-if="actionIcon" class="material-symbols-outlined text-[16px]">{{ actionIcon }}</span>
        {{ actionText }}
      </button>
    </slot>
  </div>
</template>

<script setup lang="ts">
interface Props {
  icon?: string
  title: string
  description?: string
  actionText?: string
  actionIcon?: string
}

withDefaults(defineProps<Props>(), {
  icon: 'inbox',
  description: '',
  actionText: '',
  actionIcon: '',
})

defineEmits<{
  (e: 'action'): void
}>()
</script>

