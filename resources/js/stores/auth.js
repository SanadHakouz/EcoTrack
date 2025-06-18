import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth_token'),
    isLoading: false,
    error: null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
    isAdmin: (state) => state.user?.role === 'admin',
    isModerator: (state) => ['admin', 'moderator'].includes(state.user?.role),
    userName: (state) => state.user?.name || '',
    userRole: (state) => state.user?.role || 'user',
    userLanguage: (state) => state.user?.language || 'en'
  },

  actions: {
    // Get role-based dashboard route
    getDashboardRoute() {
      const role = this.user?.role
      switch (role) {
        case 'admin':
          return { name: 'admin-dashboard' }
        case 'moderator':
          return { name: 'moderator-dashboard' }
        case 'user':
        default:
          return { name: 'user-dashboard' }
      }
    },

    // Initialize auth state
    async initAuth() {
      if (this.token && !this.user) {
        try {
          await this.fetchUser()
        } catch (error) {
          this.clearAuth()
        }
      }
    },

    // Register new user
    async register(userData) {
      this.isLoading = true
      this.error = null

      try {
        const response = await axios.post('/auth/register', {
          name: userData.name,
          username: userData.username,
          email: userData.email,
          password: userData.password,
          password_confirmation: userData.password_confirmation,
          language: userData.language || 'en'
        })

        if (response.data.success) {
          this.setAuth(response.data.user, response.data.token)
          return { success: true, dashboardRoute: this.getDashboardRoute() }
        }

        throw new Error(response.data.message || 'Registration failed')

      } catch (error) {
        this.error = error.response?.data?.message || error.message || 'Registration failed'
        return { success: false, error: this.error }
      } finally {
        this.isLoading = false
      }
    },

    // Login user
    async login(credentials) {
      this.isLoading = true
      this.error = null

      try {
        const response = await axios.post('/auth/login', {
          login: credentials.login, // email or username
          password: credentials.password
        })

        if (response.data.success) {
          this.setAuth(response.data.user, response.data.token)
          return { success: true, dashboardRoute: this.getDashboardRoute() }
        }

        throw new Error(response.data.message || 'Login failed')

      } catch (error) {
        this.error = error.response?.data?.message || error.message || 'Login failed'
        return { success: false, error: this.error }
      } finally {
        this.isLoading = false
      }
    },

    // Logout user
    async logout() {
      this.isLoading = true

      try {
        if (this.token) {
          await axios.post('/auth/logout')
        }
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.clearAuth()
        this.isLoading = false
      }
    },

    // Fetch current user data
    async fetchUser() {
      if (!this.token) return

      try {
        const response = await axios.get('/auth/me')

        if (response.data.success) {
          this.user = response.data.user
        } else {
          throw new Error('Failed to fetch user')
        }
      } catch (error) {
        console.error('Fetch user error:', error)
        this.clearAuth()
        throw error
      }
    },

    // Update user data
    updateUser(userData) {
      if (this.user) {
        this.user = { ...this.user, ...userData }
      }
    },

    // Set authentication data
    setAuth(user, token) {
      this.user = user
      this.token = token
      localStorage.setItem('auth_token', token)

      // Set axios default header
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    },

    // Clear authentication data
    clearAuth() {
      this.user = null
      this.token = null
      this.error = null
      localStorage.removeItem('auth_token')

      // Remove axios default header
      delete axios.defaults.headers.common['Authorization']
    },

    // Clear error
    clearError() {
      this.error = null
    }
  }
})
