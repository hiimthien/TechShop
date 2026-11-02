<template>
  <div class="space-y-xs">
    <label class="text-label-md font-label-md text-on-surface-variant block px-base">{{
      label
    }}</label>
    <div class="relative">
      <span
        v-if="icon"
        class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline"
        >{{ icon }}</span
      >
      <input
        :type="type"
        :placeholder="placeholder"
        v-model="model"
        class="w-full bg-surface-container-low border border-outline-variant rounded-lg py-md pl-11 pr-md focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-body-md font-body-md placeholder:text-outline-variant"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{ label: string; type?: string; placeholder: string; icon?: string }>()

const model = defineModel<string>()
</script>
