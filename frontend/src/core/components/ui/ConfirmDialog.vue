<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="dialogState.isOpen"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
        role="dialog"
        aria-modal="true"
        @click.self="handleCancel"
        @keydown.esc="handleCancel"
        @keydown.enter="handleConfirm"
      >
        <Transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="opacity-0 scale-90 translate-y-3"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-90 translate-y-3"
        >
          <div
            v-if="dialogState.isOpen"
            class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-2xl max-w-md w-full p-6 text-center space-y-5 my-auto"
          >
            <!-- Animated Icon Bubble (SweetAlert style) -->
            <div
              class="w-16 h-16 rounded-full mx-auto flex items-center justify-center shadow-lg transition-transform"
              :class="getIconBubbleClass(dialogState.type)"
            >
              <span class="material-symbols-outlined text-[36px]" style="font-variation-settings: 'FILL' 1;">
                {{ getIcon(dialogState.type) }}
              </span>
            </div>

            <!-- Title & Message -->
            <div class="space-y-2">
              <h3 class="text-lg font-black text-on-surface">
                {{ dialogState.title }}
              </h3>
              <p class="text-xs text-on-surface-variant leading-relaxed px-2">
                {{ dialogState.message }}
              </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-center gap-3 pt-2">
              <button
                type="button"
                @click="handleCancel"
                class="px-5 py-2.5 rounded-xl border border-outline-variant/50 text-on-surface-variant hover:bg-surface-container text-xs font-bold transition-all"
              >
                {{ dialogState.cancelText }}
              </button>
              <button
                type="button"
                @click="handleConfirm"
                class="px-6 py-2.5 rounded-xl text-white text-xs font-black uppercase tracking-wider transition-all shadow-md active:scale-95"
                :class="getConfirmBtnClass(dialogState.type)"
              >
                {{ dialogState.confirmText }}
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { watch, onMounted, onUnmounted } from 'vue'
import { useConfirmDialog, type DialogType } from '../../composables/useConfirmDialog'

const { dialogState, handleConfirm, handleCancel } = useConfirmDialog()

function getIcon(type?: DialogType): string {
  switch (type) {
    case 'danger':
      return 'warning'
    case 'warning':
      return 'priority_high'
    case 'info':
      return 'info'
    case 'success':
      return 'check_circle'
    default:
      return 'help'
  }
}

function getIconBubbleClass(type?: DialogType): string {
  switch (type) {
    case 'danger':
      return 'bg-rose-500/15 text-rose-600 ring-8 ring-rose-500/10'
    case 'warning':
      return 'bg-amber-500/15 text-amber-600 ring-8 ring-amber-500/10'
    case 'info':
      return 'bg-primary/15 text-primary ring-8 ring-primary/10'
    case 'success':
      return 'bg-emerald-500/15 text-emerald-600 ring-8 ring-emerald-500/10'
    default:
      return 'bg-surface-container text-outline'
  }
}

function getConfirmBtnClass(type?: DialogType): string {
  switch (type) {
    case 'danger':
      return 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/30'
    case 'warning':
      return 'bg-amber-600 hover:bg-amber-700 shadow-amber-600/30'
    case 'info':
      return 'bg-primary hover:bg-primary-hover shadow-primary/30'
    case 'success':
      return 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/30'
    default:
      return 'bg-primary hover:bg-primary-hover'
  }
}

function onKeydown(e: KeyboardEvent) {
  if (!dialogState.value.isOpen) return
  if (e.key === 'Escape') {
    handleCancel()
  } else if (e.key === 'Enter') {
    handleConfirm()
  }
}

watch(
  () => dialogState.value.isOpen,
  (val) => {
    if (val) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  }
)

onMounted(() => {
  window.addEventListener('keydown', onKeydown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', onKeydown)
  document.body.style.overflow = ''
})
</script>

