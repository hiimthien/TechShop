import api from '@/core/api/axios'
import type { BackendCartResponse } from '../types'

export const cartService = {
  async getCart(): Promise<BackendCartResponse> {
    const res = await api.get<BackendCartResponse>('/cart')
    return res.data
  },

  async addToCart(variantId: number, quantity: number): Promise<{ message: string; item_id: number }> {
    const res = await api.post('/cart/items', {
      variant_id: variantId,
      quantity,
    })
    return res.data
  },

  async updateQuantity(itemId: number, quantity: number): Promise<{ message: string }> {
    const res = await api.put(`/cart/items/${itemId}`, { quantity })
    return res.data
  },

  async removeItem(itemId: number): Promise<{ message: string }> {
    const res = await api.delete(`/cart/items/${itemId}`)
    return res.data
  },
}

