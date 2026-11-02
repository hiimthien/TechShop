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
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/modules/auth/views/LoginView.vue'),
    },
    {
      path: '/register',
      name: 'RegisterView',
      component: () => import('@/modules/auth/views/RegisterView.vue'),
    },
    {
      path: '/products',
      name: 'ProductListView',
      component: () => import('@/modules/products/views/ProductListView.vue'),
    },
    {
      path: '/products/:slug',
      name: 'ProductDetailView',
      component: () => import('@/modules/products/views/ProductDetailView.vue'),
    },
    {
      path: '/cart',
      name: 'CartView',
      component: () => import('@/modules/cart/views/CartView.vue'),
    },
    {
      path: '/checkout',
      name: 'CheckoutView',
      component: () => import('@/modules/checkout/views/CheckoutView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/about',
      name: 'AboutView',
      component: () => import('@/modules/pages/views/AboutView.vue'),
    },
    {
      path: '/warranty-policy',
      name: 'WarrantyPolicyView',
      component: () => import('@/modules/pages/views/WarrantyPolicyView.vue'),
    },
    {
      path: '/shipping-policy',
      name: 'ShippingPolicyView',
      component: () => import('@/modules/pages/views/ShippingPolicyView.vue'),
    },
    {
      path: '/contact',
      name: 'ContactView',
      component: () => import('@/modules/pages/views/ContactView.vue'),
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
        },
        {
          path: 'products',
          name: 'AdminProducts',
          component: () => import('@/modules/admin/views/AdminProductListView.vue'),
        },
        {
          path: 'categories',
          name: 'AdminCategories',
          component: () => import('@/modules/admin/views/AdminCategoryListView.vue'),
        },
        {
          path: 'brands',
          name: 'AdminBrands',
          component: () => import('@/modules/admin/views/AdminBrandListView.vue'),
        },
        {
          path: 'orders',
          name: 'AdminOrders',
          component: () => import('@/modules/admin/views/AdminOrderListView.vue'),
        },
        {
          path: 'coupons',
          name: 'AdminCoupons',
          component: () => import('@/modules/admin/views/AdminCouponListView.vue'),
        },
        {
          path: 'warranties',
          name: 'AdminWarranties',
          component: () => import('@/modules/admin/views/AdminWarrantyListView.vue'),
        },
      ],
    },
  ],
})

router.beforeEach(async (to, from, next) => {
  const requiresAuth = to.matched.some((record) => record.meta.requiresAuth)
  const requiresAdmin = to.matched.some((record) => record.meta.requiresAdmin)
  const token = localStorage.getItem('token')

  if (requiresAuth && !token) {
    return next({ path: '/login', query: { redirect: to.fullPath } })
  }

  if (requiresAdmin) {
    const authStore = useAuthStore()
    if (!authStore.user && token) {
      try {
        await authStore.fetchUser()
      } catch {
        localStorage.removeItem('token')
        return next('/login')
      }
    }

    if (authStore.user?.role !== 'admin') {
      alert('Tài khoản của bạn không có quyền truy cập trang quản trị CMS!')
      return next('/')
    }
  }

  next()
})

export default router
