import api from '@/core/api/axios'

export interface OrderItem {
  id: number
  order_id: number
  variant_id: number
  quantity: number
  price: string | number
  serial_number?: string | null
  variant?: {
    id: number
    sku: string
    price: string | number
    product?: {
      id: number
      name: string
      slug: string
      images?: { id: number; image_url: string }[]
    }
  }
}

export interface Order {
  id: number
  user_id: number
  address_id: number
  total_price: string | number
  shipping_fee: string | number
  ghn_order_code?: string
  expected_delivery_at?: string
  status: 'pending' | 'processing' | 'shipped' | 'delivered' | 'cancelled'
  payment_method?: string
  payment_status?: string
  payment_transaction_id?: string
  coupon_id?: number
  discount_amount?: string | number
  created_at: string
  updated_at: string
  user?: {
    id: number
    name: string
    email: string
    phone?: string
  }
  address?: {
    id: number
    name: string
    phone: string
    address: string
    city: string
  }
  coupon?: {
    id: number
    code: string
    type: 'fixed' | 'percentage'
    value: string | number
  }
  items?: OrderItem[]
}

export interface Coupon {
  id: number
  code: string
  type: 'fixed' | 'percentage'
  value: number | string
  min_order_value?: number | string | null
  max_discount?: number | string | null
  usage_limit?: number | null
  used_count: number
  expires_at?: string | null
  is_active: boolean | number
  created_at: string
}

export interface WarrantyClaim {
  id: number
  claim_code: string
  user_id?: number | null
  order_id?: number | null
  customer_name: string
  customer_phone: string
  customer_email?: string | null
  product_name: string
  serial_number?: string | null
  purchase_date?: string | null
  issue_description: string
  status: 'pending' | 'received' | 'inspecting' | 'completed' | 'rejected'
  admin_note?: string | null
  resolved_at?: string | null
  created_at: string
  updated_at: string
  user?: { id: number; name: string; email: string }
  order?: { id: number; ghn_order_code?: string; created_at: string }
}

export interface DashboardSummary {
  total_revenue: number
  all_time_revenue?: number
  total_orders: number
  all_time_orders?: number
  average_order_value?: number
  pending_orders: number
  processing_orders: number
  shipped_orders: number
  delivered_orders: number
  cancelled_orders: number
  total_customers: number
  total_products: number
  total_coupons: number
  pending_warranties: number
  total_warranties: number
}

export interface RevenuePoint {
  month: string
  revenue: string | number
  order_count: number
}

export interface DashboardFilterParams {
  period?: 'today' | '7days' | '30days' | 'this_month' | 'this_year' | 'all' | 'custom'
  from_date?: string | null
  to_date?: string | null
  order_status?: string | null
}

export const adminService = {
  // Dashboard
  async getDashboard(params?: DashboardFilterParams) {
    const res = await api.get('/admin/dashboard', { params })
    return res.data.data as {
      summary: DashboardSummary
      revenue_chart: RevenuePoint[]
      recent_orders: Order[]
      filters?: DashboardFilterParams
    }
  },

  // Orders
  async getOrders(params?: { page?: number; status?: string; search?: string; per_page?: number }) {
    const res = await api.get('/admin/orders', { params })
    return res.data.data
  },

  async getOrder(id: number | string) {
    const res = await api.get(`/admin/orders/${id}`)
    return res.data.data as Order
  },

  async updateOrderStatus(id: number | string, status: string) {
    const res = await api.patch(`/admin/orders/${id}/status`, { status })
    return res.data
  },

  async deleteOrder(id: number | string) {
    const res = await api.delete(`/admin/orders/${id}`)
    return res.data
  },

  async updateOrderSerials(orderId: number | string, items: { id: number; serial_number: string }[]) {
    const res = await api.patch(`/admin/orders/${orderId}/serials`, { items })
    return res.data
  },

  // Coupons
  async getCoupons(params?: { page?: number; status?: string; search?: string; per_page?: number }) {
    const res = await api.get('/admin/coupons', { params })
    return res.data.data
  },

  async createCoupon(data: Partial<Coupon>) {
    const res = await api.post('/admin/coupons', data)
    return res.data
  },

  async updateCoupon(id: number | string, data: Partial<Coupon>) {
    const res = await api.put(`/admin/coupons/${id}`, data)
    return res.data
  },

  async toggleCoupon(id: number | string) {
    const res = await api.patch(`/admin/coupons/${id}/toggle`)
    return res.data
  },

  async deleteCoupon(id: number | string) {
    const res = await api.delete(`/admin/coupons/${id}`)
    return res.data
  },

  // Warranties
  async getWarranties(params?: { page?: number; status?: string; search?: string; per_page?: number }) {
    const res = await api.get('/admin/warranties', { params })
    return res.data.data
  },

  async getWarranty(id: number | string) {
    const res = await api.get(`/admin/warranties/${id}`)
    return res.data.data as WarrantyClaim
  },

  async updateWarrantyStatus(id: number | string, status: string, admin_note?: string) {
    const res = await api.patch(`/admin/warranties/${id}/status`, { status, admin_note })
    return res.data
  },

  async deleteWarranty(id: number | string) {
    const res = await api.delete(`/admin/warranties/${id}`)
    return res.data
  },

  // Products
  async getProducts(params?: { page?: number; search?: string; category_id?: string | number | null; brand_id?: string | number | null; per_page?: number }) {
    const res = await api.get('/admin/products', { params })
    return res.data.data
  },

  async getProduct(id: number | string) {
    const res = await api.get(`/admin/products/${id}`)
    return res.data.data
  },

  async createProduct(data: Record<string, unknown>) {
    const res = await api.post('/admin/products', data)
    return res.data
  },

  async updateProduct(id: number | string, data: Record<string, unknown>) {
    const res = await api.put(`/admin/products/${id}`, data)
    return res.data
  },

  async deleteProduct(id: number | string) {
    const res = await api.delete(`/admin/products/${id}`)
    return res.data
  },

  async getCategories() {
    const res = await api.get('/categories')
    return res.data.data || res.data
  },

  async getBrands() {
    const res = await api.get('/brands')
    return res.data.data || res.data
  },

  // Upload image file
  async uploadImage(file: File) {
    const formData = new FormData()
    formData.append('image', file)
    const res = await api.post('/admin/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    return res.data as { status: boolean; message: string; url: string; path: string }
  },

  // Upload multiple images
  async uploadMultipleImages(files: FileList | File[]) {
    const formData = new FormData()
    Array.from(files).forEach((file) => {
      formData.append('images[]', file)
    })
    const res = await api.post('/admin/upload-multiple', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    return res.data as { status: boolean; message: string; urls: string[] }
  },

  // Admin Categories
  async getCategoriesAdmin(params?: { search?: string }) {
    const res = await api.get('/admin/categories', { params })
    return res.data.data
  },

  async createCategory(data: { name: string; description?: string }) {
    const res = await api.post('/admin/categories', data)
    return res.data
  },

  async updateCategory(id: number | string, data: { name: string; description?: string }) {
    const res = await api.put(`/admin/categories/${id}`, data)
    return res.data
  },

  async deleteCategory(id: number | string) {
    const res = await api.delete(`/admin/categories/${id}`)
    return res.data
  },

  // Admin Brands
  async getBrandsAdmin(params?: { search?: string }) {
    const res = await api.get('/admin/brands', { params })
    return res.data.data
  },

  async createBrand(data: { name: string; logo_url?: string; description?: string }) {
    const res = await api.post('/admin/brands', data)
    return res.data
  },

  async updateBrand(id: number | string, data: { name: string; logo_url?: string; description?: string }) {
    const res = await api.put(`/admin/brands/${id}`, data)
    return res.data
  },

  async deleteBrand(id: number | string) {
    const res = await api.delete(`/admin/brands/${id}`)
    return res.data
  },
}

