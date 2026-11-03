import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import ProductCard from '@/modules/products/components/ProductCard.vue'

describe('ProductCard Component', () => {
  const dummyProduct = {
    id: 1,
    name: 'iPhone 15 Pro Max 256GB',
    slug: 'iphone-15-pro-max-256gb',
    brand: 'Apple',
    brand_id: 2,
    starting_price: 29990000,
    original_price: 34990000,
    discount_percent: 14,
    thumbnail: 'https://example.com/iphone.jpg',
  }

  it('renders product title and brand', () => {
    const wrapper = mount(ProductCard, {
      props: {
        product: dummyProduct,
      },
      global: {
        stubs: {
          RouterLink: {
            template: '<a><slot /></a>',
          },
        },
      },
    })

    expect(wrapper.text()).toContain('iPhone 15 Pro Max 256GB')
    expect(wrapper.text()).toContain('Apple')
    expect(wrapper.text()).toContain('Trả góp 0%')
    expect(wrapper.text()).toContain('-14%')
  })

  it('renders horizontal badge container', () => {
    const wrapper = mount(ProductCard, {
      props: {
        product: dummyProduct,
      },
      global: {
        stubs: {
          RouterLink: {
            template: '<a><slot /></a>',
          },
        },
      },
    })

    const badgeContainer = wrapper.find('.flex.flex-row')
    expect(badgeContainer.exists()).toBe(true)
    expect(badgeContainer.classes()).toContain('items-center')
    expect(badgeContainer.classes()).toContain('flex-wrap')
  })
})

