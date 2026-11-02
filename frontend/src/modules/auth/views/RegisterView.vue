<template>
  <div class="grow flex items-center justify-center pt-24 pb-12 px-md min-h-screen bg-surface">
    <div class="max-w-[1100px] w-full grid grid-cols-1 lg:grid-cols-12 gap-lg items-center">
      <!-- Left Column: Branding -->
      <div class="hidden lg:flex flex-col lg:col-span-5 space-y-xl">
        <div>
          <h1 class="text-3xl md:text-5xl font-black text-on-surface mb-md leading-tight">
            Khám phá thế giới <br /><span class="text-primary">Công nghệ</span> đỉnh cao.
          </h1>
          <p class="text-sm md:text-base text-on-surface-variant max-w-[400px] leading-relaxed">
            Đăng ký tài khoản TechVina ngay hôm nay để nhận quyền lợi bảo hành độc quyền, tích điểm thành viên và ưu đãi trả góp 0%.
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

const email = ref('')
const password = ref('')
const name = ref('')
const phone = ref('')
const confirmPassword = ref('')
const error = ref('')

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
