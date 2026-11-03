<template>
  <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/30 space-y-4 shadow-xs">
    <h2 class="text-base font-black text-on-surface uppercase tracking-wider flex items-center gap-2">
      <span class="w-1.5 h-5 bg-secondary rounded-full"></span>
      Thông số kỹ thuật
    </h2>

    <div class="overflow-hidden rounded-xl border border-outline-variant/30 text-xs">
      <table class="w-full text-left border-collapse">
        <tbody>
          <tr
            v-for="(val, label, idx) in specs"
            :key="label"
            class="border-b border-outline-variant/20 last:border-b-0"
            :class="idx % 2 === 0 ? 'bg-surface-container-low/50' : 'bg-surface-container-lowest'"
          >
            <td class="p-2.5 text-on-surface-variant font-medium w-2/5 border-r border-outline-variant/20">
              {{ label }}
            </td>
            <td class="p-2.5 text-on-surface font-semibold">
              {{ val }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  specs: Record<string, string>
}

defineProps<Props>()
</script>

