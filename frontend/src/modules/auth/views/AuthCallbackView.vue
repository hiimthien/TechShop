<template>
  <div class="min-h-[70vh] flex flex-col items-center justify-center p-4">
    <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-8 max-w-md w-full text-center shadow-sm space-y-5">
      <!-- Loading State -->
      <div v-if="isLoading" class="space-y-4">
        <div class="w-16 h-16 border-4 border-primary/20 border-t-primary rounded-full animate-spin mx-auto"></div>
        <h2 class="text-lg font-bold text-on-surface">Đang xử lý đăng nhập Google...</h2>
        <p class="text-xs text-on-surface-variant">Vui lòng chờ trong giây lát, hệ thống đang kết nối tài khoản của bạn.</p>
      </div>

      <!-- Error State -->
      <div v-else-if="errorMessage" class="space-y-4">
        <div class="w-16 h-16 bg-error/10 text-error rounded-full flex items-center justify-center mx-auto">
          <span class="material-symbols-outlined text-[32px]">error</span>
        </div>
        <h2 class="text-lg font-bold text-on-surface">Đăng nhập thất bại</h2>
        <p class="text-xs text-error font-medium">{{ errorMessage }}</p>
        <RouterLink
          to="/login"
          class="inline-block mt-4 px-6 py-2.5 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-hover transition-colors"
        >
          Quay lại trang Đăng nhập
        </RouterLink>
      </div>

      <!-- Success State -->
      <div v-else class="space-y-4">
        <div class="w-16 h-16 bg-emerald-500/10 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
          <span class="material-symbols-outlined text-[32px]">check_circle</span>
        </div>
        <h2 class="text-lg font-bold text-on-surface">Đăng nhập thành công!</h2>
        <p class="text-xs text-on-surface-variant">Chào mừng bạn đã quay trở lại. Đang chuyển hướng...</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const isLoading = ref(true)
const errorMessage = ref('')

onMounted(async () => {
  const token = route.query.token as string | undefined
  const error = route.query.error as string | undefined

  // Nếu mở bằng Popup, gửi message về cửa sổ cha và tự động đóng popup
  if (window.opener) {
    if (token) {
      try {
        window.opener.postMessage({ type: 'GOOGLE_AUTH_SUCCESS', token }, window.location.origin)
      } catch {
        window.opener.postMessage({ type: 'GOOGLE_AUTH_SUCCESS', token }, '*')
      }
      window.close()
      return
    }
    if (error) {
      const decodedErr = decodeURIComponent(error)
      try {
        window.opener.postMessage({ type: 'GOOGLE_AUTH_ERROR', error: decodedErr }, window.location.origin)
      } catch {
        window.opener.postMessage({ type: 'GOOGLE_AUTH_ERROR', error: decodedErr }, '*')
      }
      window.close()
      return
    }
  }

  // Fallback nếu mở bằng redirect toàn trang thông thường
  if (error) {
    isLoading.value = false
    errorMessage.value = decodeURIComponent(error)
    return
  }

  if (token) {
    try {
      await authStore.handleGoogleCallbackToken(token)
      isLoading.value = false
      setTimeout(() => {
        router.push('/')
      }, 800)
    } catch (err: unknown) {
      isLoading.value = false
      errorMessage.value = 'Không thể đồng bộ thông tin tài khoản: ' + ((err as Error)?.message || '')
    }
  } else {
    isLoading.value = false
    errorMessage.value = 'Không tìm thấy mã xác thực từ Google.'
  }
})
</script>
