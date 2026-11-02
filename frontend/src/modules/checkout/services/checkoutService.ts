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
}

export interface OrderResponse {
  status: boolean
  message: string
  order_id?: number
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
}

