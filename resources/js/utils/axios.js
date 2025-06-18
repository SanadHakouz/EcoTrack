import axios from 'axios'
import { useAuthStore } from '../stores/auth'

// Set base URL
axios.defaults.baseURL = '/api'

// Set default headers
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

// CSRF token setup
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
}

// Request interceptor to add auth token
axios.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore()

    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`
    }

    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor to handle auth errors
axios.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    const authStore = useAuthStore()

    // Handle 401 Unauthorized
    if (error.response?.status === 401) {
      authStore.clearAuth()

      // Redirect to login if not already there
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }

    return Promise.reject(error)
  }
)

export default axios