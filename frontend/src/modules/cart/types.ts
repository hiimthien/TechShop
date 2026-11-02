export interface CartItem {
  id: number
  variant_id: number
  quantity: number
  product_name: string
  product_slug: string
  variant_sku: string
  variant_option?: string
  price: number
  thumbnail?: string | null
  stock?: number
}

export interface BackendCartItem {
  id: number
  quantity: number
  variant: {
    id: number
    sku: string
    price: number | string
  }
  product: {
    id: number
    name: string
    slug: string
  }
  subtotal: number
}

export interface BackendCartResponse {
  items: BackendCartItem[]
  total: number
}

