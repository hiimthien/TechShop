import { ref } from 'vue'

export type ToastType = 'success' | 'error' | 'warning' | 'info'

export interface ToastItem {
  id: string
  type: ToastType
  message: string
  title?: string
  duration?: number
}

const toasts = ref<ToastItem[]>([])

export function useToast() {
  function show(type: ToastType, message: string, title?: string, duration: number = 3500) {
    const id = `toast_${Date.now()}_${Math.random().toString(36).substring(2, 7)}`
    const item: ToastItem = {
      id,
      type,
      message,
      title,
      duration,
    }

    toasts.value.push(item)

    if (duration > 0) {
      setTimeout(() => {
        remove(id)
      }, duration)
    }

    return id
  }

  function remove(id: string) {
    const idx = toasts.value.findIndex((t) => t.id === id)
    if (idx !== -1) {
      toasts.value.splice(idx, 1)
    }
  }

  function success(message: string, title?: string, duration?: number) {
    return show('success', message, title, duration)
  }

  function error(message: string, title?: string, duration?: number) {
    return show('error', message, title, duration)
  }

  function warning(message: string, title?: string, duration?: number) {
    return show('warning', message, title, duration)
  }

  function info(message: string, title?: string, duration?: number) {
    return show('info', message, title, duration)
  }

  return {
    toasts,
    show,
    remove,
    success,
    error,
    warning,
    info,
  }
}

