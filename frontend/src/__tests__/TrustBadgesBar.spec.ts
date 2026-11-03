import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import TrustBadgesBar from '@/modules/home/components/TrustBadgesBar.vue'

describe('TrustBadgesBar Component', () => {
  it('renders all 4 value proposition badges', () => {
    const wrapper = mount(TrustBadgesBar)

    expect(wrapper.text()).toContain('100% Chính Hãng')
    expect(wrapper.text()).toContain('Giao Siêu Tốc 2H')
    expect(wrapper.text()).toContain('Lỗi Là Đổi 30 Ngày')
    expect(wrapper.text()).toContain('Trả Góp 0% Dễ Dàng')
  })
})

