import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  withCredentials: false
})

// Interceptor untuk menambahkan token ke setiap request
api.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  error => {
    return Promise.reject(error)
  }
)

// Interceptor untuk handle response errors
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401 || error.response?.status === 403) {
      const isDeactivated = error.response?.data?.message && error.response.data.message.includes('dinonaktifkan');
      if (isDeactivated) {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        alert(error.response.data.message)
        window.location.href = '/login'
      } else if (error.response?.status === 401) {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        window.location.href = '/login'
      }
    }
    return Promise.reject(error)
  }
)

export const getStorageUrl = (path) => {
  if (!path) return null
  const base = API_URL.replace('/api', '')
  return `${base}/storage/${path}`
}

export default api
