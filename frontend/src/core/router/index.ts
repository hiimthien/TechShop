import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/modules/auth/stores/authStore'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Customer Storefront Routes
    {
      path: '/',
      name: 'Home',
      component: () => import('@/modules/home/views/HomeView.vue'),
      meta: { title: 'Đỉnh Cao Thiết Bị Công Nghệ' },
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/modules/auth/views/LoginView.vue'),
      meta: { title: 'Đăng Nhập', hideHeaderFooter: true },
    },
    {
      path: '/register',
      name: 'RegisterView',
      component: () => import('@/modules/auth/views/RegisterView.vue'),
      meta: { title: 'Đăng Ký Tài Khoản', hideHeaderFooter: true },
    },
    {
      path: '/auth/callback',
      name: 'AuthCallbackView',
      component: () => import('@/modules/auth/views/AuthCallbackView.vue'),
      meta: { title: 'Đang Xác Thực', hideHeaderFooter: true },
    },
    {
      path: '/products',
      name: 'ProductListView',
      component: () => import('@/modules/products/views/ProductListView.vue'),
      meta: { title: 'Sản Phẩm Công Nghệ' },
    },
    {
      path: '/products/:slug',
      name: 'ProductDetailView',
      component: () => import('@/modules/products/views/ProductDetailView.vue'),
      meta: { title: 'Chi Tiết Sản Phẩm' },
    },
    {
      path: '/cart',
      name: 'CartView',
      component: () => import('@/modules/cart/views/CartView.vue'),
      meta: { title: 'Giỏ Hàng' },
    },
    {
      path: '/checkout',
      name: 'CheckoutView',
      component: () => import('@/modules/checkout/views/CheckoutView.vue'),
      meta: { title: 'Thanh Toán Đơn Hàng', requiresAuth: true },
    },
    {
      path: '/checkout/vnpay-return',
      name: 'VnpayReturnView',
      component: () => import('@/modules/checkout/views/VnpayReturnView.vue'),
      meta: { title: 'Kết Quả Thanh Toán VNPay' },
    },
    {
      path: '/account',
      name: 'AccountView',
      component: () => import('@/modules/account/views/AccountView.vue'),
      meta: { title: 'Tài Khoản & Đơn Hàng', requiresAuth: true },
    },
    {
      path: '/about',
      name: 'AboutView',
      component: () => import('@/modules/pages/views/AboutView.vue'),
      meta: { title: 'Giới Thiệu TechShop' },
    },
    {
      path: '/warranty-policy',
      name: 'WarrantyPolicyView',
      component: () => import('@/modules/pages/views/WarrantyPolicyView.vue'),
      meta: { title: 'Chính Sách & Tra Cứu Bảo Hành' },
    },
    {
      path: '/shipping-policy',
      name: 'ShippingPolicyView',
      component: () => import('@/modules/pages/views/ShippingPolicyView.vue'),
      meta: { title: 'Chính Sách Vận Chuyển' },
    },
    {
      path: '/contact',
      name: 'ContactView',
      component: () => import('@/modules/pages/views/ContactView.vue'),
      meta: { title: 'Liên Hệ Hỗ Trợ' },
    },

    // Admin CMS Routes
    {
      path: '/admin',
      component: () => import('@/modules/admin/layouts/AdminLayout.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
      children: [
        {
          path: '',
          redirect: '/admin/dashboard',
        },
        {
          path: 'dashboard',
          name: 'AdminDashboard',
          component: () => import('@/modules/admin/views/AdminDashboardView.vue'),
          meta: { title: 'Dashboard Quản Trị' },
        },
        {
          path: 'products',
          name: 'AdminProducts',
          component: () => import('@/modules/admin/views/AdminProductListView.vue'),
          meta: { title: 'Quản Lý Sản Phẩm' },
        },
        {
          path: 'products/create',
          name: 'AdminProductCreate',
          component: () => import('@/modules/admin/views/AdminProductFormView.vue'),
          meta: { title: 'Thêm Sản Phẩm Mới' },
        },
        {
          path: 'products/:id/edit',
          name: 'AdminProductEdit',
          component: () => import('@/modules/admin/views/AdminProductFormView.vue'),
          meta: { title: 'Chỉnh Sửa Sản Phẩm' },
        },
        {
          path: 'categories',
          name: 'AdminCategories',
          component: () => import('@/modules/admin/views/AdminCategoryListView.vue'),
          meta: { title: 'Quản Lý Danh Mục' },
        },
        {
          path: 'brands',
          name: 'AdminBrands',
          component: () => import('@/modules/admin/views/AdminBrandListView.vue'),
          meta: { title: 'Quản Lý Thương Hiệu' },
        },
        {
          path: 'orders',
          name: 'AdminOrders',
          component: () => import('@/modules/admin/views/AdminOrderListView.vue'),
          meta: { title: 'Quản Lý Đơn Hàng' },
        },
        {
          path: 'coupons',
          name: 'AdminCoupons',
          component: () => import('@/modules/admin/views/AdminCouponListView.vue'),
          meta: { title: 'Quản Lý Khuyến Mãi' },
        },
        {
          path: 'warranties',
          name: 'AdminWarranties',
          component: () => import('@/modules/admin/views/AdminWarrantyListView.vue'),
          meta: { title: 'Quản Lý Bảo Hành RMA' },
        },
      ],
    },
  ],
})

// Dynamic Document Title
router.afterEach((to) => {
  const baseTitle = 'TechShop - Thiết Bị Công Nghệ Chính Hãng'
  if (to.meta?.title) {
    document.title = `${to.meta.title} | TechShop`
  } else {
    document.title = baseTitle
  }
})

// Navigation Guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  const token = localStorage.getItem('token')

  // Wait for auth initialization if token exists but user not loaded yet
  if (token && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch {
      localStorage.removeItem('token')
    }
  }

  // Requires Auth Guard
  if (to.matched.some((record) => record.meta.requiresAuth)) {
    if (!authStore.isAuthenticated) {
      return next({
        path: '/login',
        query: { redirect: to.fullPath },
      })
    }
  }

  // Requires Admin Role Guard
  if (to.matched.some((record) => record.meta.requiresAdmin)) {
    if (!authStore.isAdmin) {
      return next({ path: '/' })
    }
  }

  // Prevent authenticated user from visiting login/register
  if ((to.name === 'login' || to.name === 'RegisterView') && authStore.isAuthenticated) {
    return next({ path: '/' })
  }

  next()
})

export default router
