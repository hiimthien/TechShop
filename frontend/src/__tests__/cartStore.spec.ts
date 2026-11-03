import { describe, it, expect, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useCartStore } from '@/modules/cart/stores/cartStore'

describe('CartStore E-commerce Logic', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    localStorage.clear()
  })

  it('calculates totalItems, subtotal and shipping fee accurately', () => {
    const cart = useCartStore()

    // Add product under 500k -> shipping fee = 30k
    cart.items = [
      {
        id: 1,
        variant_id: 101,
        product_id: 1,
        name: 'Chuột Logitech G102',
        slug: 'chuot-logitech-g102',
        sku: 'LOGI-G102',
        price: 400000,
        quantity: 1,
        stock: 10,
        thumbnail: null,
      },
    ]

    expect(cart.totalItems).toBe(1)
    expect(cart.subtotal).toBe(400000)
    expect(cart.shippingFee).toBe(30000) // Under 500k
    expect(cart.totalAmount).toBe(430000)

    // Increase quantity to 2 -> subtotal = 800k >= 500k -> free shipping
    cart.items[0].quantity = 2
    expect(cart.totalItems).toBe(2)
    expect(cart.subtotal).toBe(800000)
    expect(cart.shippingFee).toBe(0) // Free shipping
    expect(cart.totalAmount).toBe(800000)
  })

  it('handles empty cart correctly', () => {
    const cart = useCartStore()
    cart.items = []

    expect(cart.totalItems).toBe(0)
    expect(cart.subtotal).toBe(0)
    expect(cart.shippingFee).toBe(0)
    expect(cart.totalAmount).toBe(0)
  })
})

