import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { authService } from '@/modules/auth/services/authService'

export interface AuthUser {
  id: number
  name: string
  email: string
  role?: string
  phone?: string
  avatar?: string
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<AuthUser | null>(null)

  const isAuthenticated = computed(() => !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  async function login(email: string, password: string) {
    const res = await authService.login(email, password)
    user.value = res.user
    localStorage.setItem('token', res.token)
  }

  async function logout() {
    await authService.logout()
    user.value = null
    localStorage.removeItem('token')
  }

  async function fetchUser() {
    const res = await authService.me()
    user.value = res
  }

  async function register(
    name: string,
    email: string,
    password: string,
    confirmPassword: string,
    phone?: string,
  ) {
    const res = await authService.register(name, email, password, confirmPassword, phone)
    user.value = res.user
    localStorage.setItem('token', res.token)
  }

  function loginWithGoogle() {
    authService.redirectToGoogle()
  }

  function loginWithGooglePopup(): Promise<AuthUser | null> {
    return new Promise((resolve, reject) => {
      const width = 500
      const height = 650
      const left = window.screenX + Math.max(0, (window.outerWidth - width) / 2)
      const top = window.screenY + Math.max(0, (window.outerHeight - height) / 2)
      const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
      const url = `${apiUrl}/auth/google/redirect`

      const popup = window.open(
        url,
        'GoogleSignInPopup',
        `width=${width},height=${height},left=${left},top=${top},status=no,toolbar=no,menubar=no,location=yes`
      )

      if (!popup) {
        // Fallback sang chuyển trang toàn màn hình nếu trình duyệt chặn popup
        loginWithGoogle()
        return
      }

      let checkTimer: number | null = null

      const cleanup = () => {
        window.removeEventListener('message', handleMessage)
        if (checkTimer !== null) {
          window.clearInterval(checkTimer)
          checkTimer = null
        }
      }

      const handleMessage = async (event: MessageEvent) => {
        if (event.origin !== window.location.origin) return

        if (event.data?.type === 'GOOGLE_AUTH_SUCCESS') {
          cleanup()
          try {
            await handleGoogleCallbackToken(event.data.token)
            resolve(user.value)
          } catch (err) {
            reject(err)
          }
        } else if (event.data?.type === 'GOOGLE_AUTH_ERROR') {
          cleanup()
          reject(new Error(event.data.error || 'Đăng nhập Google thất bại'))
        }
      }

      window.addEventListener('message', handleMessage)

      checkTimer = window.setInterval(() => {
        if (popup.closed) {
          cleanup()
        }
      }, 500)
    })
  }

  async function handleGoogleCallbackToken(token: string) {
    localStorage.setItem('token', token)
    await fetchUser()
  }

  async function googleTokenLogin(credential: string) {
    const res = await authService.googleTokenLogin(credential)
    user.value = res.user
    localStorage.setItem('token', res.token)
  }

  return {
    user,
    isAuthenticated,
    isAdmin,
    login,
    logout,
    fetchUser,
    register,
    loginWithGoogle,
    loginWithGooglePopup,
    handleGoogleCallbackToken,
    googleTokenLogin,
  }
})
