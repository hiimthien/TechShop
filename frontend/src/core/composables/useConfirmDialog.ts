import { ref } from 'vue'

export type DialogType = 'danger' | 'warning' | 'info' | 'success'

export interface ConfirmOptions {
  title: string
  message: string
  confirmText?: string
  cancelText?: string
  type?: DialogType
}

interface DialogState extends ConfirmOptions {
  isOpen: boolean
  resolve: (value: boolean) => void
}

const dialogState = ref<DialogState>({
  isOpen: false,
  title: '',
  message: '',
  confirmText: 'Đồng ý',
  cancelText: 'Hủy bỏ',
  type: 'danger',
  resolve: () => {},
})

export function useConfirmDialog() {
  function confirm(options: ConfirmOptions): Promise<boolean> {
    return new Promise((resolve) => {
      dialogState.value = {
        isOpen: true,
        title: options.title,
        message: options.message,
        confirmText: options.confirmText || 'Đồng ý',
        cancelText: options.cancelText || 'Hủy bỏ',
        type: options.type || 'danger',
        resolve,
      }
    })
  }

  function handleConfirm() {
    dialogState.value.isOpen = false
    dialogState.value.resolve(true)
  }

  function handleCancel() {
    dialogState.value.isOpen = false
    dialogState.value.resolve(false)
  }

  return {
    dialogState,
    confirm,
    handleConfirm,
    handleCancel,
  }
}

