<template>
  <Teleport to="body">
    <div
      class="fixed top-5 right-5 z-[9999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none px-4 sm:px-0"
    >
      <TransitionGroup
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 translate-x-8 scale-95"
        enter-to-class="opacity-100 translate-x-0 scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-x-0 scale-100"
        leave-to-class="opacity-0 translate-x-8 scale-95"
      >
        <div
          v-for="t in toasts"
          :key="t.id"
          class="pointer-events-auto p-4 rounded-2xl border shadow-xl flex items-start gap-3 backdrop-blur-md transition-all"
          :class="getToastClass(t.type)"
        >
          <!-- Icon -->
          <div
            class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
            :class="getIconContainerClass(t.type)"
          >
            <span class="material-symbols-outlined text-[18px]">
              {{ getIcon(t.type) }}
            </span>
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0 pt-0.5">
            <h4 v-if="t.title" class="text-xs font-black uppercase tracking-wider mb-0.5">
              {{ t.title }}
            </h4>
            <p class="text-xs font-semibold leading-relaxed">
              {{ t.message }}
            </p>
          </div>

          <!-- Close button -->
          <button
            type="button"
            @click="remove(t.id)"
            class="w-6 h-6 rounded-lg inline-flex items-center justify-center opacity-70 hover:opacity-100 transition-opacity shrink-0"
            title="Đóng"
          >
            <span class="material-symbols-outlined text-[16px]">close</span>
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { useToast, type ToastType } from '../../composables/useToast'

const { toasts, remove } = useToast()

function getIcon(type: ToastType): string {
  switch (type) {
    case 'success':
      return 'check_circle'
    case 'error':
      return 'error'
    case 'warning':
      return 'warning'
    case 'info':
      return 'info'
  }
}

function getToastClass(type: ToastType): string {
  switch (type) {
    case 'success':
      return 'bg-surface-container-lowest/95 border-emerald-500/30 text-on-surface shadow-emerald-500/10'
    case 'error':
      return 'bg-surface-container-lowest/95 border-rose-500/30 text-on-surface shadow-rose-500/10'
    case 'warning':
      return 'bg-surface-container-lowest/95 border-amber-500/30 text-on-surface shadow-amber-500/10'
    case 'info':
      return 'bg-surface-container-lowest/95 border-primary/30 text-on-surface shadow-primary/10'
  }
}

function getIconContainerClass(type: ToastType): string {
  switch (type) {
    case 'success':
      return 'bg-emerald-500/10 text-emerald-600'
    case 'error':
      return 'bg-rose-500/10 text-rose-600'
    case 'warning':
      return 'bg-amber-500/10 text-amber-600'
    case 'info':
      return 'bg-primary/10 text-primary'
  }
}
</script>

