import api from '@/core/api/axios'

export const authService = {
  async login(email: string, password: string) {
    const res = await api.post('/auth/login', { email, password })
    return res.data
  },

  async register(
    name: string,
    email: string,
    password: string,
    confirmPassword: string,
    phone?: string,
  ) {
    const response = await api.post('/auth/register', {
      name,
      email,
      password,
      password_confirmation: confirmPassword,
      phone,
    })
    return response.data
  },

  async logout() {
    const response = await api.post('/auth/logout')
    return response.data
  },

  async me() {
    const response = await api.get('/auth/me')
    return response.data
  },

  async getGoogleAuthUrl(): Promise<{ status: boolean; url: string }> {
    const response = await api.get('/auth/google/url')
    return response.data
  },

  redirectToGoogle() {
    const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
    window.location.href = `${apiUrl}/auth/google/redirect`
  },

  async googleTokenLogin(credential: string) {
    const response = await api.post('/auth/google/token', { credential })
    return response.data
  },
}
