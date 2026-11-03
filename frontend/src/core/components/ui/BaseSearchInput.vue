<template>
  <div class="relative flex items-center w-full">
    <span class="material-symbols-outlined absolute left-3 text-[18px] text-on-surface-variant pointer-events-none select-none">
      search
    </span>
    <input
      type="text"
      :value="modelValue"
      @input="handleInput"
      :placeholder="placeholder"
      class="w-full pl-9 pr-8 py-2 text-xs md:text-sm bg-surface-container-lowest border border-outline-variant/60 rounded-xl text-on-surface placeholder:text-on-surface-variant/70 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all shadow-2xs"
    />
    <button
      v-if="modelValue"
      type="button"
      @click="clear"
      class="absolute right-2.5 text-on-surface-variant hover:text-on-surface p-0.5 rounded-full hover:bg-surface-container-low transition-colors"
      aria-label="Xóa nội dung tìm kiếm"
    >
      <span class="material-symbols-outlined text-[16px] block">close</span>
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

interface Props {
  modelValue: string
  placeholder?: string
  debounceMs?: number
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Tìm kiếm...',
  debounceMs: 300,
})

const emit = defineEmits<{
  (e: 'update:modelValue', val: string): void
  (e: 'search', val: string): void
}>()

let timer: ReturnType<typeof setTimeout> | null = null

function handleInput(event: Event) {
  const target = event.target as HTMLInputElement
  const value = target.value
  emit('update:modelValue', value)

  if (timer) clearTimeout(timer)
  timer = setTimeout(() => {
    emit('search', value)
  }, props.debounceMs)
}

function clear() {
  emit('update:modelValue', '')
  emit('search', '')
}
</script>

