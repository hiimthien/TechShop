/**
 * Core Composable: useFormatters
 * Reusable formatting utilities for prices, dates, and relative time in Vietnamese format.
 */
export function useFormatters() {
  function formatPrice(amount?: number | string | null): string {
    if (amount === undefined || amount === null) return '0 ₫'
    const num = typeof amount === 'string' ? parseFloat(amount) : amount
    if (isNaN(num)) return '0 ₫'
    return new Intl.NumberFormat('vi-VN').format(num) + ' ₫'
  }

  function formatDate(dateStr?: string | null, includeTime: boolean = true): string {
    if (!dateStr) return '--'
    try {
      const d = new Date(dateStr)
      if (isNaN(d.getTime())) return dateStr
      return d.toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        ...(includeTime ? { hour: '2-digit', minute: '2-digit' } : {}),
      })
    } catch {
      return dateStr
    }
  }

  function timeAgo(dateStr?: string | null): string {
    if (!dateStr) return ''
    try {
      const diffSec = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000)
      if (diffSec < 60) return 'Vừa xong'
      if (diffSec < 3600) return `${Math.floor(diffSec / 60)} phút trước`
      if (diffSec < 86400) return `${Math.floor(diffSec / 3600)} giờ trước`
      if (diffSec < 2592000) return `${Math.floor(diffSec / 86400)} ngày trước`
      return formatDate(dateStr, false)
    } catch {
      return ''
    }
  }

  return {
    formatPrice,
    formatDate,
    timeAgo,
  }
}

