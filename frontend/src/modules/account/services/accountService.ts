import api from '@/core/api/axios'

export interface UserProfile {
  id: number
  name: string
  email: string
  phone?: string | null
  avatar?: string | null
  role?: string
}

export interface OrderItemDetail {
  id: number
  quantity: number
  price: number
  line_total: number
  serial_number?: string | null
  variant?: {
    id?: number
    sku?: string
  }
  product?: {
    id?: number
    name: string
    slug?: string
    image?: string
  }
}

export interface CustomerOrder {
  id: number
  order_code: string
  shipping_fee: number
  total_price: number
  discount_amount: number
  final_total: number
  status: 'pending' | 'processing' | 'shipping' | 'delivered' | 'cancelled'
  payment_method?: string
  payment_status?: 'unpaid' | 'pending' | 'paid' | 'failed' | 'refunded'
  payment_transaction_id?: string | null
  created_at: string
  created_at_raw?: string
  address?: {
    id: number
    name: string
    phone: string
    address: string
    city?: string
  }
  coupon?: {
    id: number
    code: string
    type: 'fixed' | 'percentage'
    value: number
  }
  items: OrderItemDetail[]
}

export interface CouponItem {
  id: number
  code: string
  type: 'fixed' | 'percentage'
  value: number
  min_order_value?: number | null
  max_discount?: number | null
  usage_limit?: number | null
  used_count: number
  expires_at?: string | null
  is_active: boolean
}

export interface WarrantyClaimItem {
  id: number
  claim_code: string
  customer_name: string
  customer_phone: string
  customer_email?: string | null
  product_name: string
  serial_number?: string | null
  issue_description: string
  status: 'pending' | 'received' | 'inspecting' | 'completed' | 'rejected'
  admin_note?: string | null
  created_at: string
}

export const accountService = {
  async getProfile(): Promise<UserProfile> {
    const res = await api.get('/auth/me')
    return res.data
  },

  async updateProfile(payload: { name: string; phone?: string; avatar?: string }): Promise<{
    status: boolean
    message: string
    user: UserProfile
  }> {
    const res = await api.put('/auth/profile', payload)
    return res.data
  },

  async uploadAvatar(file: File): Promise<{
    status: boolean
    message: string
    avatar_url: string
    user: UserProfile
  }> {
    const formData = new FormData()
    formData.append('avatar', file)
    const res = await api.post('/auth/avatar', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    return res.data
  },

  async updatePassword(payload: {
    current_password?: string
    new_password: string
    new_password_confirmation: string
  }): Promise<{ status: boolean; message: string }> {
    const res = await api.put('/auth/password', payload)
    return res.data
  },

  async getMyOrders(): Promise<CustomerOrder[]> {
    const res = await api.get('/orders')
    return res.data?.data || []
  },

  async getOrderDetail(orderId: number | string): Promise<CustomerOrder> {
    const res = await api.get(`/orders/${orderId}`)
    return res.data?.data
  },

  async cancelOrder(orderId: number | string): Promise<{ status: boolean; message: string }> {
    const res = await api.post(`/orders/${orderId}/cancel`)
    return res.data
  },

  async getAvailableCoupons(): Promise<CouponItem[]> {
    const res = await api.get('/coupons/available')
    return res.data?.data || []
  },

  async getMyWarranties(): Promise<WarrantyClaimItem[]> {
    const res = await api.get('/warranty-claims/me')
    return res.data?.data || []
  },

  async createWarranty(payload: {
    customer_name: string
    customer_phone: string
    customer_email?: string
    product_name: string
    serial_number?: string
    issue_description: string
    address?: string
  }): Promise<{ status: boolean; message: string; data: WarrantyClaimItem }> {
    const res = await api.post('/warranty-claims', payload)
    return res.data
  },
}

