import api from '@/core/api/axios'
import type {
  Product,
  Category,
  Brand,
  ProductListResponse,
  ProductFilterParams,
} from '../types'

export const productService = {
  async getProducts(params?: ProductFilterParams): Promise<ProductListResponse> {
    const res = await api.get<ProductListResponse>('/products', { params })
    return res.data
  },

  async getProduct(slug: string): Promise<Product> {
    const res = await api.get<Product>(`/products/${slug}`)
    return res.data
  },

  async getCategories(): Promise<Category[]> {
    const res = await api.get<Category[]>('/categories')
    return res.data
  },

  async getBrands(): Promise<Brand[]> {
    const res = await api.get<Brand[]>('/brands')
    return res.data
  },
}
