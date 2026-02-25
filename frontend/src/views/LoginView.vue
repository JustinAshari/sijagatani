<template>
  <div class="login-container">
    <!-- Left Side - Branding -->
    <div class="login-left">
      <div class="branding-content">
        <div class="logo-section">
          <img src="../assets/Bulog Logo.png" alt="Bulog Logo" class="logo-bulog">
          <img src="../assets/Sijagatani Logo.PNG" alt="Sijagatani Logo" class="logo-sijagatani">
        </div>
        <h1 class="main-title">Sistem Jemput<br>Gabah Jagung Petani</h1>
      </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="login-right">
      <div class="login-form-container">
        <form @submit.prevent="handleLogin" class="login-form">
          <div class="form-group">
            <label for="username">Username</label>
            <div class="input-wrapper">
              <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              <input
                id="username"
                v-model="form.username"
                type="text"
                placeholder="username"
                required
                autocomplete="username"
              />
            </div>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrapper">
              <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••••••••"
                required
                autocomplete="current-password"
              />
              <button 
                type="button" 
                class="toggle-password" 
                @click="showPassword = !showPassword"
                tabindex="-1"
              >
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                  <line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
              </button>
            </div>
          </div>

          <div v-if="error" class="error-message">
            {{ error }}
          </div>

          <button type="submit" class="btn-login" :disabled="loading">
            {{ loading ? 'Loading...' : 'Masuk' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  username: '',
  password: ''
})

const loading = ref(false)
const error = ref('')
const showPassword = ref(false)

const handleLogin = async () => {
  loading.value = true
  error.value = ''

  try {
    const result = await authStore.login(form.value.username, form.value.password)
    
    if (result.success) {
      router.push('/')
    } else {
      error.value = result.message || 'Login gagal'
    }
  } catch (err) {
    error.value = 'Terjadi kesalahan. Silakan coba lagi.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  background: #f5f5f5;
}

/* Left Side - Branding */
.login-left {
  flex: 1;
  background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
  padding: 60px 120px;
  display: flex;
  flex-direction: column;
}

.branding-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 30px;
  margin-bottom: 30px;
}

.logo-bulog {
  height: 60px;
  width: auto;
  object-fit: contain;
}

.logo-sijagatani {
  height: 120px;
  width: auto;
  object-fit: contain;
}

.main-title {
  font-size: 48px;
  font-weight: 700;
  color: #1a1a1a;
  line-height: 1.2;
  text-align: left;
}

/* Right Side - Login Form */
.login-right {
  flex: 1;
  background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
}

.login-form-container {
  width: 100%;
  max-width: 400px;
}

.login-form {
  width: 100%;
}

.form-group {
  margin-bottom: 24px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #1a1a1a;
  font-weight: 500;
  font-size: 14px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 16px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
  pointer-events: none;
}

.toggle-password {
  position: absolute;
  right: 16px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  transition: color 0.2s ease;
}

.toggle-password:hover {
  color: #6b7280;
}

.toggle-password svg {
  width: 20px;
  height: 20px;
}

.input-wrapper input {
  width: 100%;
  padding: 14px 16px 14px 48px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 15px;
  transition: all 0.2s ease;
  background: #f9fafb;
}

.input-wrapper input:focus {
  outline: none;
  border-color: #3b82f6;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.input-wrapper input::placeholder {
  color: #9ca3af;
}

.error-message {
  background-color: #fee;
  border: 1px solid #fcc;
  color: #c33;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 20px;
  font-size: 14px;
}

.btn-login {
  width: 100%;
  padding: 16px;
  background: #3b82f6;
  color: #ffffff;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-top: 8px;
}

.btn-login:hover:not(:disabled) {
  background: #2563eb;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.btn-login:active:not(:disabled) {
  transform: translateY(0);
}

.btn-login:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 1024px) {
  .login-container {
    flex-direction: column;
  }

  .login-left {
    padding: 30px;
    min-height: 40vh;
  }

  .logo-section {
    margin-bottom: 30px;
  }

  .logo-bulog,
  .logo-sijagatani {
    height: 45px;
  }

  .main-title {
    font-size: 36px;
    margin-bottom: 30px;
  }

  .login-right {
    min-height: 60vh;
  }
}

@media (max-width: 640px) {
  .login-left {
    padding: 20px;
  }

  .logo-section {
    gap: 15px;
    margin-bottom: 20px;
  }

  .logo-bulog,
  .logo-sijagatani {
    height: 35px;
  }

  .main-title {
    font-size: 28px;
  }

  .login-right {
    padding: 20px;
  }

  .login-form-container {
    max-width: 100%;
  }
}
</style>
