import api from '@/core/api/axios'

export interface AppNotification {
  id: string
  type: string
  title: string
  message: string
  category: 'order' | 'warranty' | 'coupon' | 'system'
  link: string
  icon: string
  reference_id: number | string | null
  is_read: boolean
  read_at: string | null
  created_at: string
}

export interface NotificationResponse {
  status: boolean
  data: AppNotification[]
  unread_count: number
  pagination: {
    total: number
    per_page: number
    current_page: number
    last_page: number
  }
}

export const notificationService = {
  async getNotifications(params?: { category?: string; page?: number; per_page?: number }): Promise<NotificationResponse> {
    const res = await api.get<NotificationResponse>('/notifications', { params })
    return res.data
  },

  async markAsRead(id: string): Promise<{ status: boolean; unread_count: number }> {
    const res = await api.patch<{ status: boolean; unread_count: number }>(`/notifications/${id}/read`)
    return res.data
  },

  async markAllAsRead(): Promise<{ status: boolean; unread_count: number }> {
    const res = await api.patch<{ status: boolean; unread_count: number }>('/notifications/read-all')
    return res.data
  },
}

