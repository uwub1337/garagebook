import { defineStore } from 'pinia'
import http from '../api/http'

const TOKEN_KEY = 'garagebook_token'
const DEMO_CREDENTIALS = {
  email: 'demo@garagebook.test',
  password: 'password123',
}
const PREVIEW_MODE = import.meta.env.VITE_PREVIEW_MODE !== 'false'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem(TOKEN_KEY),
  }),

  getters: {
    isLoggedIn: (state) => Boolean(state.token),
  },

  actions: {
    setAuth({ user, token }) {
      this.user = user
      this.token = token
      localStorage.setItem(TOKEN_KEY, token)
    },

    async login(credentials) {
      const { data } = await http.post('/auth/login', credentials)
      this.setAuth(data)
    },

    async ensurePreviewLogin() {
      if (!PREVIEW_MODE || this.token) return

      try {
        await this.login(DEMO_CREDENTIALS)
      } catch {
        // 預覽資料尚未完成 seed 時，保留一般登入頁作為 fallback。
      }
    },

    async register(payload) {
      const { data } = await http.post('/auth/register', payload)
      this.setAuth(data)
    },

    async fetchMe() {
      if (!this.token) return
      try {
        const { data } = await http.get('/auth/me')
        this.user = data
      } catch {
        this.clear()
      }
    },

    async logout() {
      try {
        await http.post('/auth/logout')
      } catch {
        // 即使登出 API 失敗，也清除本機狀態
      }
      this.clear()
    },

    clear() {
      this.user = null
      this.token = null
      localStorage.removeItem(TOKEN_KEY)
    },
  },
})
