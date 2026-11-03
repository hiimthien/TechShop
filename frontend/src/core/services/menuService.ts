import api from '@/core/api/axios'

export interface MegaBrand {
  id: number
  name: string
  slug: string
  logo_url?: string | null
}

export interface MegaCategory {
  id: number
  name: string
  slug: string
  description?: string
  brands?: MegaBrand[]
}

export const menuService = {
  async getMegaMenu(): Promise<MegaCategory[]> {
    try {
      const res = await api.get('/mega-menu')
      return res.data?.data || []
    } catch (err) {
      console.error('Lỗi khi tải mega menu:', err)
      return []
    }
  },
}

