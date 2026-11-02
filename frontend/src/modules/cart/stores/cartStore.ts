import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { CartItem } from '../types'
import { cartService } from '../services/cartService'

const STORAGE_KEY = 'techshop_cart'

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>([])
  const isLoading = ref<boolean>(false)

  // Khởi tạo từ LocalStorage nếu có
  const loadFromStorage = () => {
    try {
      const stored = localStorage.getItem(STORAGE_KEY)
      if (stored) {
        items.value = JSON.parse(stored)
      }
    } catch (e) {
      console.error('Không thể đọc giỏ hàng từ localStorage:', e)
    }
  }

  const saveToStorage = () => {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(items.value))
    } catch (e) {
      console.error('Không thể lưu giỏ hàng vào localStorage:', e)
    }
  }

  loadFromStorage()

  // Getters
  const totalItems = computed<number>(() => {
    return items.value.reduce((sum, item) => sum + item.quantity, 0)
  })

  const subtotal = computed<number>(() => {
    return items.value.reduce((sum, item) => sum + item.price * item.quantity, 0)
  })

  const shippingFee = computed<number>(() => {
    if (subtotal.value === 0) return 0
    return subtotal.value >= 500000 ? 0 : 30000
  })

  const totalAmount = computed<number>(() => {
    return subtotal.value + shippingFee.value
  })

  // Sync giỏ hàng từ Backend (nếu người dùng có token)
  const fetchCart = async () => {
    const token = localStorage.getItem('token')
    if (!token) return

    isLoading.value = true
    try {
      const res = await cartService.getCart()
      if (res && res.items) {
        // Map backend items sang CartItem
        items.value = res.items.map((it) => ({
          id: it.id,
          variant_id: it.variant.id,
          quantity: it.quantity,
          product_name: it.product.name,
          product_slug: it.product.slug,
          variant_sku: it.variant.sku,
          price: typeof it.variant.price === 'string' ? parseFloat(it.variant.price) : it.variant.price,
          thumbnail: null,
        }))
        saveToStorage()
      }
    } catch (error) {
      console.error('Lỗi khi tải giỏ hàng từ API:', error)
    } finally {
      isLoading.value = false
    }
  }

  // Thêm sản phẩm vào giỏ
  const addItem = async (payload: {
    variant_id: number
    quantity: number
    product_name: string
    product_slug: string
    variant_sku: string
    variant_option?: string
    price: number
    thumbnail?: string | null
    stock?: number
  }) => {
    const existingIndex = items.value.findIndex((it) => it.variant_id === payload.variant_id)

    if (existingIndex > -1) {
      const item = items.value[existingIndex]
      if (item) {
        item.quantity += payload.quantity
      }
    } else {
      items.value.push({
        id: Date.now(),
        variant_id: payload.variant_id,
        quantity: payload.quantity,
        product_name: payload.product_name,
        product_slug: payload.product_slug,
        variant_sku: payload.variant_sku,
        variant_option: payload.variant_option,
        price: payload.price,
        thumbnail: payload.thumbnail,
        stock: payload.stock,
      })
    }

    saveToStorage()

    // Nếu đã đăng nhập, đồng bộ lên Backend API
    const token = localStorage.getItem('token')
    if (token) {
      try {
        await cartService.addToCart(payload.variant_id, payload.quantity)
      } catch (e) {
        console.error('Không thể đồng bộ thêm vào giỏ lên API:', e)
      }
    }
  }

  // Cập nhật số lượng
  const updateQuantity = async (itemId: number, quantity: number) => {
    if (quantity <= 0) {
      await removeItem(itemId)
      return
    }

    const item = items.value.find((it) => it.id === itemId)
    if (item) {
      item.quantity = quantity
      saveToStorage()

      const token = localStorage.getItem('token')
      if (token) {
        try {
          await cartService.updateQuantity(itemId, quantity)
        } catch (e) {
          console.error('Không thể đồng bộ cập nhật số lượng lên API:', e)
        }
      }
    }
  }

  // Xóa sản phẩm khỏi giỏ
  const removeItem = async (itemId: number) => {
    items.value = items.value.filter((it) => it.id !== itemId)
    saveToStorage()

    const token = localStorage.getItem('token')
    if (token) {
      try {
        await cartService.removeItem(itemId)
      } catch (e) {
        console.error('Không thể đồng bộ xóa item lên API:', e)
      }
    }
  }

  // Xóa sạch giỏ hàng
  const clearCart = () => {
    items.value = []
    saveToStorage()
  }

  return {
    items,
    isLoading,
    totalItems,
    subtotal,
    shippingFee,
    totalAmount,
    fetchCart,
    addItem,
    updateQuantity,
    removeItem,
    clearCart,
  }
})

