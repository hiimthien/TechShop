import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import FlashSaleSection from '@/modules/home/components/FlashSaleSection.vue'

describe('FlashSaleSection Component', () => {
  const dummyProducts = [
    {
      id: 1,
      name: 'Bàn phím Keychron K3 Pro',
      slug: 'keychron-k3-pro',
      brand: 'Keychron',
      starting_price: 2290000,
      discount_percent: 15,
      thumbnail: 'https://example.com/keychron.jpg',
    },
  ]

  it('renders flash sale header and sales progress', () => {
    const wrapper = mount(FlashSaleSection, {
      props: {
        products: dummyProducts,
        isLoading: false,
      },
      global: {
        stubs: {
          RouterLink: {
            template: '<a><slot /></a>',
          },
        },
      },
    })

    expect(wrapper.text()).toContain('Flash Sale Giá Sốc')
    expect(wrapper.text()).toContain('Bàn phím Keychron K3 Pro')
    expect(wrapper.text()).toContain('-15%')
    expect(wrapper.text()).toContain('Đã bán')
  })
})

