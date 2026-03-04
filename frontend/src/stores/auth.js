import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)
  const isAuthenticated = computed(() => !!token.value)
  const isSuperAdmin = computed(() => user.value?.role === 'superadmin')
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isLapangan = computed(() => user.value?.role === 'lapangan')
  const isPenggilingan = computed(() => user.value?.role === 'penggilingan')
  const namaPenggilingan = computed(() => user.value?.nama_penggilingan || null)
  
  // Access permission (can view)
  const canAccessPetani = computed(() => ['admin', 'lapangan', 'superadmin'].includes(user.value?.role))
  const canAccessPenggilingan = computed(() => ['admin', 'penggilingan', 'superadmin'].includes(user.value?.role))
  const canAccessUsers = computed(() => user.value?.role === 'superadmin')
  
  // CRUD permission (can create, update, delete)
  const canManagePetani = computed(() => ['admin', 'lapangan', 'superadmin'].includes(user.value?.role))
  const canManagePenggilingan = computed(() => ['admin', 'penggilingan', 'superadmin'].includes(user.value?.role))
  const canVerify = computed(() => ['admin', 'superadmin'].includes(user.value?.role))

  const login = async (username, password) => {
    try {
      const response = await api.post('/login', { username, password })
      if (response.data.success) {
        token.value = response.data.data.token
        user.value = response.data.data.user
        localStorage.setItem('token', response.data.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.data.user))
        return { success: true }
      }
      return { success: false, message: response.data.message }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Login gagal'
      }
    }
  }

  const logout = async () => {
    try {
      await api.post('/logout')
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      user.value = null
      token.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    }
  }

  const fetchUser = async () => {
    try {
      const response = await api.get('/me')
      if (response.data.success) {
        user.value = response.data.data
        localStorage.setItem('user', JSON.stringify(response.data.data))
      }
    } catch (error) {
      console.error('Fetch user error:', error)
      user.value = null
      token.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    }
  }

  const initAuth = () => {
    const storedToken = localStorage.getItem('token')
    const storedUser = localStorage.getItem('user')
    if (storedToken && storedUser) {
      token.value = storedToken
      user.value = JSON.parse(storedUser)
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    isSuperAdmin,
    isAdmin,
    isLapangan,
    isPenggilingan,
    namaPenggilingan,
    canAccessPetani,
    canAccessPenggilingan,
    canAccessUsers,
    canManagePetani,
    canManagePenggilingan,
    canVerify,
    login,
    logout,
    fetchUser,
    initAuth
  }
})
