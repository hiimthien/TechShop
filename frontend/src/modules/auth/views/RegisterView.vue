<template>
  <div class="grow flex items-center justify-center py-10 px-md min-h-screen bg-surface">
    <div class="max-w-[1100px] w-full grid grid-cols-1 lg:grid-cols-12 gap-lg items-center">
      <!-- Left Column: Branding -->
      <div class="hidden lg:flex flex-col lg:col-span-5 space-y-xl">
        <div>
          <RouterLink to="/" class="text-3xl font-black text-primary mb-3 inline-block tracking-tight hover:opacity-90 transition-opacity">
            TechShop
          </RouterLink>
          <h1 class="text-3xl md:text-5xl font-black text-on-surface mb-md leading-tight">
            Khám phá thế giới <br /><span class="text-primary">Công nghệ</span> đỉnh cao.
          </h1>
          <p class="text-sm md:text-base text-on-surface-variant max-w-[400px] leading-relaxed">
            Đăng ký tài khoản TechShop ngay hôm nay để nhận quyền lợi bảo hành độc quyền, tích điểm thành viên và ưu đãi trả góp 0%.
          </p>
        </div>
        <div
          class="relative w-full aspect-square rounded-2xl overflow-hidden shadow-2xl"
        >
          <img
            alt="Premium Laptop Design"
            class="w-full h-full object-cover"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAtNSxXuMuhO9iWn0iKaIqRvyoMMIarZ8xeU1EBfDe3OJf-i-zJS-GoXLRxddseSTLcEuN-UCnpW9XQeW3v30jf3APpQHnPBscA8qVcKQUigCdSRKHxx2D8_2nmy5B7Xzb2-NxIFGlgv9wPfJcWxsNlZoVS56XkhnOw1VczFa5CuSVslkcDH5EKUXf9osOdbFWGgkRYPHF8xmc5sIm4JrdErtQwxveeO3XxOVMmBtx9yD1WmEE_vat-LsRlQpTtbygBFCnO29tcOAY"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
          <div class="absolute bottom-6 left-6 text-white">
            <div class="flex items-center gap-1 mb-1">
              <span class="material-symbols-outlined text-primary-fixed text-lg">verified</span>
              <span class="text-xs font-bold uppercase tracking-wider">Hệ thống phân phối chính hãng</span>
            </div>
            <div class="text-lg font-black">
              Apple, Asus, Dell, Samsung Digital Flagship
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Register Form -->
      <div class="lg:col-span-7 flex justify-center lg:justify-end">
        <div
          class="w-full max-w-[520px] bg-surface-container-lowest rounded-2xl shadow-xl border border-outline-variant/30 p-6 md:p-8 relative overflow-hidden"
        >
          <div class="mb-6">
            <h2 class="text-2xl font-black text-on-surface mb-1">
              Đăng ký tài khoản
            </h2>
            <p class="text-xs text-on-surface-variant">
              Vui lòng nhập thông tin của bạn để tạo tài khoản mới.
            </p>
          </div>

          <form class="space-y-4" @submit.prevent="handleRegister">
            <BaseAlert v-if="error" type="error" class="mb-3 text-xs">{{ error }}</BaseAlert>

            <BaseInput
              label="Họ và tên"
              type="text"
              placeholder="Nguyễn Văn A"
              icon="person"
              v-model="name"
              required
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <BaseInput
                label="Email"
                type="email"
                placeholder="name@example.com"
                icon="mail"
                v-model="email"
                required
              />
              <BaseInput
                label="Số điện thoại"
                type="tel"
                placeholder="0901234567"
                icon="phone"
                v-model="phone"
                required
              />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <BaseInput
                label="Mật khẩu"
                type="password"
                placeholder="••••••••"
                icon="lock"
                v-model="password"
                required
              />
              <BaseInput
                label="Nhập lại mật khẩu"
                type="password"
                placeholder="••••••••"
                icon="lock"
                v-model="confirmPassword"
                required
              />
            </div>

            <BaseButton
              type="submit"
              :loading="loading"
              class="w-full py-3.5 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-md hover:bg-primary-container transition-all active:scale-[0.98] mt-2"
            >
              Tạo tài khoản
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
            @click="handleGoogleRegister"
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
            <span>Đăng ký nhanh với Google</span>
          </button>

          <!-- Login Link -->
          <p class="mt-6 text-center text-xs text-on-surface-variant">
            Đã có tài khoản?
            <RouterLink to="/login" class="text-primary font-bold hover:underline ml-1">
              Đăng nhập ngay
            </RouterLink>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '@/modules/auth/stores/authStore'
import { useCartStore } from '@/modules/cart/stores/cartStore'
import BaseInput from '@/core/components/BaseInput.vue'
import BaseButton from '@/core/components/BaseButton.vue'
import BaseAlert from '@/core/components/BaseAlert.vue'

const authStore = useAuthStore()
const cartStore = useCartStore()
const router = useRouter()
const loading = ref(false)
const googleLoading = ref(false)

const email = ref('')
const password = ref('')
const name = ref('')
const phone = ref('')
const confirmPassword = ref('')
const error = ref('')

const handleGoogleRegister = async () => {
  error.value = ''
  googleLoading.value = true
  try {
    await authStore.loginWithGooglePopup()
    await cartStore.fetchCart()
    await router.push('/')
  } catch (err: unknown) {
    if (err instanceof Error && err.message) {
      error.value = err.message
    }
  } finally {
    googleLoading.value = false
  }
}

const handleRegister = async () => {
  error.value = ''

  if (password.value !== confirmPassword.value) {
    error.value = 'Mật khẩu nhập lại không khớp.'
    return
  }

  if (password.value.length < 6) {
    error.value = 'Mật khẩu phải có ít nhất 6 ký tự.'
    return
  }

  loading.value = true

  try {
    await authStore.register(
      name.value.trim(),
      email.value.trim(),
      password.value,
      confirmPassword.value,
      phone.value.trim(),
    )
    // Sau khi đăng ký thành công, user đã có token, đồng bộ cart và về trang chủ
    await cartStore.fetchCart()
    await router.push('/')
  } catch (e: unknown) {
    console.error('Đăng ký thất bại:', e)
    const errData = (e as { response?: { data?: { message?: string } } })?.response?.data
    error.value =
      errData?.message || 'Đăng ký thất bại. Vui lòng kiểm tra lại thông tin.'
  } finally {
    loading.value = false
  }
}
</script>
