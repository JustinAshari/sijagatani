<script setup>
import { RouterView, useRoute } from 'vue-router'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import AppSidebar from './components/AppSidebar.vue'
import { useAuthStore } from './stores/auth'
import sijagataniLogo from './assets/Sijagatani Logo.PNG'

const route = useRoute()
const authStore = useAuthStore()

// On desktop sidebar always open; on mobile starts closed
const isMobile = ref(window.innerWidth <= 768)
const sidebarOpen = ref(!isMobile.value)

const onResize = () => {
  isMobile.value = window.innerWidth <= 768
  if (!isMobile.value) sidebarOpen.value = true
  else sidebarOpen.value = false
}
onMounted(() => window.addEventListener('resize', onResize))
onUnmounted(() => window.removeEventListener('resize', onResize))

const showLayout = computed(() => route.name !== 'login')

const handleLogout = async () => {
  if (confirm('Apakah Anda yakin ingin logout?')) {
    await authStore.logout()
    window.location.href = '/login'
  }
}

const userInitials = computed(() => {
  const name = authStore.user?.name || authStore.user?.username || '?'
  return name.split(' ').slice(0, 2).map(w => w[0]?.toUpperCase()).join('')
})

const roleLabel = computed(() => {
  const roles = { superadmin: 'Super Admin', admin: 'Admin', lapangan: 'Petugas Lapangan', penggilingan: 'Penggilingan' }
  return roles[authStore.user?.role] || authStore.user?.role || ''
})
</script>

<template>
  <div v-if="!showLayout">
    <RouterView />
  </div>
  <div v-else class="app-container">

    <!-- Sidebar -->
    <AppSidebar :open="sidebarOpen" @close="sidebarOpen = false" />

    <!-- Mobile Backdrop -->
    <div v-if="sidebarOpen && isMobile" class="mobile-backdrop" @click="sidebarOpen = false"></div>

    <!-- Right column: navbar + content -->
    <div class="app-right" :class="{ 'sidebar-open': sidebarOpen && !isMobile }">

      <!-- Top Navbar -->
      <header class="top-navbar">
        <div class="navbar-content">
          <div class="navbar-left">
            <!-- Hamburger: mobile only -->
            <button
              class="btn-hamburger"
              @click="sidebarOpen = !sidebarOpen"
              :title="sidebarOpen ? 'Tutup Sidebar' : 'Buka Sidebar'"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
              </svg>
            </button>
            <img :src="sijagataniLogo" alt="Sijagatani Logo" class="sijagatani-logo">
            <h1 class="app-title">Sistem Jemput<br>Gabah Jagung Petani</h1>
          </div>

          <div class="navbar-right">
            <!-- User Info -->
            <div class="user-info" v-if="authStore.user">
              <div class="user-avatar">{{ userInitials }}</div>
              <div class="user-details">
                <span class="user-name">{{ authStore.user?.name || authStore.user?.username }}</span>
                <span class="user-role">{{ roleLabel }}</span>
              </div>
            </div>

            <!-- Logout -->
            <button @click="handleLogout" class="btn-logout">
              <svg class="logout-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
              </svg>
              <span class="logout-text">Logout</span>
            </button>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="app-main">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  background: #f0f3f8;
  color: #1a2332;
}

.app-container {
  min-height: 100vh;
  display: flex;
}

/* Right column that sits beside sidebar */
.app-right {
  flex: 1;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  margin-left: 0;
  transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.app-right.sidebar-open {
  margin-left: 240px;
}

/* Top Navbar */
.top-navbar {
  position: sticky;
  top: 0;
  height: 80px;
  background: #ffffff;
  border-bottom: 1px solid #e8ecf0;
  z-index: 900;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.navbar-content {
  height: 100%;
  padding: 0 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.navbar-left {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

/* Hamburger  hidden on desktop, shown on mobile */
.btn-hamburger {
  display: none;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  background: transparent;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  color: #4b5563;
  transition: background 0.15s;
  flex-shrink: 0;
}

.btn-hamburger:hover {
  background: #f3f6fb;
}

.sijagatani-logo {
  height: 56px;
  width: auto;
  object-fit: contain;
}

.app-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: #1a2332;
  margin: 0;
  line-height: 1.3;
}

.navbar-right {
  display: flex;
  align-items: center;
  gap: 1.25rem;
}

/* User info */
.user-info {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.45rem 0.85rem;
  background: #f3f6fb;
  border-radius: 10px;
  border: 1px solid #e8ecf0;
}

.user-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: #ffffff;
  font-size: 0.875rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  letter-spacing: 0.5px;
}

.user-details {
  display: flex;
  flex-direction: column;
  gap: 1px;
  line-height: 1;
}

.user-name {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1a2332;
  white-space: nowrap;
}

.user-role {
  font-size: 0.72rem;
  font-weight: 500;
  color: #6b7280;
  white-space: nowrap;
}

/* Logout button */
.btn-logout {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 10px 18px;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 9px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.logout-icon {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
}

.btn-logout:hover {
  background: #dc2626;
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25);
}

/* Main */
.app-main {
  flex: 1;
  padding: 1.5rem;
  width: 100%;
}

/* Mobile backdrop */
.mobile-backdrop {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  z-index: 999;
}

@media (max-width: 768px) {
  .btn-hamburger {
    display: flex;
  }

  .app-title {
    display: none;
  }

  .user-details {
    display: none;
  }

  .user-info {
    padding: 0.45rem;
  }

  .logout-text {
    display: none;
  }

  .btn-logout {
    padding: 9px 11px;
  }

  .app-right.sidebar-open {
    margin-left: 0;
  }

  .mobile-backdrop {
    display: block;
  }

  .sijagatani-logo {
    height: 42px;
  }

  .app-main {
    padding: 0.8rem;
  }
}

@media (max-width: 480px) {
  .sijagatani-logo {
    height: 30px;
  }

  .app-main {
    padding: 0.6rem;
  }
}
</style>