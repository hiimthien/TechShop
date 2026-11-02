import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { authService } from '@/modules/auth/services/authService'

export interface AuthUser {
  id: number
  name: string
  email: string
  role?: string
  phone?: string
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

  return { user, isAuthenticated, isAdmin, login, logout, fetchUser, register }
})
