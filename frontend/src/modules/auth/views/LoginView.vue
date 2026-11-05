<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-lg relative overflow-hidden bg-surface">
    <!-- Background Ambient Glow -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-secondary/5 rounded-full blur-3xl pointer-events-none"></div>

    <main class="w-full max-w-md z-10">
      <!-- Brand Logo -->
      <div class="flex flex-col items-center mb-8">
        <RouterLink to="/" class="text-3xl font-black text-primary mb-1 tracking-tight hover:opacity-90 transition-opacity">
          TechShop
        </RouterLink>
        <p class="text-xs text-on-surface-variant font-medium">Hệ thống phân phối đồ công nghệ hàng đầu</p>
      </div>

      <!-- Login Card -->
      <div
        class="bg-surface-container-lowest rounded-2xl p-6 md:p-8 border border-outline-variant/30 shadow-lg"
      >
        <div class="mb-6">
          <h1 class="text-xl font-black text-on-surface">Đăng nhập tài khoản</h1>
          <p class="text-xs text-on-surface-variant mt-1">Vui lòng điền thông tin để tiếp tục trải nghiệm.</p>
        </div>

        <!-- Error Alert -->
        <BaseAlert v-if="error" type="error" class="mb-4 text-xs">{{ error }}</BaseAlert>

        <form class="space-y-4" @submit.prevent="handleLogin">
          <BaseInput
            label="Địa chỉ Email"
            type="email"
            placeholder="name@example.com"
            icon="mail"
            v-model="email"
            required
          />

          <BaseInput
            label="Mật khẩu"
            type="password"
            placeholder="••••••••"
            icon="lock"
            v-model="password"
            required
          />

          <div class="flex items-center justify-between text-xs pt-1">
            <label class="flex items-center gap-2 cursor-pointer select-none text-on-surface-variant hover:text-on-surface">
              <input
                type="checkbox"
                v-model="rememberMe"
                class="rounded border-outline-variant text-primary focus:ring-primary w-4 h-4 cursor-pointer"
              />
              <span>Ghi nhớ đăng nhập</span>
            </label>
            <a href="#" class="text-primary font-bold hover:underline">Quên mật khẩu?</a>
          </div>

          <BaseButton
            type="submit"
            :loading="loading"
            class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-md hover:bg-primary-container transition-all active:scale-[0.98] mt-2"
          >
            Đăng nhập
          </BaseButton>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-outline-variant/30"></div>
          </div>
          <div class="relative flex justify-center text-[11px] font-semibold text-outline uppercase">
            <span class="px-3 bg-surface-container-lowest">Hoặc tiếp tục với</span>
          </div>
        </div>

        <!-- Social Login -->
        <button
          type="button"
          :disabled="googleLoading"
          @click="handleGoogleLogin"
          class="w-full flex items-center justify-center gap-3 py-3 px-4 border border-outline-variant/40 rounded-xl text-xs font-bold hover:bg-surface-container-low hover:border-outline-variant transition-all text-on-surface disabled:opacity-50 shadow-sm"
        >
          <div
            v-if="googleLoading"
            class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin"
          ></div>
          <svg v-else class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
            <path
              d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
              fill="#4285F4"
            />
            <path
              d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
              fill="#34A853"
            />
            <path
              d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"
              fill="#FBBC05"
            />
            <path
              d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
              fill="#EA4335"
            />
          </svg>
          <span>Tiếp tục với Google</span>
        </button>

        <!-- Quick Demo Login Section for Portfolio Reviewers -->
        <div class="mt-6 pt-5 border-t border-outline-variant/30 space-y-2">
          <div class="flex items-center justify-between text-[11px] font-medium text-on-surface-variant">
            <span>Tài khoản dùng thử</span>
            <span class="text-[10px] text-outline uppercase tracking-wider">Quick access</span>
          </div>

          <div class="grid grid-cols-2 gap-2">
            <button
              type="button"
              @click="quickLogin('admin@techshop.com', 'password')"
              class="py-2 px-3 bg-surface-container-low hover:bg-surface-container rounded-xl text-left border border-outline-variant/30 transition-colors text-xs"
            >
              <div class="font-bold text-on-surface">Admin Portal</div>
              <div class="text-[10px] text-outline mt-0.5">admin@techshop.com</div>
            </button>

            <button
              type="button"
              @click="quickLogin('customer@techshop.com', 'password')"
              class="py-2 px-3 bg-surface-container-low hover:bg-surface-container rounded-xl text-left border border-outline-variant/30 transition-colors text-xs"
            >
              <div class="font-bold text-on-surface">Khách hàng</div>
              <div class="text-[10px] text-outline mt-0.5">customer@techshop.com</div>
            </button>
          </div>
        </div>

        <!-- Signup Link -->
        <p class="mt-6 text-center text-xs text-on-surface-variant">
          Chưa có tài khoản?
          <RouterLink to="/register" class="text-primary font-bold hover:underline ml-1">
            Đăng ký ngay
          </RouterLink>
        </p>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import { useAuthStore } from '@/modules/auth/stores/authStore'
import { useCartStore } from '@/modules/cart/stores/cartStore'
import BaseInput from '@/core/components/BaseInput.vue'
import BaseButton from '@/core/components/BaseButton.vue'
import BaseAlert from '@/core/components/BaseAlert.vue'

const authStore = useAuthStore()
const cartStore = useCartStore()
const router = useRouter()
const route = useRoute()

const email = ref('')
const password = ref('')
const rememberMe = ref(true)
const error = ref('')
const loading = ref(false)
const googleLoading = ref(false)

const handleGoogleLogin = async () => {
  error.value = ''
  googleLoading.value = true
  try {
    await authStore.loginWithGooglePopup()
    await cartStore.fetchCart()
    const redirectPath = (route.query.redirect as string) || (authStore.user?.role === 'admin' ? '/admin/dashboard' : '/')
    await router.push(redirectPath)
  } catch (err: unknown) {
    if (err instanceof Error && err.message) {
      error.value = err.message
    }
  } finally {
    googleLoading.value = false
  }
}

const handleLogin = async () => {
  error.value = ''

  if (!email.value.trim() || !password.value) {
    error.value = 'Vui lòng nhập đầy đủ Email và Mật khẩu.'
    return
  }

  loading.value = true

  try {
    await authStore.login(email.value.trim(), password.value)
    // Sau khi đăng nhập thành công, tải giỏ hàng của user
    await cartStore.fetchCart()
    const redirectPath = (route.query.redirect as string) || (authStore.user?.role === 'admin' ? '/admin/dashboard' : '/')
    await router.push(redirectPath)
  } catch (e: unknown) {
    console.error('Đăng nhập thất bại:', e)
    const errData = (e as { response?: { data?: { message?: string } } })?.response?.data
    error.value =
      errData?.message || 'Email hoặc mật khẩu không chính xác. Vui lòng thử lại.'
  } finally {
    loading.value = false
  }
}

const quickLogin = async (demoEmail: string, demoPass: string) => {
  email.value = demoEmail
  password.value = demoPass
  await handleLogin()
}

onMounted(() => {
  if (route.query.error) {
    error.value = decodeURIComponent(route.query.error as string)
  }
})
</script>
