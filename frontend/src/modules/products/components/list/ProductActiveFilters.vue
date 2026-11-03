<template>
  <div v-if="hasActiveFilterTags" class="flex flex-wrap items-center gap-2 mb-6">
    <span class="text-xs text-outline font-medium">Đang lọc:</span>

    <!-- Category tag -->
    <span
      v-if="categoryName"
      class="inline-flex items-center gap-1 px-2.5 py-1 bg-primary/10 text-primary text-xs font-bold rounded-lg"
    >
      {{ categoryName }}
      <button @click="$emit('remove-category')" class="hover:text-error cursor-pointer">×</button>
    </span>

    <!-- Brand tags -->
    <span
      v-for="brand in activeBrands"
      :key="brand.id"
      class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-container-high text-on-surface text-xs font-semibold rounded-lg"
    >
      {{ brand.name }}
      <button @click="$emit('remove-brand', brand.id)" class="hover:text-error cursor-pointer">×</button>
    </span>

    <!-- RAM tag -->
    <span
      v-if="ram"
      class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-container-high text-on-surface text-xs font-semibold rounded-lg"
    >
      RAM: {{ ram }}
      <button @click="$emit('remove-ram')" class="hover:text-error cursor-pointer">×</button>
    </span>

    <!-- Storage tag -->
    <span
      v-if="storage"
      class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-container-high text-on-surface text-xs font-semibold rounded-lg"
    >
      SSD: {{ storage }}
      <button @click="$emit('remove-storage')" class="hover:text-error cursor-pointer">×</button>
    </span>

    <!-- Connection tag -->
    <span
      v-if="connection"
      class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-container-high text-on-surface text-xs font-semibold rounded-lg"
    >
      Kết nối: {{ connection }}
      <button @click="$emit('remove-connection')" class="hover:text-error cursor-pointer">×</button>
    </span>

    <!-- Screen Feature tag -->
    <span
      v-if="screenFeature"
      class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-container-high text-on-surface text-xs font-semibold rounded-lg"
    >
      Màn hình: {{ screenFeature }}
      <button @click="$emit('remove-screen-feature')" class="hover:text-error cursor-pointer">×</button>
    </span>

    <!-- In Stock tag -->
    <span
      v-if="inStock"
      class="inline-flex items-center gap-1 px-2.5 py-1 bg-secondary/10 text-secondary text-xs font-semibold rounded-lg"
    >
      Còn hàng
      <button @click="$emit('remove-in-stock')" class="hover:text-error cursor-pointer">×</button>
    </span>

    <!-- Reset All Button -->
    <button
      @click="$emit('reset-all')"
      class="text-xs text-primary hover:underline font-bold ml-2 cursor-pointer"
    >
      Xóa tất cả
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface BrandItem {
  id: number
  name: string
}

interface Props {
  categoryName?: string | null
  activeBrands: BrandItem[]
  ram?: string | null
  storage?: string | null
  connection?: string | null
  screenFeature?: string | null
  inStock?: boolean
}

const props = defineProps<Props>()
defineEmits<{
  (e: 'remove-category'): void
  (e: 'remove-brand', id: number): void
  (e: 'remove-ram'): void
  (e: 'remove-storage'): void
  (e: 'remove-connection'): void
  (e: 'remove-screen-feature'): void
  (e: 'remove-in-stock'): void
  (e: 'reset-all'): void
}>()

const hasActiveFilterTags = computed(() => {
  return (
    !!props.categoryName ||
    props.activeBrands.length > 0 ||
    !!props.ram ||
    !!props.storage ||
    !!props.connection ||
    !!props.screenFeature ||
    props.inStock
  )
})
</script>

