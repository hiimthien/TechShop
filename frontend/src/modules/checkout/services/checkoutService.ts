import api from '@/core/api/axios'

export interface UserAddress {
  id: number
  name: string
  phone: string
  address: string
  city: string
  ward_code?: string | null
  district_id?: number | null
  province_id?: number | null
  is_default?: boolean
}

export interface CreateAddressPayload {
  name: string
  phone: string
  address: string
  city: string
  ward_code?: string | null
  district_id?: number | null
  province_id?: number | null
  is_default?: boolean
}

export interface CreateOrderPayload {
  address_id: number
  shipping_fee: number
  coupon_code?: string
  payment_method?: string
}

export interface OrderResponse {
  status: boolean
  message: string
  order_id?: number
}

export interface VnpayUrlResponse {
  status: boolean
  message: string
  data: {
    payment_url: string
    order_id: number
    total_price: number
  }
}

export interface VnpayReturnResponse {
  status: boolean
  message: string
  data?: {
    order_id: number
    order_code: string
    transaction_no?: string
    amount?: number
    response_code?: string
  }
  errors?: {
    response_code?: string
  }
}

export const checkoutService = {
  async getAddresses(): Promise<UserAddress[]> {
    const res = await api.get<{ status: boolean; data: UserAddress[] }>('/addresses')
    return res.data.data
  },

  async createAddress(payload: CreateAddressPayload): Promise<{ status: boolean; message: string }> {
    const res = await api.post('/addresses', payload)
    return res.data
  },

  async createOrder(payload: CreateOrderPayload): Promise<OrderResponse> {
    const res = await api.post<OrderResponse>('/orders', payload)
    return res.data
  },

  async createVnpayPaymentUrl(orderId: number): Promise<VnpayUrlResponse['data']> {
    const res = await api.post<VnpayUrlResponse>('/payment/vnpay/create-url', { order_id: orderId })
    return res.data.data
  },

  async checkCoupon(code: string, orderTotal: number): Promise<{
    status: boolean
    message: string
    data?: {
      coupon: { id: number; code: string; type: string; value: number }
      discount_amount: number
      final_total: number
    }
  }> {
    const res = await api.post('/coupons/check', { code, order_total: orderTotal })
    return res.data
  },

  async handleVnpayReturn(params: Record<string, string>): Promise<VnpayReturnResponse> {
    const res = await api.get<VnpayReturnResponse>('/payment/vnpay/return', { params })
    return res.data
  },
}
