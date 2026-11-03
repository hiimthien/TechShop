<template>
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-outline-variant/30">
    <div class="space-y-1">
      <div class="flex items-center gap-2.5">
        <h1 class="text-xl md:text-2xl font-black text-on-surface tracking-tight">
          {{ title }}
        </h1>
        <span
          v-if="badgeText"
          class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary border border-primary/20"
        >
          {{ badgeText }}
        </span>
      </div>
      <p v-if="description" class="text-xs md:text-sm text-on-surface-variant font-normal">
        {{ description }}
      </p>
    </div>

    <!-- Right Actions -->
    <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
      <slot name="actions">
        <button
          v-if="actionButtonText"
          type="button"
          @click="$emit('action')"
          class="px-4 py-2.5 bg-primary hover:bg-primary-hover text-on-primary rounded-xl font-bold text-xs md:text-sm transition-all shadow-xs flex items-center gap-2 active:scale-95"
        >
          <span v-if="actionButtonIcon" class="material-symbols-outlined text-[18px]">
            {{ actionButtonIcon }}
          </span>
          <span>{{ actionButtonText }}</span>
        </button>
      </slot>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  title: string
  description?: string
  badgeText?: string | number
  actionButtonText?: string
  actionButtonIcon?: string
}

defineProps<Props>()
defineEmits<{
  (e: 'action'): void
}>()
</script>

