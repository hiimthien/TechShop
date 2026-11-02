export interface ProductVariant {
  id: number
  sku: string
  price: number | string
  stock: number
  sale_price?: number | string | null
  sale_starts_at?: string | null
  sale_ends_at?: string | null
  options?: string[]
  image_url?: string | null
}

export interface Product {
  id: number
  name: string
  slug: string
  brand?: string
  category?: string
  thumbnail: string | null
  starting_price: number | string | null
  images?: string[]
  description?: string
  variants?: ProductVariant[]
}

export interface Category {
  id: number
  name: string
  slug: string
}

export interface Brand {
  id: number
  name: string
  slug: string
  logo_url?: string | null
}

export interface PaginationMeta {
  total: number
  per_page: number
  current_page: number
  last_page: number
}

export interface ProductListResponse {
  data: Product[]
  pagination: PaginationMeta
}

export interface ProductFilterParams {
  category_id?: number | null
  brand_ids?: number[]
  brand_id?: number | null
  search?: string
  min_price?: number | null
  max_price?: number | null
  in_stock?: boolean
  ram?: string | null
  storage?: string | null
  connection?: string | null
  screen_feature?: string | null
  sort?: string
  page?: number
  per_page?: number
}
